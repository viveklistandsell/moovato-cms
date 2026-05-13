<?php

declare(strict_types=1);

namespace App\Actions\Media;

use Illuminate\Http\UploadedFile;

final readonly class StoreMediaChunk
{
    /**
     * @return array{received: int, total: int, complete: bool}
     */
    public function handle(string $identifier, int $chunkNumber, int $totalChunks, UploadedFile $chunk): array
    {
        $directory = $this->chunkDirectory($identifier);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $chunk->move($directory, sprintf('%06d', $chunkNumber));

        return [
            'received' => $chunkNumber,
            'total' => $totalChunks,
            'complete' => $this->countChunks($directory) >= $totalChunks,
        ];
    }

    public function chunkExists(string $identifier, int $chunkNumber): bool
    {
        return is_file($this->chunkDirectory($identifier).'/'.sprintf('%06d', $chunkNumber));
    }

    public function chunkDirectory(string $identifier): string
    {
        $safe = preg_replace('/[^A-Za-z0-9_\-]/', '', $identifier);

        return storage_path('app/chunks/'.$safe);
    }

    private function countChunks(string $directory): int
    {
        $entries = glob($directory.'/*') ?: [];

        return count($entries);
    }
}
