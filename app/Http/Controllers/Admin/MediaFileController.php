<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Media\AssembleMediaUpload;
use App\Actions\Media\DeleteMediaFile;
use App\Actions\Media\MoveMediaFile;
use App\Actions\Media\RenameMediaFile;
use App\Actions\Media\StoreMediaChunk;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Media\StoreMediaFileRequest;
use App\Http\Requests\Admin\Media\UpdateMediaFileRequest;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class MediaFileController extends Controller
{
    public function store(StoreMediaFileRequest $request, StoreMediaChunk $chunkStore, AssembleMediaUpload $assemble): JsonResponse
    {
        $identifier = $request->string('resumableIdentifier')->toString();
        $chunkNumber = $request->integer('resumableChunkNumber');
        $totalChunks = $request->integer('resumableTotalChunks');

        $result = $chunkStore->handle(
            identifier: $identifier,
            chunkNumber: $chunkNumber,
            totalChunks: $totalChunks,
            chunk: $request->file('file'),
        );

        if (! $result['complete']) {
            return response()->json($result);
        }

        $file = $assemble->handle(
            user: $request->user(),
            identifier: $identifier,
            originalName: $request->string('resumableFilename')->toString(),
            mimeType: $request->string('resumableType')->toString(),
            folderId: $request->integer('folder_id') ?: null,
        );

        return response()->json([
            'received' => $chunkNumber,
            'total' => $totalChunks,
            'complete' => true,
            'file' => [
                'id' => $file->id,
                'name' => $file->name,
                'url' => $file->url,
                'thumb_url' => $file->thumb_url,
                'mime_type' => $file->mime_type,
                'size' => $file->size,
            ],
        ]);
    }

    public function check(Request $request, StoreMediaChunk $chunkStore): Response
    {
        $identifier = (string) $request->query('resumableIdentifier', '');
        $chunkNumber = (int) $request->query('resumableChunkNumber', 0);

        if ($identifier === '' || $chunkNumber < 1) {
            return response()->noContent(404);
        }

        return $chunkStore->chunkExists($identifier, $chunkNumber)
            ? response()->noContent(200)
            : response()->noContent(204);
    }

    public function update(UpdateMediaFileRequest $request, MediaFile $file, RenameMediaFile $rename, MoveMediaFile $move): RedirectResponse
    {
        if ($request->filled('name') && $request->string('name')->toString() !== $file->name) {
            $file = $rename->handle($file, $request->string('name')->toString());
        }

        if ($request->has('folder_id')) {
            $newFolderId = $request->integer('folder_id') ?: null;
            if ($newFolderId !== $file->folder_id) {
                $newFolder = $newFolderId ? MediaFolder::query()->findOrFail($newFolderId) : null;
                $file = $move->handle($file, $newFolder);
            }
        }

        return back()->with('status', 'File updated.');
    }

    public function destroy(Request $request, MediaFile $file, DeleteMediaFile $action): RedirectResponse
    {
        $request->user()->can('delete', $file) ?: abort(403);

        $action->handle($file);

        return back()->with('status', 'File moved to trash.');
    }

    public function bulkDestroy(Request $request, DeleteMediaFile $action): RedirectResponse
    {
        $request->user()->can('create', MediaFile::class) ?: abort(403);

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        MediaFile::query()->whereIn('id', $data['ids'])->get()->each(
            fn (MediaFile $file) => $action->handle($file),
        );

        return back()->with('status', count($data['ids']).' file(s) moved to trash.');
    }

    public function bulkMove(Request $request, MoveMediaFile $action): RedirectResponse
    {
        $request->user()->can('create', MediaFile::class) ?: abort(403);

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
            'folder_id' => ['nullable', 'integer', Rule::exists('media_folders', 'id')->whereNull('deleted_at')],
        ]);

        $folder = isset($data['folder_id']) ? MediaFolder::query()->find($data['folder_id']) : null;

        MediaFile::query()->whereIn('id', $data['ids'])->get()->each(
            fn (MediaFile $file) => $action->handle($file, $folder),
        );

        return back()->with('status', count($data['ids']).' file(s) moved.');
    }

    public function download(Request $request, MediaFile $file): BinaryFileResponse
    {
        $request->user()->can('view', $file) ?: abort(403);

        $disk = Storage::disk($file->disk);

        abort_unless($disk->exists($file->path), 404);

        return response()->download($disk->path($file->path), $file->original_name);
    }
}
