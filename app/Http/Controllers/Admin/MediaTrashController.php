<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Media\ForceDeleteMediaFile;
use App\Actions\Media\ForceDeleteMediaFolder;
use App\Actions\Media\RestoreMediaFile;
use App\Actions\Media\RestoreMediaFolder;
use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class MediaTrashController extends Controller
{
    public function index(Request $request): Response
    {
        $request->user()->can('viewAny', MediaFolder::class) ?: abort(403);

        $folders = MediaFolder::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->get(['id', 'name', 'path', 'deleted_at']);

        $files = MediaFile::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate(60)
            ->through(fn (MediaFile $file): array => [
                'id' => $file->id,
                'name' => $file->name,
                'mime_type' => $file->mime_type,
                'size' => $file->size,
                'thumb_url' => $file->thumb_url,
                'deleted_at' => $file->deleted_at?->toIso8601String(),
            ]);

        return Inertia::render('Media/Trash', [
            'folders' => $folders,
            'files' => $files,
        ]);
    }

    public function restoreFolder(Request $request, int $id, RestoreMediaFolder $action): RedirectResponse
    {
        $folder = MediaFolder::onlyTrashed()->findOrFail($id);

        $request->user()->can('restore', $folder) ?: abort(403);

        $action->handle($folder);

        return back()->with('status', 'Folder restored.');
    }

    public function restoreFile(Request $request, int $id, RestoreMediaFile $action): RedirectResponse
    {
        $file = MediaFile::onlyTrashed()->findOrFail($id);

        $request->user()->can('restore', $file) ?: abort(403);

        $action->handle($file);

        return back()->with('status', 'File restored.');
    }

    public function forceDeleteFolder(Request $request, int $id, ForceDeleteMediaFolder $action): RedirectResponse
    {
        $folder = MediaFolder::onlyTrashed()->findOrFail($id);

        $request->user()->can('forceDelete', $folder) ?: abort(403);

        $action->handle($folder);

        return back()->with('status', 'Folder permanently deleted.');
    }

    public function forceDeleteFile(Request $request, int $id, ForceDeleteMediaFile $action): RedirectResponse
    {
        $file = MediaFile::onlyTrashed()->findOrFail($id);

        $request->user()->can('forceDelete', $file) ?: abort(403);

        $action->handle($file);

        return back()->with('status', 'File permanently deleted.');
    }
}
