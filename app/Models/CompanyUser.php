<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

/**
 * Auth account for a company owner / partner.
 *
 * A CompanyUser is created in one of two ways:
 *   1. Public POST /partner/register — status=pending, password=null
 *   2. Admin-initiated invite (future) — same shape, seeded status
 *
 * The password stays null until the admin approves the row and the
 * user follows the "set your password" email link. `canLogin()`
 * enforces both (approved + password set) before an auth attempt is
 * even considered.
 */
#[Fillable([
    'company_id',
    'first_name', 'last_name', 'email', 'phone',
    'password',
    'status',
    'rejection_reason',
    'registration_docs',
    'reviewed_by_user_id', 'reviewed_at',
    'email_verified_at',
])]
#[Hidden(['password', 'remember_token'])]
final class CompanyUser extends Authenticatable implements CanResetPasswordContract
{
    use CanResetPassword, Notifiable, SoftDeletes;

    /** Lifecycle values kept as consts so seeders + controllers agree. */
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_INACTIVE = 'inactive';

    protected $table = 'company_users';

    /* --------------------------------------------- relations */

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    /* --------------------------------------------- helpers */

    /**
     * Convenience "full name" for greetings + admin lists.
     */
    public function fullName(): string
    {
        return mb_trim("{$this->first_name} {$this->last_name}");
    }

    public function canLogin(): bool
    {
        return $this->status === self::STATUS_APPROVED
            && $this->password !== null;
    }

    public function portalUrl(?string $locale = null): ?string
    {
        if ($this->company_id === null) {
            return null;
        }

        $locale ??= app()->getLocale();

        $company = $this->relationLoaded('company')
            ? $this->company
            : Company::query()->with('translations:id,company_id,lang,permalink')->find($this->company_id);

        if ($company === null) {
            return null;
        }

        $translation = $company->translation($locale);
        $permalink = $translation?->permalink;

        return $permalink !== null && $permalink !== ''
            ? "/company-portal/{$company->id}/{$permalink}"
            : "/company-portal/{$company->id}";
    }

    /* --------------------------------------------- scopes */

    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_REJECTED);
    }

    protected static function booted(): void
    {
        self::deleting(function (self $user): void {
            if ($user->company_id === null) {
                return;
            }

            $company = Company::query()->find($user->company_id);
            if ($company === null) {
                return;
            }

            if ($user->isForceDeleting()) {
                self::cascadeForceDeleteCompany($company);

                return;
            }

            if ($company->status === 'published') {
                $company->update(['status' => 'draft']);
            }
        });

        self::restoring(function (self $user): void {
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'password' => 'hashed',
            'registration_docs' => 'array',
        ];
    }

    private static function cascadeForceDeleteCompany(Company $company): void
    {
        DB::transaction(function () use ($company): void {
            $company->translations()->delete();
            $company->contacts()->delete();
            $company->media()->delete();
            $company->faqs()->delete();
            $company->reviews()->delete();
            $company->services()->detach();
            $company->serviceAreas()->detach();
            $company->delete();
        });
    }
}
