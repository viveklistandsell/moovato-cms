<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Media\CreateMediaFolder;
use App\Actions\Media\DeleteMediaFile;
use App\Actions\Media\DeleteMediaFolder;
use App\Actions\Media\MoveMediaFile;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class MediaPickerController
{
    /**
     * Image-only paginated listing for the in-form picker modal.
     * Filters by ?q=search, ?folder=id, ?sort=newest|oldest|name|name_desc.
     */
    public function index(Request $request): JsonResponse
    {
        $search = mb_trim((string) $request->query('q', ''));
        $folderId = $request->integer('folder') ?: null;
        $sort = (string) $request->query('sort', 'newest');
        $allowedSorts = ['newest', 'oldest', 'name', 'name_desc'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'newest';
        }

        $query = MediaFile::query()
            ->where('mime_type', 'like', 'image/%');

        match ($sort) {
            'oldest' => $query->orderBy('id'),
            'name' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            default => $query->orderByDesc('id'),
        };

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('original_name', 'like', "%{$search}%");
            });
        } else {
            // Always scope to the current folder when not searching — at root
            // that's WHERE folder_id IS NULL (mirrors the main /admin/media
            // controller's behavior). Without this, the picker incorrectly
            // shows every image regardless of folder, so uploads in subfolders
            // look like they leaked out to root.
            $query->where('folder_id', $folderId);
        }

        $paginator = $query->with('user:id,name')->paginate(48);

        // Build relative URLs (avoids APP_URL host mismatch in dev where the
        // browser hits 127.0.0.1:8000 but APP_URL is http://localhost).
        $files = collect($paginator->items())
            ->map(fn (MediaFile $file): array => $this->presentFile($file))
            ->all();

        $folderRecords = $search === ''
            ? MediaFolder::query()
                ->where('parent_id', $folderId)
                ->orderBy('name')
                ->get(['id', 'name', 'parent_id'])
            : collect();

        $folderIds = $folderRecords->pluck('id')->all();

        $thumbnails = $folderIds === []
            ? collect()
            : MediaFile::query()
                ->whereIn('folder_id', $folderIds)
                ->where('mime_type', 'like', 'image/%')
                ->whereNotNull('thumb_path')
                ->orderByDesc('id')
                ->get(['id', 'folder_id', 'disk', 'thumb_path'])
                ->groupBy('folder_id')
                ->map(fn ($group) => $group->first());

        $fileCounts = $folderIds === []
            ? collect()
            : MediaFile::query()
                ->whereIn('folder_id', $folderIds)
                ->selectRaw('folder_id, COUNT(*) as total')
                ->groupBy('folder_id')
                ->pluck('total', 'folder_id');

        $childCounts = $folderIds === []
            ? collect()
            : MediaFolder::query()
                ->whereIn('parent_id', $folderIds)
                ->selectRaw('parent_id, COUNT(*) as total')
                ->groupBy('parent_id')
                ->pluck('total', 'parent_id');

        $folders = $folderRecords->map(function (MediaFolder $f) use ($thumbnails, $fileCounts, $childCounts): array {
            $cover = $thumbnails->get($f->id);

            return [
                'id' => $f->id,
                'name' => $f->name,
                'parent_id' => $f->parent_id,
                'thumb_url' => $cover?->thumb_url,
                'file_count' => (int) ($fileCounts[$f->id] ?? 0),
                'subfolder_count' => (int) ($childCounts[$f->id] ?? 0),
            ];
        })->all();

        $totalFilesInFolder = $search === ''
            ? (int) MediaFile::query()
                ->where('folder_id', $folderId)
                ->where('mime_type', 'like', 'image/%')
                ->count()
            : $paginator->total();

        return response()->json([
            'files' => $files,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'has_more' => $paginator->hasMorePages(),
            ],
            'folders' => $folders,
            'stats' => [
                'file_count' => $totalFilesInFolder,
                'folder_count' => count($folders),
            ],
            'breadcrumb' => $folderId !== null
                ? $this->folderTrail($folderId)
                : [],
            'parent_folder_id' => $folderId !== null
                ? MediaFolder::query()->find($folderId)?->parent_id
                : null,
        ]);
    }

    /**
     * Simple direct image upload from the picker modal — bypasses chunked
     * upload because we expect single small images (≤10 MB).
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'max:10240'],
            'folder_id' => ['nullable', 'integer', 'exists:media_folders,id'],
        ]);

        $upload = $request->file('file');
        $folderId = $request->integer('folder_id') ?: null;
        $disk = 'public';

        $extension = $upload->getClientOriginalExtension() ?: $upload->extension();
        $name = pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME);
        $stored = Str::random(32).($extension !== '' ? '.'.$extension : '');
        $path = $upload->storeAs('media', $stored, $disk);

        $file = MediaFile::query()->create([
            'folder_id' => $folderId,
            'user_id' => $request->user()?->id,
            'name' => $name,
            'original_name' => $upload->getClientOriginalName(),
            'mime_type' => $upload->getMimeType() ?? 'application/octet-stream',
            'extension' => $extension,
            'size' => $upload->getSize() ?? 0,
            'disk' => $disk,
            'path' => $path,
            'thumb_path' => null,
            'medium_path' => null,
            'metadata' => null,
        ]);

        $file->load('user:id,name');

        return response()->json([
            'file' => $this->presentFile($file),
        ]);
    }

    /**
     * Update editable metadata (alt_text, title, caption, description, name).
     * Persists structured fields into the existing `metadata` JSON column so
     * we don't need a migration.
     */
    public function updateFile(Request $request, MediaFile $file): JsonResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:500'],
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $metadata = is_array($file->metadata) ? $file->metadata : [];
        foreach (['alt_text', 'title', 'caption', 'description'] as $key) {
            if (array_key_exists($key, $data)) {
                $metadata[$key] = $data[$key];
            }
        }

        $file->fill([
            'name' => $data['name'] ?? $file->name,
            'metadata' => $metadata,
        ]);
        $file->save();
        $file->load('user:id,name');

        return response()->json(['file' => $this->presentFile($file)]);
    }

    /**
     * Move one or more files into a folder (or to root when folder_id is
     * null). Powers the drag-and-drop reorganization in the picker.
     */
    public function moveFiles(Request $request, MoveMediaFile $action): JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:media_files,id'],
            'folder_id' => ['nullable', 'integer', 'exists:media_folders,id'],
        ]);

        $folder = isset($data['folder_id'])
            ? MediaFolder::query()->find($data['folder_id'])
            : null;

        MediaFile::query()->whereIn('id', $data['ids'])->get()->each(
            fn (MediaFile $file) => $action->handle($file, $folder),
        );

        return response()->json([
            'moved' => count($data['ids']),
            'folder_id' => $data['folder_id'] ?? null,
        ]);
    }

    /**
     * Soft-delete a file (moves to trash). Use forceDestroy() to remove
     * permanently.
     */
    public function destroyFile(MediaFile $file, DeleteMediaFile $action): JsonResponse
    {
        $action->handle($file);

        return response()->json(['deleted' => true]);
    }

    /**
     * Permanently remove a file from disk + DB (skips trash).
     */
    public function forceDestroyFile(MediaFile $file): JsonResponse
    {
        if ($file->path !== null && $file->path !== '') {
            Storage::disk($file->disk)->delete($file->path);
            if ($file->thumb_path) {
                Storage::disk($file->disk)->delete($file->thumb_path);
            }
            if ($file->medium_path) {
                Storage::disk($file->disk)->delete($file->medium_path);
            }
        }
        $file->forceDelete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Soft-delete a folder (moves it and its descendants to trash). Returns
     * JSON for the picker; existing /admin/media folder delete endpoint
     * returns an Inertia redirect which doesn't fit the modal flow.
     */
    public function destroyFolder(MediaFolder $folder, DeleteMediaFolder $action): JsonResponse
    {
        $action->handle($folder);

        return response()->json(['deleted' => true]);
    }

    /**
     * Create a folder via JSON. Delegates to CreateMediaFolder so the
     * `path` column is computed correctly from the parent chain (writing
     * to media_folders directly would violate the not-null `path` field).
     */
    public function createFolder(Request $request, CreateMediaFolder $action): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:media_folders,id'],
        ]);

        $folder = $action->handle($request->user(), [
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        return response()->json([
            'folder' => [
                'id' => $folder->id,
                'name' => $folder->name,
                'parent_id' => $folder->parent_id,
                'thumb_url' => null,
                'file_count' => 0,
                'subfolder_count' => 0,
            ],
        ], 201);
    }

    /**
     * Shape a MediaFile for the picker UI. Includes editable metadata
     * fields lifted out of the JSON column so the frontend can bind them.
     *
     * @return array<string, mixed>
     */
    private function presentFile(MediaFile $file): array
    {
        $metadata = is_array($file->metadata) ? $file->metadata : [];

        return [
            'id' => $file->id,
            'name' => $file->name,
            'original_name' => $file->original_name,
            'path' => $file->path,
            'url' => $this->relativeUrl($file->path),
            'thumb_url' => $file->thumb_path
                ? $this->relativeUrl($file->thumb_path)
                : $this->relativeUrl($file->path),
            'medium_url' => $file->medium_path
                ? $this->relativeUrl($file->medium_path)
                : null,
            'mime_type' => $file->mime_type,
            'extension' => $file->extension,
            'size' => $file->size,
            'folder_id' => $file->folder_id,
            'uploader' => $file->user?->name,
            'created_at' => $file->created_at?->toIso8601String(),
            'updated_at' => $file->updated_at?->toIso8601String(),
            'alt_text' => $metadata['alt_text'] ?? null,
            'title' => $metadata['title'] ?? null,
            'caption' => $metadata['caption'] ?? null,
            'description' => $metadata['description'] ?? null,
        ];
    }

    /**
     * Build a relative `/storage/<path>` URL. Avoids absolute APP_URL
     * prefixing — works regardless of whether the dev server is on
     * localhost vs 127.0.0.1.
     */
    private function relativeUrl(string $path): string
    {
        return '/storage/'.mb_ltrim($path, '/');
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function folderTrail(int $folderId): array
    {
        $trail = [];
        $folder = MediaFolder::query()->find($folderId);
        while ($folder !== null) {
            array_unshift($trail, ['id' => $folder->id, 'name' => $folder->name]);
            $folder = $folder->parent_id !== null
                ? MediaFolder::query()->find($folder->parent_id)
                : null;
        }

        return $trail;
    }
}
