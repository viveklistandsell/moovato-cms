<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Operational health overview. Each check returns a structured
 * { id, label, status: ok|warn|fail, detail, value } row; the Vue page
 * groups them and colours by status.
 *
 * Deliberately read-only — this surface only reports, never fixes. Fixes
 * are one click away via the existing System pages (Cache Management,
 * Sitemap, Maintenance Mode, etc.).
 *
 * Permission: `system.cache` — sysadmin-adjacent like Cache + Export.
 *
 * Localization: labels + detail strings come from `lang/<locale>/admin.php`
 * under `system.health_check.<id>.*`. The SetAdminLocale middleware sets
 * the active locale before this controller runs, so `trans()` returns the
 * right language for the user driving the page.
 */
final class HealthController extends Controller
{
    /** Minimum PHP version we expect (matches composer.json `require.php`). */
    private const PHP_MIN = '8.3.0';

    public function index(Request $request): Response
    {
        $checks = [
            $this->checkPhp(),
            $this->checkDatabase(),
            $this->checkCache(),
            $this->checkStorageSymlink(),
            $this->checkStorageWritable(),
            $this->checkDiskFree(),
            $this->checkQueue(),
            $this->checkMail(),
            $this->checkDebugMode(),
            $this->checkAppEnv(),
            $this->checkLogFile(),
            $this->checkMaintenance(),
        ];

        return Inertia::render('admin/system/Health', [
            'checks' => $checks,
            'summary' => $this->summarise($checks),
        ]);
    }

    /**
     * @param  list<array{id: string, label: string, status: string, detail: string, value: ?string}>  $checks
     * @return array{ok: int, warn: int, fail: int}
     */
    private function summarise(array $checks): array
    {
        $totals = ['ok' => 0, 'warn' => 0, 'fail' => 0];
        foreach ($checks as $check) {
            $totals[$check['status']]++;
        }

        return $totals;
    }

    private function checkPhp(): array
    {
        $current = PHP_VERSION;
        $ok = version_compare($current, self::PHP_MIN, '>=');

        return $this->row(
            'php',
            $this->label('php'),
            $ok ? 'ok' : 'fail',
            $this->detail($ok ? 'php.ok' : 'php.fail', ['min' => self::PHP_MIN]),
            $current,
        );
    }

    private function checkDatabase(): array
    {
        try {
            DB::select('SELECT 1');
            $driver = (string) config('database.default');
            $name = (string) (config('database.connections.'.$driver.'.database') ?? '?');

            return $this->row(
                'database',
                $this->label('database'),
                'ok',
                $this->detail('database.ok'),
                "{$driver} · {$name}",
            );
        } catch (Throwable $e) {
            return $this->row(
                'database',
                $this->label('database'),
                'fail',
                $this->detail('database.fail', ['error' => $e->getMessage()]),
                null,
            );
        }
    }

    private function checkCache(): array
    {
        try {
            $key = 'health-check-'.uniqid('', true);
            $value = 'probe';
            Cache::put($key, $value, 10);
            $read = Cache::get($key);
            Cache::forget($key);

            $ok = $read === $value;

            return $this->row(
                'cache',
                $this->label('cache'),
                $ok ? 'ok' : 'fail',
                $this->detail($ok ? 'cache.ok' : 'cache.wrong_value'),
                (string) config('cache.default'),
            );
        } catch (Throwable $e) {
            return $this->row(
                'cache',
                $this->label('cache'),
                'fail',
                $this->detail('cache.fail', ['error' => $e->getMessage()]),
                (string) config('cache.default'),
            );
        }
    }

    private function checkStorageSymlink(): array
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (! file_exists($link)) {
            return $this->row(
                'storage-symlink',
                $this->label('storage_symlink'),
                'fail',
                $this->detail('storage_symlink.missing'),
                $link,
            );
        }

        if (! is_link($link)) {
            // On Windows this can be a false negative when XAMPP creates
            // junction-style links — treat the realpath match as
            // authoritative and downgrade fail → warn.
            $matches = realpath($link) === realpath($target);

            return $this->row(
                'storage-symlink',
                $this->label('storage_symlink'),
                $matches ? 'warn' : 'fail',
                $this->detail($matches ? 'storage_symlink.windows_junction' : 'storage_symlink.not_symlink'),
                $link,
            );
        }

        $ok = realpath($link) === realpath($target);

        return $this->row(
            'storage-symlink',
            $this->label('storage_symlink'),
            $ok ? 'ok' : 'fail',
            $ok
                ? $this->detail('storage_symlink.ok')
                : $this->detail('storage_symlink.mismatch', ['target' => (string) readlink($link)]),
            (string) readlink($link),
        );
    }

    private function checkStorageWritable(): array
    {
        $path = storage_path('app/public');
        $ok = is_dir($path) && is_writable($path);

        return $this->row(
            'storage-writable',
            $this->label('storage_writable'),
            $ok ? 'ok' : 'fail',
            $this->detail($ok ? 'storage_writable.ok' : 'storage_writable.fail'),
            $path,
        );
    }

    private function checkDiskFree(): array
    {
        $path = storage_path();
        $free = disk_free_space($path);
        $total = disk_total_space($path);

        if ($free === false || $total === false) {
            return $this->row(
                'disk',
                $this->label('disk'),
                'warn',
                $this->detail('disk.unreadable'),
                null,
            );
        }

        $percentFree = $total > 0 ? ($free / $total) * 100 : 0;
        $status = match (true) {
            $percentFree < 5 => 'fail',
            $percentFree < 15 => 'warn',
            default => 'ok',
        };

        return $this->row(
            'disk',
            $this->label('disk'),
            $status,
            $this->detail('disk.detail', [
                'free' => $this->bytes((int) $free),
                'total' => $this->bytes((int) $total),
            ]),
            $this->detail('disk.value', ['percent' => number_format($percentFree, 1)]),
        );
    }

    private function checkQueue(): array
    {
        $driver = (string) config('queue.default');

        if ($driver === 'sync') {
            return $this->row(
                'queue',
                $this->label('queue'),
                'warn',
                $this->detail('queue.sync'),
                $driver,
            );
        }

        // For driver=database we can at least check the `jobs` table size to
        // surface a stuck queue. Other drivers don't expose a portable count.
        if ($driver === 'database') {
            try {
                $pending = DB::table((string) config('queue.connections.database.table', 'jobs'))->count();
                $failed = DB::table('failed_jobs')->count();
                $status = $failed > 0 ? 'warn' : 'ok';

                return $this->row(
                    'queue',
                    $this->label('queue'),
                    $status,
                    $this->detail('queue.database', ['pending' => $pending, 'failed' => $failed]),
                    $driver,
                );
            } catch (Throwable $e) {
                return $this->row(
                    'queue',
                    $this->label('queue'),
                    'warn',
                    $this->detail('queue.database_unreadable', ['error' => $e->getMessage()]),
                    $driver,
                );
            }
        }

        return $this->row(
            'queue',
            $this->label('queue'),
            'ok',
            $this->detail('queue.other_driver', ['driver' => $driver]),
            $driver,
        );
    }

    private function checkMail(): array
    {
        $mailer = (string) config('mail.default');
        $from = (string) (config('mail.from.address') ?? '');

        if ($mailer === 'log') {
            return $this->row(
                'mail',
                $this->label('mail'),
                'warn',
                $this->detail('mail.log'),
                $mailer,
            );
        }

        if ($from === '') {
            return $this->row(
                'mail',
                $this->label('mail'),
                'warn',
                $this->detail('mail.no_from'),
                $mailer,
            );
        }

        return $this->row(
            'mail',
            $this->label('mail'),
            'ok',
            $this->detail('mail.ok', ['from' => $from]),
            $mailer,
        );
    }

    private function checkDebugMode(): array
    {
        $debug = (bool) config('app.debug');
        $env = (string) config('app.env');

        if ($env === 'production' && $debug) {
            return $this->row(
                'debug',
                $this->label('debug'),
                'fail',
                $this->detail('debug.production_on'),
                'on',
            );
        }

        return $this->row(
            'debug',
            $this->label('debug'),
            'ok',
            $this->detail($debug ? 'debug.on' : 'debug.off'),
            $debug ? 'on' : 'off',
        );
    }

    private function checkAppEnv(): array
    {
        $env = (string) config('app.env');

        return $this->row(
            'env',
            $this->label('env'),
            'ok',
            $this->detail('env.detail'),
            $env,
        );
    }

    private function checkLogFile(): array
    {
        $path = storage_path('logs/laravel.log');

        if (! file_exists($path)) {
            return $this->row(
                'log',
                $this->label('log'),
                'warn',
                $this->detail('log.missing'),
                $path,
            );
        }

        $size = filesize($path);
        $mtime = filemtime($path);
        $detail = $this->detail('log.detail', [
            'size' => $this->bytes($size === false ? 0 : (int) $size),
            'when' => $mtime !== false ? date('Y-m-d H:i:s', $mtime) : $this->detail('log.unknown_when'),
        ]);
        $status = ($size !== false && $size > 100 * 1024 * 1024) ? 'warn' : 'ok';

        return $this->row(
            'log',
            $this->label('log'),
            $status,
            $detail.($status === 'warn' ? $this->detail('log.rotate_hint') : ''),
            $path,
        );
    }

    private function checkMaintenance(): array
    {
        try {
            $on = (bool) (SiteSetting::current()->maintenance_enabled ?? false);
        } catch (Throwable) {
            return $this->row(
                'maintenance',
                $this->label('maintenance'),
                'warn',
                $this->detail('maintenance.unreadable'),
                null,
            );
        }

        return $this->row(
            'maintenance',
            $this->label('maintenance'),
            $on ? 'warn' : 'ok',
            $this->detail($on ? 'maintenance.on' : 'maintenance.off'),
            $on ? 'enabled' : 'disabled',
        );
    }

    /**
     * @return array{id: string, label: string, status: string, detail: string, value: ?string}
     */
    private function row(string $id, string $label, string $status, string $detail, ?string $value): array
    {
        return [
            'id' => $id,
            'label' => $label,
            'status' => $status,
            'detail' => $detail,
            'value' => $value,
        ];
    }

    /**
     * Convenience wrapper for the localized check label.
     */
    private function label(string $checkKey): string
    {
        return (string) trans("admin.system.health_check.{$checkKey}.label");
    }

    /**
     * Convenience wrapper for localized check detail strings. `$detailKey`
     * is shaped like `<check>.<variant>` (e.g. `php.ok`, `database.fail`).
     *
     * @param  array<string, string|int|float>  $replacements
     */
    private function detail(string $detailKey, array $replacements = []): string
    {
        return (string) trans("admin.system.health_check.{$detailKey}", $replacements);
    }

    private function bytes(int $b): string
    {
        if ($b < 1024) {
            return $b.' B';
        }
        if ($b < 1024 * 1024) {
            return number_format($b / 1024, 1).' KB';
        }
        if ($b < 1024 * 1024 * 1024) {
            return number_format($b / (1024 * 1024), 1).' MB';
        }

        return number_format($b / (1024 * 1024 * 1024), 2).' GB';
    }
}
