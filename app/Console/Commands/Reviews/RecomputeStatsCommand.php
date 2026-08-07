<?php

declare(strict_types=1);

namespace App\Console\Commands\Reviews;

use App\Models\Company;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * One-shot / on-demand backfill for the review cache columns.
 *
 * Two situations call for this:
 *   1. First-time deploy of the review module — companies seeded
 *      before the observer existed still have zeroed caches.
 *   2. Data recovery — if the cache ever drifts (bulk SQL import,
 *      migration mishap), this is the "reset to truth" button.
 *
 * The observer keeps the caches in sync during normal operation; this
 * command should NOT be needed after Phase 3 lands.
 */
#[Signature('reviews:recompute-stats {--company= : Only recompute for one company id}')]
#[Description('Recompute the denormalized rating cache columns on companies from company_reviews.')]
final class RecomputeStatsCommand extends Command
{
    public function handle(): int
    {
        $oneCompany = $this->option('company');

        $query = Company::query()->select('id')->orderBy('id');
        if ($oneCompany !== null) {
            $query->where('id', (int) $oneCompany);
        }

        $count = $query->count();
        if ($count === 0) {
            $this->warn('No companies matched.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->chunkById(100, function ($companies) use ($bar): void {
            foreach ($companies as $company) {
                Company::recomputeReviewStats((int) $company->id);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("Recomputed review stats for {$count} companies.");

        return self::SUCCESS;
    }
}
