<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily trash retention sweep. Soft-deleted media older than 30 days
// gets force-deleted (DB row + disk file + thumbnails) via the same
// actions the admin "force delete" button uses. The hard expectation
// here is that the host runs `php artisan schedule:run` once a minute
// from cron — without that, this is a no-op.
Schedule::command('media:prune-trash --older-than=30')
    ->daily()
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(fn () => info('media:prune-trash completed'))
    ->onFailure(fn () => report(new \RuntimeException('media:prune-trash failed')));
