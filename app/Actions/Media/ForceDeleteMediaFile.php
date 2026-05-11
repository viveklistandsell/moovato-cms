<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFile;
use Illuminate\Support\Facades\Storage;

final readonly class ForceDeleteMediaFile
{
    public function handle(MediaFile $file): void
    {
        $disk = Storage::disk($file->disk);

        foreach (array_filter([$file->path, $file->thumb_path, $file->medium_path]) as $path) {
            $disk->delete($path);
        }

        $file->forceDelete();
    }
}
