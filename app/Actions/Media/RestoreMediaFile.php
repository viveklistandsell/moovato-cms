<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFile;

final readonly class RestoreMediaFile
{
    public function handle(MediaFile $file): MediaFile
    {
        $file->restore();

        return $file->refresh();
    }
}
