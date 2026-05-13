<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFolder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class MoveMediaFolder
{
    public function __construct(private RecalculateFolderPaths $recalculate) {}

    public function handle(MediaFolder $folder, ?MediaFolder $newParent): MediaFolder
    {
        $this->guardAgainstCycle($folder, $newParent);

        return DB::transaction(function () use ($folder, $newParent): MediaFolder {
            $folder->parent_id = $newParent?->id;
            $folder->path = $newParent
                ? $newParent->path.'/'.$folder->name
                : $folder->name;
            $folder->save();

            $this->recalculate->handle($folder);

            return $folder->refresh();
        });
    }

    private function guardAgainstCycle(MediaFolder $folder, ?MediaFolder $newParent): void
    {
        if ($newParent === null) {
            return;
        }

        if ($newParent->id === $folder->id) {
            throw new InvalidArgumentException('A folder cannot be moved into itself.');
        }

        $cursor = $newParent;

        while ($cursor !== null) {
            if ($cursor->id === $folder->id) {
                throw new InvalidArgumentException('A folder cannot be moved into one of its descendants.');
            }
            $cursor = $cursor->parent;
        }
    }
}
