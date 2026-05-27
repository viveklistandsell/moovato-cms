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
     * }  $data
     */
    public function handle(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $avatarPath = isset($data['avatar']) && $data['avatar'] instanceof UploadedFile
                ? $data['avatar']->store('avatars', 'public')
                : null;

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
