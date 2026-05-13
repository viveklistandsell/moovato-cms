<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

final readonly class GenerateMediaThumbnails
{
    public function handle(MediaFile $file): void
    {
        if (! $file->isImage()) {
            return;
        }

        $disk = Storage::disk($file->disk);
        $absolute = $disk->path($file->path);

        if (! is_file($absolute)) {
            return;
        }

        $manager = new ImageManager(new Driver);
        $image = $manager->decodePath($absolute);

        $width = $image->width();
        $height = $image->height();

        $thumbPath = $this->variantPath($file->path, 'thumb');
        $mediumPath = $this->variantPath($file->path, 'medium');

        $manager->decodePath($absolute)
            ->scaleDown(width: 300, height: 300)
            ->save($disk->path($thumbPath));

        $manager->decodePath($absolute)
            ->scaleDown(width: 1200, height: 1200)
            ->save($disk->path($mediumPath));

        $file->update([
            'thumb_path' => $thumbPath,
            'medium_path' => $mediumPath,
            'metadata' => array_merge($file->metadata ?? [], [
                'width' => $width,
                'height' => $height,
            ]),
        ]);
    }

    private function variantPath(string $path, string $suffix): string
    {
        $info = pathinfo($path);
        $dir = $info['dirname'] ?? '';
        $name = $info['filename'];
        $ext = isset($info['extension']) ? '.'.$info['extension'] : '';

        return ($dir !== '' && $dir !== '.' ? $dir.'/' : '').$name.'_'.$suffix.$ext;
    }
}
