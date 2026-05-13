<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFile;

final readonly class RenameMediaFile
{
    public function handle(MediaFile $file, string $name): MediaFile
    {
        $file->name = $name;
        $file->save();

        return $file->refresh();
    }
}
