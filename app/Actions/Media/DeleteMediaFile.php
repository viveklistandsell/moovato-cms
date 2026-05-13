<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFile;

final readonly class DeleteMediaFile
{
    public function handle(MediaFile $file): void
    {
        $file->delete();
    }
}
