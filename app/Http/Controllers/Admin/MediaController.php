<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class MediaController extends Controller
{
    public function index(Request $request): Response
    {
        $folderId = $request->integer('folder') ?: null;
        $search = mb_trim((string) $request->query('q', ''));
        $isSearching = $search !== '';
        $sort = (string) $request->query('sort', 'newest');
        $allowedSorts = ['newest', 'oldest', 'name', 'name_desc', 'largest', 'smallest'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'newest';
        }

        $folder = $folderId
            ? MediaFolder::query()->with('parent')->findOrFail($folderId)
            : null;

        $folders = $isSearching
            ? collect([])
            : MediaFolder::query()
                ->where('parent_id', $folderId)
                ->orderBy('name')
                ->get(['id', 'name', 'parent_id', 'path']);

        $folderIds = $folders->pluck('id')->all();
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

        $folders = $folders->map(function (MediaFolder $folder) use ($thumbnails, $fileCounts, $childCounts): array {
            $cover = $thumbnails->get($folder->id);

            return [
                'id' => $folder->id,
                'name' => $folder->name,
                'parent_id' => $folder->parent_id,
                'path' => $folder->path,
                'thumb_url' => $cover?->thumb_url,
                'file_count' => (int) ($fileCounts[$folder->id] ?? 0),
                'subfolder_count' => (int) ($childCounts[$folder->id] ?? 0),
            ];
        });

        $filesQuery = MediaFile::query();

        match ($sort) {
            'oldest' => $filesQuery->orderBy('id'),
            'name' => $filesQuery->orderBy('name'),
            'name_desc' => $filesQuery->orderByDesc('name'),
            'largest' => $filesQuery->orderByDesc('size'),
            'smallest' => $filesQuery->orderBy('size'),
            default => $filesQuery->orderByDesc('id'),
        };

        if ($isSearching) {
            $filesQuery->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('original_name', 'like', '%'.$search.'%');
            });
        } else {
            $filesQuery->where('folder_id', $folderId);
        }

        $totalSize = $isSearching
            ? (int) (clone $filesQuery)->sum('size')
            : (int) MediaFile::query()->where('folder_id', $folderId)->sum('size');

        $files = $filesQuery
            ->paginate(60)
            ->withQueryString()
            ->through(fn (MediaFile $file): array => [
                'id' => $file->id,
                'name' => $file->name,
                'original_name' => $file->original_name,
                'mime_type' => $file->mime_type,
                'extension' => $file->extension,
                'size' => $file->size,
                'url' => $file->url,
                'thumb_url' => $file->thumb_url,
                'medium_url' => $file->medium_url,
                'metadata' => $file->metadata,
                'folder_id' => $file->folder_id,
                'path' => $file->path,
                'created_at' => $file->created_at?->toIso8601String(),
            ]);

        return Inertia::render('Media/Index', [
            'currentFolder' => $folder
                ? [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'path' => $folder->path,
                    'parent_id' => $folder->parent_id,
                ]
                : null,
            'breadcrumbs' => $this->breadcrumbs($folder),
            'folders' => $folders,
            'files' => $files,
            'tree' => $this->folderTree(),
            'search' => $search,
            'sort' => $sort,
            'totalSize' => $totalSize,
        ]);
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function breadcrumbs(?MediaFolder $folder): array
    {
        $segments = [];
        $cursor = $folder;

        while ($cursor !== null) {
            array_unshift($segments, ['id' => $cursor->id, 'name' => $cursor->name]);
            $cursor = $cursor->parent;
        }

        return $segments;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function folderTree(): array
    {
        $all = MediaFolder::query()
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name'])
            ->groupBy('parent_id');

        $build = function (?int $parentId) use (&$build, $all): array {
            return ($all[$parentId] ?? collect())
                ->map(fn ($folder): array => [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'children' => $build($folder->id),
                ])
                ->all();
        };

        return $build(null);
    }
}
