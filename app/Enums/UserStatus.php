<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * User account lifecycle. `active` is the only state that can authenticate;
 * the other three each block login and surface separately in filters.
 *
 * - active   = normal account
 * - inactive = manually disabled by an admin
 * - banned   = blocked for policy violation (no email password reset, no re-invite)
 * - pending  = invited but hasn't verified email yet
 */
enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Banned = 'banned';
    case Pending = 'pending';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }

    /** Human label used in filter pills + dropdowns. */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Banned => 'Banned',
            self::Pending => 'Pending',
        };
    }

    /** Tailwind pill colors keyed to status — matches the pages/blog status pills. */
    public function color(): string
    {
        return match ($this) {
            self::Active => 'emerald',
            self::Inactive => 'neutral',
            self::Banned => 'rose',
            self::Pending => 'amber',
        };
    }

    /** Only `active` users can log in / use the app. */
    public function canLogin(): bool
    {
        return $this === self::Active;
    }
}
