<?php

declare(strict_types=1);

namespace App\Actions\Admin\User;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final readonly class UpdateUser
{
    public function __construct(private ActivityLogger $logger) {}

    /**
     * @param  array{
     *   name: string,
     *   email: string,
     *   password?: ?string,
     *   status: string,
     *   role?: ?string,
     *   avatar?: UploadedFile|null,
     *   avatar_path?: ?string,
     *   remove_avatar?: bool|null,
     * }  $data
     */
    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $original = [
                'email' => $user->email,
                'status' => $user->status?->value,
                'role' => $user->roles->pluck('name')->first(),
            ];

            $user->fill([
                'name' => $data['name'],
                'email' => $data['email'],
                'status' => $data['status'],
            ]);

            if (! empty($data['password'])) {
                $user->password = $data['password']; // hashed via cast
            }

            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                if ($user->avatar !== null) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = $data['avatar']->store('avatars', 'public');
            } elseif (! empty($data['avatar_path']) && $data['avatar_path'] !== $user->avatar) {
                $user->avatar = $data['avatar_path'];
            } elseif (filter_var($data['remove_avatar'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                if ($user->avatar !== null) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = null;
            }

            $user->save();

            // Single role per user — sync replaces whatever was there. Passing
            // an empty array clears the role.
            $newRole = $data['role'] ?? null;
            $user->syncRoles($newRole !== null && $newRole !== '' ? [$newRole] : []);

            $this->logger->updated(
                $user,
                "Updated user {$user->email}",
                [
                    'before' => $original,
                    'after' => [
                        'email' => $user->email,
                        'status' => $user->status?->value,
                        'role' => $newRole,
                    ],
                ],
            );

            return $user;
        });
    }
}
