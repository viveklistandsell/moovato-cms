<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFile;
use App\Models\MediaFolder;

final readonly class MoveMediaFile
{
    public function handle(MediaFile $file, ?MediaFolder $newFolder): MediaFile
    {
        $file->folder_id = $newFolder?->id;
        $file->save();

        return $file->refresh();
    }
}
