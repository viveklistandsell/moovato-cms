<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFolder;

final readonly class RecalculateFolderPaths
{
    public function handle(MediaFolder $folder): void
    {
        foreach ($folder->children as $child) {
            $child->path = $folder->path.'/'.$child->name;
            $child->save();

            $this->handle($child);
        }
    }
}
