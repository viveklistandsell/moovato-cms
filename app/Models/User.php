<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name', 'email', 'password', 'avatar', 'status',
    'last_login_at', 'last_login_ip', 'login_count',
])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
final class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    /** Reserved role name for the unrestricted system administrator. */
    public const SUPER_ADMIN_ROLE = 'Super Admin';

    /** Bypass for the global Gate::before in AppServiceProvider — keeps `can('anything')` true. */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::SUPER_ADMIN_ROLE);
    }

    /** Legacy entry point kept so existing call sites (admin middleware, sidebar) don't break. */
    public function isAdmin(): bool
    {
        return $this->isSuperAdmin();
    }

    /** Public URL for the avatar, or null if none set. */
    public function avatarUrl(): ?string
    {
        return $this->avatar !== null
            ? Storage::disk('public')->url($this->avatar)
            : null;
    }

    // ----------------------------------------------------------------------
    // Query scopes
    // ----------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Active->value);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Inactive->value);
    }

    public function scopeBanned(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Banned->value);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Pending->value);
    }

    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'status' => UserStatus::class,
            'login_count' => 'integer',
        ];
    }
}
