<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFolder;
use Illuminate\Support\Facades\DB;

final readonly class RenameMediaFolder
{
    public function __construct(private RecalculateFolderPaths $recalculate) {}

    public function handle(MediaFolder $folder, string $name): MediaFolder
    {
        return DB::transaction(function () use ($folder, $name): MediaFolder {
            $folder->name = $name;
            $folder->path = $folder->parent
                ? $folder->parent->path.'/'.$name
                : $name;
            $folder->save();

            $this->recalculate->handle($folder);

            return $folder->refresh();
        });
    }
}
