<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFolder;
use Illuminate\Support\Facades\DB;

final readonly class DeleteMediaFolder
{
    public function handle(MediaFolder $folder): void
    {
        DB::transaction(function () use ($folder): void {
            $folder->files()->delete();

            foreach ($folder->children as $child) {
                $this->handle($child);
            }

            $folder->delete();
        });
    }
}
