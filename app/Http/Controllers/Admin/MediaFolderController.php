<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Media\CreateMediaFolder;
use App\Actions\Media\DeleteMediaFolder;
use App\Actions\Media\MoveMediaFolder;
use App\Actions\Media\RenameMediaFolder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Media\StoreMediaFolderRequest;
use App\Http\Requests\Admin\Media\UpdateMediaFolderRequest;
use App\Models\MediaFolder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class MediaFolderController extends Controller
{
    public function store(StoreMediaFolderRequest $request, CreateMediaFolder $action): RedirectResponse
    {
        $folder = $action->handle($request->user(), [
            'name' => $request->string('name')->toString(),
            'parent_id' => $request->integer('parent_id') ?: null,
        ]);

        return back()->with('status', 'Folder "'.$folder->name.'" created.');
    }

    public function update(
        UpdateMediaFolderRequest $request,
        MediaFolder $folder,
        RenameMediaFolder $rename,
        MoveMediaFolder $move,
    ): RedirectResponse {
        if ($request->filled('name') && $request->string('name')->toString() !== $folder->name) {
            $folder = $rename->handle($folder, $request->string('name')->toString());
        }

        if ($request->has('parent_id')) {
            $newParentId = $request->integer('parent_id') ?: null;
            if ($newParentId !== $folder->parent_id) {
                $newParent = $newParentId ? MediaFolder::query()->findOrFail($newParentId) : null;
                $folder = $move->handle($folder, $newParent);
            }
        }

        return back()->with('status', 'Folder updated.');
    }

    public function destroy(Request $request, MediaFolder $folder, DeleteMediaFolder $action): RedirectResponse
    {
        $request->user()->can('delete', $folder) ?: abort(403);

        $action->handle($folder);

        return back()->with('status', 'Folder moved to trash.');
    }

    public function bulkDestroy(Request $request, DeleteMediaFolder $action): RedirectResponse
    {
        $request->user()->can('create', MediaFolder::class) ?: abort(403);

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        MediaFolder::query()->whereIn('id', $data['ids'])->get()->each(
            fn (MediaFolder $folder) => $action->handle($folder),
        );

        return back()->with('status', count($data['ids']).' folder(s) moved to trash.');
    }
}
