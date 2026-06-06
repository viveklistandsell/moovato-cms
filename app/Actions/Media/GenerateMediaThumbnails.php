<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

final readonly class GenerateMediaThumbnails
{
    private const DECODABLE_MIMES = [
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/bmp',
        'image/x-bmp',
        'image/x-ms-bmp',
        'image/avif',
    ];

    public function handle(MediaFile $file): void
    {
        if (! $this->isThumbnailable($file)) {
            return;
        }

        $disk = Storage::disk($file->disk);
        $absolute = $disk->path($file->path);

        if (! is_file($absolute)) {
            return;
        }

        // Wrap the decode in a try/catch so a corrupt-but-claims-to-be-JPEG
        // (or any future MIME edge case) doesn't roll back the whole upload
        // transaction. The file is already on disk; missing a thumb is a
        // graceful degradation, not an error worth surfacing to the admin.
        try {
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
        } catch (Throwable $e) {
            Log::warning('Thumbnail generation skipped', [
                'media_file_id' => $file->id,
                'mime' => $file->mime_type,
                'path' => $file->path,
                'reason' => $e->getMessage(),
            ]);
        }
    }

    private function isThumbnailable(MediaFile $file): bool
    {
        if (! $file->isImage()) {
            return false;
        }

        return in_array(mb_strtolower((string) $file->mime_type), self::DECODABLE_MIMES, true);
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
