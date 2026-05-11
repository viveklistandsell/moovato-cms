<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFolder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final readonly class ForceDeleteMediaFolder
{
    public function handle(MediaFolder $folder): void
    {
        DB::transaction(function () use ($folder): void {
            $files = $folder->files()->withTrashed()->get();

            foreach ($files as $file) {
                $disk = Storage::disk($file->disk);

                foreach (array_filter([$file->path, $file->thumb_path, $file->medium_path]) as $path) {
                    $disk->delete($path);
                }

                $file->forceDelete();
            }

            foreach ($folder->children()->withTrashed()->get() as $child) {
                $this->handle($child);
            }

            $folder->forceDelete();
        });
    }
}
