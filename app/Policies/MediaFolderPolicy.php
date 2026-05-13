<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MediaFolder;
use App\Models\User;

final class MediaFolderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, MediaFolder $mediaFolder): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, MediaFolder $mediaFolder): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, MediaFolder $mediaFolder): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, MediaFolder $mediaFolder): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, MediaFolder $mediaFolder): bool
    {
        return $user->isAdmin();
    }
}
