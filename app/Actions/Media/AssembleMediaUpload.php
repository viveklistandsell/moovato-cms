<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

final readonly class AssembleMediaUpload
{
    public function __construct(
        private StoreMediaChunk $chunkStore,
        private GenerateMediaThumbnails $thumbnails,
    ) {}

    public function handle(
        User $user,
        string $identifier,
        string $originalName,
        string $mimeType,
        ?int $folderId,
    ): MediaFile {
        $directory = $this->chunkStore->chunkDirectory($identifier);
        $chunks = glob($directory.'/*') ?: [];

        if ($chunks === []) {
            throw new RuntimeException('No chunks found for upload.');
        }

        sort($chunks);

        $extension = mb_strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $relativeDir = 'media/'.date('Y/m');
        $relativePath = $relativeDir.'/'.(string) Str::ulid().($extension !== '' ? '.'.$extension : '');

        $disk = Storage::disk('public');
        $disk->makeDirectory($relativeDir);

        $absolute = $disk->path($relativePath);
        $output = fopen($absolute, 'wb');

        if ($output === false) {
            throw new RuntimeException('Could not open output file for writing.');
        }

        try {
            foreach ($chunks as $chunk) {
                $input = fopen($chunk, 'rb');
                if ($input === false) {
                    throw new RuntimeException('Could not read chunk: '.$chunk);
                }
                stream_copy_to_stream($input, $output);
                fclose($input);
            }
        } finally {
            fclose($output);
        }

        $this->cleanupChunks($directory, $chunks);

        $size = (int) filesize($absolute);
        $detectedMime = $mimeType !== '' ? $mimeType : (mime_content_type($absolute) ?: 'application/octet-stream');

        return DB::transaction(function () use ($user, $folderId, $originalName, $detectedMime, $extension, $size, $relativePath): MediaFile {
            $file = MediaFile::query()->create([
                'folder_id' => $folderId,
                'user_id' => $user->id,
                'name' => $originalName,
                'original_name' => $originalName,
                'mime_type' => $detectedMime,
                'extension' => $extension,
                'size' => $size,
                'disk' => 'public',
                'path' => $relativePath,
            ]);

            $this->thumbnails->handle($file);

            return $file->refresh();
        });
    }

    /**
     * @param  array<int, string>  $chunks
     */
    private function cleanupChunks(string $directory, array $chunks): void
    {
        foreach ($chunks as $chunk) {
            @unlink($chunk);
        }

        if (is_dir($directory)) {
            @rmdir($directory);
        }
    }
}
