<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Media\ForceDeleteMediaFile;
use App\Actions\Media\ForceDeleteMediaFolder;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use Illuminate\Console\Command;
use Throwable;

/**
 * Permanently delete soft-deleted media (files + folders) whose
 * `deleted_at` is older than the configured threshold. Defaults to 30
 * days — matches the wording used in the recommendations doc.
 *
 * Wired to the daily scheduler in routes/console.php. Run on demand
 * with `php artisan media:prune-trash --older-than=14 --dry-run` to
 * preview what would go.
 *
 * Reuses the existing ForceDeleteMediaFile / ForceDeleteMediaFolder
 * actions so disk cleanup + thumbnail variants + parent-folder updates
 * happen identically to the admin "force delete" button.
 */
final class PruneMediaTrash extends Command
{
    protected $signature = 'media:prune-trash
        {--older-than=30 : Days threshold; soft-deleted rows older than this are purged}
        {--dry-run : Show what would be purged without writing}';

    protected $description = 'Permanently delete soft-deleted media files and folders past the retention threshold.';

    public function __construct(
        private readonly ForceDeleteMediaFile $purgeFile,
        private readonly ForceDeleteMediaFolder $purgeFolder,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $days = (int) $this->option('older-than');
        if ($days < 1) {
            $this->error("--older-than must be a positive integer (got: {$days}).");

            return self::INVALID;
        }

        $dryRun = (bool) $this->option('dry-run');
        $cutoff = now()->subDays($days);

        $this->info(sprintf(
            '%s soft-deleted media with deleted_at < %s (%d day%s ago).',
            $dryRun ? '[DRY RUN] Would purge' : 'Purging',
            $cutoff->toDateTimeString(),
            $days,
            $days === 1 ? '' : 's',
        ));
        $this->newLine();

        $fileStats = $this->pruneFiles($cutoff, $dryRun);
        $folderStats = $this->pruneFolders($cutoff, $dryRun);

        $this->newLine();
        $this->table(
            ['Kind', 'Found', 'Purged', 'Failed'],
            [
                ['Files', $fileStats['found'], $fileStats['purged'], $fileStats['failed']],
                ['Folders', $folderStats['found'], $folderStats['purged'], $folderStats['failed']],
            ],
        );

        if ($dryRun) {
            $this->warn('Dry run — nothing was written. Re-run without --dry-run to apply.');
        }

        return self::SUCCESS;
    }

    /**
     * @return array{found: int, purged: int, failed: int}
     */
    private function pruneFiles(\DateTimeInterface $cutoff, bool $dryRun): array
    {
        $found = 0;
        $purged = 0;
        $failed = 0;

        MediaFile::onlyTrashed()
            ->where('deleted_at', '<', $cutoff)
            ->orderBy('id')
            ->chunkById(100, function ($files) use ($dryRun, &$found, &$purged, &$failed): void {
                foreach ($files as $file) {
                    $found++;

                    if ($dryRun) {
                        $this->line(sprintf(
                            '  · file #%d %s (trashed %s)',
                            $file->id,
                            $file->original_name ?? $file->name,
                            optional($file->deleted_at)->toDateTimeString() ?? '?',
                        ));

                        continue;
                    }

                    try {
                        $this->purgeFile->handle($file);
                        $purged++;
                    } catch (Throwable $e) {
                        $failed++;
                        $this->line("  ! file #{$file->id} failed: ".$e->getMessage());
                    }
                }
            });

        return ['found' => $found, 'purged' => $purged, 'failed' => $failed];
    }

    /**
     * @return array{found: int, purged: int, failed: int}
     */
    private function pruneFolders(\DateTimeInterface $cutoff, bool $dryRun): array
    {
        $found = 0;
        $purged = 0;
        $failed = 0;

        MediaFolder::onlyTrashed()
            ->where('deleted_at', '<', $cutoff)
            ->orderBy('id')
            ->chunkById(100, function ($folders) use ($dryRun, &$found, &$purged, &$failed): void {
                foreach ($folders as $folder) {
                    $found++;

                    if ($dryRun) {
                        $this->line(sprintf(
                            '  · folder #%d %s (trashed %s)',
                            $folder->id,
                            $folder->path ?? $folder->name,
                            optional($folder->deleted_at)->toDateTimeString() ?? '?',
                        ));

                        continue;
                    }

                    try {
                        $this->purgeFolder->handle($folder);
                        $purged++;
                    } catch (Throwable $e) {
                        $failed++;
                        $this->line("  ! folder #{$folder->id} failed: ".$e->getMessage());
                    }
                }
            });

        return ['found' => $found, 'purged' => $purged, 'failed' => $failed];
    }
}
