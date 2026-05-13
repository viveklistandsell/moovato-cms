<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFolder;
use Illuminate\Support\Facades\DB;

final readonly class RestoreMediaFolder
{
    public function handle(MediaFolder $folder): MediaFolder
    {
        return DB::transaction(function () use ($folder): MediaFolder {
            $folder->restore();

            $folder->files()->withTrashed()->restore();

            foreach ($folder->children()->withTrashed()->get() as $child) {
                $this->handle($child);
            }

            return $folder->refresh();
        });
    }
}
