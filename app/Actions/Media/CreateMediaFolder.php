<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\MediaFolder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class CreateMediaFolder
{
    /**
     * @param  array{name: string, parent_id?: ?int}  $data
     */
    public function handle(User $user, array $data): MediaFolder
    {
        return DB::transaction(function () use ($user, $data): MediaFolder {
            $parent = isset($data['parent_id']) ? MediaFolder::query()->find($data['parent_id']) : null;

            $folder = MediaFolder::query()->create([
                'parent_id' => $parent?->id,
                'user_id' => $user->id,
                'name' => $data['name'],
                'path' => $parent ? $parent->path.'/'.$data['name'] : $data['name'],
            ]);

            return $folder;
        });
    }
}
