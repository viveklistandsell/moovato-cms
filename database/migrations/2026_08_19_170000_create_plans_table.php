<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Move partner plan definitions from config/plans.php into the DB
 * so admins can edit label, price, positioning, placement, features
 * and caps from the admin dashboard without touching PHP.
 *
 * The row `slug` mirrors the App\Enums\PlanTier cases (basic /
 * premium / gold) — the enum stays as the type-safe identifier
 * while the flexible data lives here. Adding a new slug requires
 * an enum change too, so the admin UI is edit-only in v1.
 *
 * We seed straight from the current config values in up(), which
 * means an existing install migrates cleanly with zero manual
 * data entry. If the config file is ever removed the seed still
 * has hard-coded fallback defaults to prevent a busted install.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $t): void {
            $t->string('slug', 32)->primary();
            $t->string('label', 100);
            $t->unsignedInteger('price')->default(0);
            $t->string('currency', 8)->default('€');
            $t->string('period', 32)->default('month');
            $t->string('positioning', 255)->nullable();
            $t->string('placement', 32)->default('standard');
            $t->json('features');
            $t->json('caps');
            $t->unsignedSmallInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        $this->seedFromConfig();
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }

    private function seedFromConfig(): void
    {
        $tiers = (array) config('plans.tiers', []);
        $currency = (string) config('plans.currency', '€');
        $period = (string) config('plans.period', 'month');

        $defaults = $this->hardcodedDefaults();
        $sortOrder = 1;

        foreach (['basic', 'premium', 'gold'] as $slug) {
            $source = $tiers[$slug] ?? $defaults[$slug];

            DB::table('plans')->insert([
                'slug' => $slug,
                'label' => (string) ($source['label'] ?? ucfirst($slug)),
                'price' => (int) ($source['price'] ?? 0),
                'currency' => $currency,
                'period' => $period,
                'positioning' => $source['positioning'] ?? null,
                'placement' => (string) ($source['placement'] ?? 'standard'),
                'features' => json_encode((array) ($source['features'] ?? $defaults[$slug]['features'])),
                'caps' => json_encode((array) ($source['caps'] ?? $defaults[$slug]['caps'])),
                'sort_order' => $sortOrder++,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function hardcodedDefaults(): array
    {
        return [
            'basic' => [
                'label' => 'Basic',
                'price' => 0,
                'positioning' => 'Free listing so every partner has a presence.',
                'placement' => 'standard',
                'features' => [
                    'short_description' => false, 'about' => false, 'founded' => false,
                    'employees' => false, 'faqs' => false, 'cover' => false,
                    'trust' => false, 'google' => false, 'reply_reviews' => false,
                ],
                'caps' => ['contacts' => 1, 'services' => 1, 'areas' => 3, 'gallery' => 3, 'faqs' => 0],
            ],
            'premium' => [
                'label' => 'Premium',
                'price' => 19,
                'positioning' => 'Standard paid tier for serious operators.',
                'placement' => 'boosted',
                'features' => [
                    'short_description' => true, 'about' => true, 'founded' => true,
                    'employees' => true, 'faqs' => true, 'cover' => true,
                    'trust' => false, 'google' => true, 'reply_reviews' => true,
                ],
                'caps' => ['contacts' => 5, 'services' => 5, 'areas' => 10, 'gallery' => 10, 'faqs' => 10],
            ],
            'gold' => [
                'label' => 'Gold',
                'price' => 49,
                'positioning' => 'Everything on, boosted placement, trust badges.',
                'placement' => 'featured',
                'features' => [
                    'short_description' => true, 'about' => true, 'founded' => true,
                    'employees' => true, 'faqs' => true, 'cover' => true,
                    'trust' => true, 'google' => true, 'reply_reviews' => true,
                ],
                'caps' => ['contacts' => null, 'services' => null, 'areas' => null, 'gallery' => null, 'faqs' => null],
            ],
        ];
    }
};
