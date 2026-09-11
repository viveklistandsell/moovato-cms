<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Move `reply_reviews` from an on/off feature to a numeric cap so
 * admins can grant partners a "reply to N reviews" allowance
 * instead of the current binary yes/no. Basic partners now get 10
 * free replies (lifetime); Premium and Gold stay unlimited.
 *
 * Data-only migration — schema is already flexible (features + caps
 * are JSON on the `plans` table). We update each of the 3 seeded
 * plan rows, then bust the plan registry cache so the next request
 * sees the new shape.
 *
 * Idempotent: running it twice against the same rows produces the
 * same final JSON (no-ops if the cap is already there and the
 * feature is already stripped).
 */
return new class extends Migration
{
    /**
     * Basic gets 10 lifetime replies; Premium + Gold unlimited (null).
     */
    private const NEW_CAPS = [
        'basic' => 10,
        'premium' => null,
        'gold' => null,
    ];

    public function up(): void
    {
        $plans = DB::table('plans')->whereIn('slug', array_keys(self::NEW_CAPS))->get();

        foreach ($plans as $plan) {
            $features = (array) json_decode((string) $plan->features, true) ?: [];
            $caps = (array) json_decode((string) $plan->caps, true) ?: [];

            unset($features['reply_reviews']);
            if (! array_key_exists('reply_reviews', $caps)) {
                $caps['reply_reviews'] = self::NEW_CAPS[$plan->slug];
            }

            DB::table('plans')->where('slug', $plan->slug)->update([
                'features' => json_encode($features),
                'caps' => json_encode($caps),
                'updated_at' => now(),
            ]);
        }

        Cache::forget('plans.registry.v1');
    }

    public function down(): void
    {
        $legacyFeature = [
            'basic' => false,
            'premium' => true,
            'gold' => true,
        ];

        $plans = DB::table('plans')->whereIn('slug', array_keys($legacyFeature))->get();

        foreach ($plans as $plan) {
            $features = (array) json_decode((string) $plan->features, true) ?: [];
            $caps = (array) json_decode((string) $plan->caps, true) ?: [];

            unset($caps['reply_reviews']);
            $features['reply_reviews'] = $legacyFeature[$plan->slug];

            DB::table('plans')->where('slug', $plan->slug)->update([
                'features' => json_encode($features),
                'caps' => json_encode($caps),
                'updated_at' => now(),
            ]);
        }

        Cache::forget('plans.registry.v1');
    }
};
