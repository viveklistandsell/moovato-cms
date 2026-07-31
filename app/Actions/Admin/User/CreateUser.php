<?php

declare(strict_types=1);

namespace App\Actions\Admin\User;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final readonly class CreateUser
{
    public function __construct(private ActivityLogger $logger) {}

    /**
     * @param  array{
     *   name: string,
     *   email: string,
     *   password: string,
     *   status: string,
     *   role?: ?string,
     *   avatar?: UploadedFile|null,
     *   avatar_path?: ?string,
     * }  $data
     */
    public function handle(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            // Avatar source priority: uploaded file > media-library path > none.
            $avatarPath = null;
            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                $avatarPath = $data['avatar']->store('avatars', 'public');
            } elseif (! empty($data['avatar_path'])) {
                $avatarPath = $data['avatar_path'];
            }

            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'], // hashed via model cast
                'status' => $data['status'],
                'avatar' => $avatarPath,
            ]);

            if (! empty($data['role'])) {
                $user->assignRole($data['role']);
            }

            $this->logger->created(
                $user,
                "Created user {$user->email}",
                ['role' => $data['role'] ?? null],
            );

            return $user;
        });
    }
}
