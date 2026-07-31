<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Page;
use Illuminate\Support\Facades\Process;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\Process as SymfonyProcess;

/**
 * Export / backup hub. Three independent streamed downloads — pages and
 * blog posts as portable JSON (drop into another instance, parse manually,
 * etc.) and a full database dump as SQL via `mysqldump`.
 *
 * Permission: `system.cache` is reused for now since "export" is a sysadmin-
 * adjacent action and we don't want to proliferate permissions until a
 * separate role actually needs it.
 */
final class ExportController extends Controller
{
    public function index(): Response
    {
        $dump = $this->resolveMysqldumpPath();

        return Inertia::render('admin/system/Export', [
            'counts' => [
                'pages' => Page::query()->count(),
                'posts' => Blog::query()->count(),
            ],
            'database' => [
                'driver' => (string) config('database.default'),
                'mysqldump_available' => $dump !== null,
                'mysqldump_path' => $dump,
            ],
        ]);
    }

    public function pages(): StreamedResponse
    {
        $filename = 'pages-export-'.now()->format('Y-m-d-His').'.json';

        return $this->streamJson($filename, function (): array {
            return Page::query()
                ->with([
                    'translations',
                    'categories.translations',
                    'widgets.translations',
                ])
                ->orderBy('id')
                ->get()
                ->map(fn (Page $p): array => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'permalink' => $p->permalink,
                    'image' => $p->image,
                    'template' => $p->template,
                    'is_home' => (bool) $p->is_home,
                    'status' => $p->status,
                    'created_at' => $p->created_at?->toIso8601String(),
                    'updated_at' => $p->updated_at?->toIso8601String(),
                    'translations' => $p->translations->map(fn ($t): array => [
                        'lang' => $t->lang,
                        'title' => $t->title,
                        'permalink' => $t->permalink,
                    ])->all(),
                    'categories' => $p->categories->map(fn ($c): array => [
                        'permalink' => $c->permalink,
                        'title' => $c->title,
                    ])->all(),
                    'widgets' => $p->widgets->map(fn ($w): array => [
                        'type' => $w->type,
                        'position' => $w->position,
                        'settings' => $w->settings,
                        'visibility' => $w->visibility,
                        'css_class' => $w->css_class,
                        'is_active' => (bool) $w->is_active,
                        'translations' => $w->translations->map(fn ($wt): array => [
                            'lang' => $wt->lang,
                            'data' => $wt->data,
                        ])->all(),
                    ])->all(),
                ])
                ->all();
        });
    }

    public function posts(): StreamedResponse
    {
        $filename = 'blog-posts-export-'.now()->format('Y-m-d-His').'.json';

        return $this->streamJson($filename, function (): array {
            return Blog::query()
                ->with(['translations', 'categories', 'tags', 'user:id,name,email'])
                ->orderBy('id')
                ->get()
                ->map(fn (Blog $b): array => [
                    'id' => $b->id,
                    'name' => $b->name,
                    'permalink' => $b->permalink,
                    'image' => $b->image,
                    'short_description' => $b->short_description,
                    'content' => $b->content,
                    'status' => $b->status,
                    'is_sticky' => (bool) $b->is_sticky,
                    'is_featured' => (bool) $b->is_featured,
                    'view_count' => (int) $b->view_count,
                    'reading_time' => (int) $b->reading_time,
                    'created_at' => $b->created_at?->toIso8601String(),
                    'updated_at' => $b->updated_at?->toIso8601String(),
                    'author' => $b->user?->only(['name', 'email']),
                    'translations' => $b->translations->map(fn ($t): array => [
                        'lang' => $t->lang,
                        'name' => $t->name,
                        'permalink' => $t->permalink,
                        'short_description' => $t->short_description,
                        'content' => $t->content,
                    ])->all(),
                    'categories' => $b->categories->pluck('permalink')->all(),
                    'tags' => $b->tags->pluck('permalink')->all(),
                ])
                ->all();
        });
    }

    /**
     * Streams a `mysqldump` of the active database. Bails with a clear
     * 422 message when mysqldump isn't resolvable so the operator sees
     * the actual problem instead of a generic 500.
     */
    public function database(): StreamedResponse
    {
        $dump = $this->resolveMysqldumpPath();
        abort_if($dump === null, 422, 'mysqldump is not available on this host.');

        $connection = config('database.connections.'.config('database.default'));

        $host = (string) ($connection['host'] ?? '127.0.0.1');
        $port = (string) ($connection['port'] ?? '3306');
        $database = (string) ($connection['database'] ?? '');
        $username = (string) ($connection['username'] ?? '');
        $password = (string) ($connection['password'] ?? '');

        $filename = "database-backup-{$database}-".now()->format('Y-m-d-His').'.sql';

        $credsFile = tempnam(sys_get_temp_dir(), 'mysqldump_');
        if ($credsFile === false) {
            abort(500, 'Unable to allocate temp file for mysqldump credentials.');
        }
        @chmod($credsFile, 0600);
        file_put_contents(
            $credsFile,
            "[client]\npassword=\"".str_replace('"', '\"', $password)."\"\n",
        );

        return new StreamedResponse(function () use ($dump, $credsFile, $host, $port, $database, $username): void {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            try {
                $process = new SymfonyProcess([
                    $dump,
                    '--defaults-extra-file='.$credsFile,
                    "--host={$host}",
                    "--port={$port}",
                    "--user={$username}",
                    '--single-transaction',
                    '--quick',
                    '--lock-tables=false',
                    '--no-tablespaces',
                    '--default-character-set=utf8mb4',
                    $database,
                ], null, $this->subprocessEnv());

                $process->setTimeout(600);
                $process->start();

                foreach ($process as $type => $chunk) {
                    if ($type === SymfonyProcess::OUT) {
                        echo $chunk;
                    } else {
                        foreach (preg_split('/\r?\n/', $chunk) ?: [] as $line) {
                            if ($line !== '') {
                                echo "-- mysqldump stderr: {$line}\n";
                            }
                        }
                    }
                    flush();
                }
            } finally {
                @unlink($credsFile);
            }
        }, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'X-Accel-Buffering' => 'no',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Explicit subprocess environment.
     *
     * Apache + PHP-FPM under XAMPP scrub most of the parent environment
     * by default. mysqldump on Windows needs SystemRoot / WINDIR for
     * Winsock to initialise.
     *
     * @return array<string, string>
     */
    private function subprocessEnv(): array
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return [
                'PATH' => (string) (getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin'),
                'HOME' => (string) (getenv('HOME') ?: sys_get_temp_dir()),
                'LANG' => (string) (getenv('LANG') ?: 'C.UTF-8'),
            ];
        }

        return [
            'SystemRoot' => (string) (getenv('SystemRoot') ?: 'C:\\Windows'),
            'WINDIR' => (string) (getenv('WINDIR') ?: 'C:\\Windows'),
            'TEMP' => (string) (getenv('TEMP') ?: sys_get_temp_dir()),
            'TMP' => (string) (getenv('TMP') ?: sys_get_temp_dir()),
            'PATH' => (string) (getenv('PATH') ?: 'C:\\Windows\\system32;C:\\Windows'),
            'PATHEXT' => (string) (getenv('PATHEXT') ?: '.COM;.EXE;.BAT;.CMD'),
            'SYSTEMDRIVE' => (string) (getenv('SYSTEMDRIVE') ?: 'C:'),
            'COMSPEC' => (string) (getenv('COMSPEC') ?: 'C:\\Windows\\system32\\cmd.exe'),
        ];
    }

    /**
     * @param  callable(): array<int, mixed>  $payload
     */
    private function streamJson(string $filename, callable $payload): StreamedResponse
    {
        return new StreamedResponse(function () use ($payload): void {
            $rows = $payload();
            echo json_encode([
                'exported_at' => now()->toIso8601String(),
                'driver' => config('database.default'),
                'count' => count($rows),
                'rows' => $rows,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'X-Accel-Buffering' => 'no',
        ]);
    }
    private function resolveMysqldumpPath(): ?string
    {
        if (config('database.default') !== 'mysql') {
            return null;
        }

        $override = (string) env('DB_DUMP_BIN', '');
        if ($override !== '' && is_executable($override)) {
            return $this->normaliseSlashes($override);
        }

        $candidates = PHP_OS_FAMILY === 'Windows'
            ? [
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                'C:\\xampp\\mariadb\\bin\\mysqldump.exe',
                'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
                'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
                'C:\\Program Files\\MariaDB 10.11\\bin\\mysqldump.exe',
                'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
            ]
            : [
                '/usr/bin/mysqldump',
                '/usr/local/bin/mysqldump',
                '/usr/local/mysql/bin/mysqldump',
                '/opt/homebrew/bin/mysqldump',
                '/opt/homebrew/opt/mysql/bin/mysqldump',
                '/Applications/MAMP/Library/bin/mysqldump',
            ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $this->normaliseSlashes($path);
            }
        }

        $cmd = PHP_OS_FAMILY === 'Windows' ? 'where mysqldump' : 'command -v mysqldump';
        $result = Process::run($cmd);

        if ($result->successful()) {
            $first = trim(strtok($result->output(), "\r\n"));
            if ($first !== '' && file_exists($first)) {
                return $this->normaliseSlashes($first);
            }
        }

        return null;
    }
    private function normaliseSlashes(string $path): string
    {
        return str_replace('\\', '/', $path);
    }
}
