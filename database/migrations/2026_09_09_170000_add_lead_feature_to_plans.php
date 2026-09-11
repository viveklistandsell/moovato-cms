<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Inject the new `lead` boolean into the `features` JSON on every plan
 * row. Enabled for `gold` by default (matches the config/plans.php
 * hardcoded defaults), disabled for `basic` and `premium`. Existing
 * admin overrides that don't include the `lead` key get the safe
 * "off" default so nothing lights up unexpectedly.
 */
return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('plans')->get(['slug', 'features']);
        foreach ($rows as $row) {
            $features = json_decode((string) $row->features, true) ?: [];
            if (! array_key_exists('lead', $features)) {
                $features['lead'] = $row->slug === 'gold';
                DB::table('plans')
                    ->where('slug', $row->slug)
                    ->update(['features' => json_encode($features)]);
            }
        }
    }

    public function down(): void 
    {
        $rows = DB::table('plans')->get(['slug', 'features']);
        foreach ($rows as $row) {
            $features = json_decode((string) $row->features, true) ?: [];
            unset($features['lead']);
            DB::table('plans')
                ->where('slug', $row->slug)
                ->update(['features' => json_encode($features)]);
        }
    }
};
