<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
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
            'PHP version',
            $ok ? 'ok' : 'fail',
            $ok ? 'Meets minimum ('.self::PHP_MIN.').' : 'Below minimum ('.self::PHP_MIN.'). Upgrade PHP.',
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
                'Database',
                'ok',
                'Reachable.',
                "{$driver} · {$name}",
            );
        } catch (Throwable $e) {
            return $this->row(
                'database',
                'Database',
                'fail',
                'Connection failed: '.$e->getMessage(),
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
                'Cache driver',
                $ok ? 'ok' : 'fail',
                $ok ? 'put/get/forget roundtrip succeeded.' : 'Cache wrote but read back wrong value.',
                (string) config('cache.default'),
            );
        } catch (Throwable $e) {
            return $this->row(
                'cache',
                'Cache driver',
                'fail',
                'Cache failed: '.$e->getMessage(),
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
                'Storage symlink',
                'fail',
                'public/storage missing. Run `php artisan storage:link` on this host.',
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
                'Storage symlink',
                $matches ? 'warn' : 'fail',
                $matches
                    ? 'public/storage is not a symlink but its realpath resolves to storage/app/public (typical of Windows junctions).'
                    : 'public/storage exists but is not a symlink to storage/app/public.',
                $link,
            );
        }

        $ok = realpath($link) === realpath($target);

        return $this->row(
            'storage-symlink',
            'Storage symlink',
            $ok ? 'ok' : 'fail',
            $ok ? 'Points at storage/app/public.' : 'Symlink target ('.readlink($link).') does not match storage/app/public.',
            readlink($link),
        );
    }

    private function checkStorageWritable(): array
    {
        $path = storage_path('app/public');
        $ok = is_dir($path) && is_writable($path);

        return $this->row(
            'storage-writable',
            'Storage writable',
            $ok ? 'ok' : 'fail',
            $ok ? 'PHP can write here.' : 'storage/app/public is not writable by the PHP user.',
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
                'Disk free space',
                'warn',
                'Could not read disk space — host restriction?',
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
            'Disk free space',
            $status,
            sprintf('%s free of %s on the storage volume.', $this->bytes((int) $free), $this->bytes((int) $total)),
            sprintf('%.1f%% free', $percentFree),
        );
    }

    private function checkQueue(): array
    {
        $driver = (string) config('queue.default');

        if ($driver === 'sync') {
            return $this->row(
                'queue',
                'Queue worker',
                'warn',
                'Driver is `sync` — jobs run inline during requests. Switch to database/redis and run `php artisan queue:work` in production.',
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
                    'Queue worker',
                    $status,
                    "{$pending} pending · {$failed} failed.",
                    $driver,
                );
            } catch (Throwable $e) {
                return $this->row(
                    'queue',
                    'Queue worker',
                    'warn',
                    'Driver is `database` but the jobs table could not be read: '.$e->getMessage(),
                    $driver,
                );
            }
        }

        return $this->row(
            'queue',
            'Queue worker',
            'ok',
            "Driver: {$driver}. Ensure a worker process is running on the host.",
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
                'Mail driver',
                'warn',
                'Mailer is `log` — outgoing email lands in the log file, not in inboxes. Set MAIL_MAILER=smtp/ses/etc. before launch.',
                $mailer,
            );
        }

        if ($from === '') {
            return $this->row(
                'mail',
                'Mail driver',
                'warn',
                'No MAIL_FROM_ADDRESS configured. Notifications will fail to send.',
                $mailer,
            );
        }

        return $this->row(
            'mail',
            'Mail driver',
            'ok',
            "Sending from {$from}.",
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
                'Debug mode',
                'fail',
                'APP_DEBUG=true on production exposes stack traces with file paths and env values. Set to false immediately.',
                'on',
            );
        }

        return $this->row(
            'debug',
            'Debug mode',
            'ok',
            $debug ? 'On (allowed outside production).' : 'Off.',
            $debug ? 'on' : 'off',
        );
    }

    private function checkAppEnv(): array
    {
        $env = (string) config('app.env');

        return $this->row(
            'env',
            'App environment',
            'ok',
            'Reported by APP_ENV. Use this to confirm which environment a deploy actually landed on.',
            $env,
        );
    }

    private function checkLogFile(): array
    {
        $path = storage_path('logs/laravel.log');

        if (! file_exists($path)) {
            return $this->row(
                'log',
                'Log file',
                'warn',
                'No laravel.log yet — first request will create it.',
                $path,
            );
        }

        $size = filesize($path);
        $mtime = filemtime($path);
        $detail = sprintf(
            '%s · last write %s.',
            $this->bytes($size === false ? 0 : (int) $size),
            $mtime !== false ? date('Y-m-d H:i:s', $mtime) : 'unknown',
        );
        $status = ($size !== false && $size > 100 * 1024 * 1024) ? 'warn' : 'ok';

        return $this->row(
            'log',
            'Log file',
            $status,
            $detail.($status === 'warn' ? ' Consider rotating — over 100 MB.' : ''),
            $path,
        );
    }

    private function checkMaintenance(): array
    {
        try {
            $on = (bool) (\App\Models\SiteSetting::current()->maintenance_enabled ?? false);
        } catch (Throwable) {
            return $this->row(
                'maintenance',
                'Maintenance mode',
                'warn',
                'Could not read site settings.',
                null,
            );
        }

        return $this->row(
            'maintenance',
            'Maintenance mode',
            $on ? 'warn' : 'ok',
            $on ? 'Holding page is active — only admins see the live site.' : 'Off — site is public.',
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
