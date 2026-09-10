<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Data-only migration. The `pricing` widget used to store one `cta_label`
 * per plan inside `data.plans[].cta_label`; it now renders a single button
 * from a top-level `data.cta_label` (plan cards are injected live from
 * `plans` instead — see PageController::resolveLivePlans()). Existing
 * page_widget_translation rows saved before that change have no top-level
 * `cta_label`, so PricingRenderer's `v-if="data.cta_label"` hides the
 * button entirely on those pages. Backfill it from the old per-plan value.
 *
 * Idempotent: only rows missing `cta_label` are touched.
 */
return new class extends Migration
{
    private const DEFAULT_CTA_LABEL = 'Partner werden';

    public function up(): void
    {
        $rows = DB::table('page_widget_translation')
            ->join('page_widgets', 'page_widgets.id', '=', 'page_widget_translation.page_widget_id')
            ->where('page_widgets.type', 'pricing')
            ->select('page_widget_translation.id', 'page_widget_translation.data')
            ->get();

        foreach ($rows as $row) {
            $data = (array) json_decode((string) $row->data, true) ?: [];

            if (array_key_exists('cta_label', $data) && $data['cta_label'] !== '') {
                continue;
            }

            $data['cta_label'] = $data['plans'][0]['cta_label'] ?? self::DEFAULT_CTA_LABEL;

            DB::table('page_widget_translation')->where('id', $row->id)->update([
                'data' => json_encode($data),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        $rows = DB::table('page_widget_translation')
            ->join('page_widgets', 'page_widgets.id', '=', 'page_widget_translation.page_widget_id')
            ->where('page_widgets.type', 'pricing')
            ->select('page_widget_translation.id', 'page_widget_translation.data')
            ->get();

        foreach ($rows as $row) {
            $data = (array) json_decode((string) $row->data, true) ?: [];

            if (! isset($data['plans'])) {
                continue;
            }

            unset($data['cta_label']);

            DB::table('page_widget_translation')->where('id', $row->id)->update([
                'data' => json_encode($data),
            ]);
        }
    }
};
