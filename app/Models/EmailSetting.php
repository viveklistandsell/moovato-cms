<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Throwable;

/**
 * Global singleton holding the mail / SMTP configuration the admin can
 * edit from `/admin/settings/email`. Cached forever — the cache is busted
 * explicitly via `flush()` on every save so the next request picks up
 * the new credentials without a redis flush or php artisan call.
 */
#[Fillable([
    'mail_transport',
    'mail_host',
    'mail_port',
    'mail_username',
    'mail_password',
    'mail_encryption',
    'mail_from_address',
    'mail_from_name',
])]
final class EmailSetting extends Model
{
    private static ?self $resolved = null;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'mail_port' => 'integer',
        'mail_password' => 'encrypted',
    ];

    /**
     * Returns the singleton row. Resolves by "lowest existing id" rather
     * than `firstOrCreate(['id' => 1])` — auto-increment can push the
     * first row past 1 (e.g. after a re-seed), in which case the
     * `id=1` lookup misses and we'd silently insert a SECOND row every
     * time something asks for the singleton. Once we have at least one
     * row we always reuse it; only the very first call ever inserts.
     */
    public static function current(): self
    {
        if (self::$resolved !== null) {
            return self::$resolved;
        }

        try {
            return self::$resolved = self::query()->orderBy('id')->first()
                ?? self::query()->create([]);
        } catch (Throwable) {
            return new self;
        }
    }

    public static function flush(): void
    {
        self::$resolved = null;
    }
}
