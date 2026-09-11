<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Inject the new `opening_hours` boolean into the `features` JSON on
 * every plan row. Enabled for `premium` and `gold` by default; disabled
 * for `basic`. Existing admin overrides that don't yet include the
 * `opening_hours` key get the safe "off" default so nothing changes
 * unexpectedly.
 */
return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('plans')->get(['slug', 'features']);
        foreach ($rows as $row) {
            $features = json_decode((string) $row->features, true) ?: [];
            if (! array_key_exists('opening_hours', $features)) {
                $features['opening_hours'] = in_array($row->slug, ['premium', 'gold'], true);
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
            unset($features['opening_hours']);
            DB::table('plans')
                ->where('slug', $row->slug)
                ->update(['features' => json_encode($features)]);
        }
    }
};
