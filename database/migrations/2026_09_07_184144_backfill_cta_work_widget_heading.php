<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Data-only migration. The `cta_work` widget used to store a headline split
 * across `data.title_before` / `data.title_after` (wrapped around an inline
 * image) plus a background image; it now renders a single `data.heading`
 * and `data.subtext` on a plain glow card with no images. Existing
 * page_widget_translation rows saved before that redesign have no
 * `heading`/`subtext`, so CtaWorkRenderer's `v-if` hides them entirely.
 * Backfill `heading` from the old title parts and default `subtext`.
 *
 * Idempotent: only rows missing `heading` are touched.
 */
return new class extends Migration
{
    private const DEFAULT_SUBTEXT = 'Erhalten Sie in wenigen Minuten ein unverbindliches Angebot und starten Sie entspannt in Ihr neues Zuhause in Berlin.';

    public function up(): void
    {
        $rows = DB::table('page_widget_translation')
            ->join('page_widgets', 'page_widgets.id', '=', 'page_widget_translation.page_widget_id')
            ->where('page_widgets.type', 'cta_work')
            ->select('page_widget_translation.id', 'page_widget_translation.data')
            ->get();

        foreach ($rows as $row) {
            $data = (array) json_decode((string) $row->data, true) ?: [];

            if (array_key_exists('heading', $data) && $data['heading'] !== '') {
                continue;
            }

            $heading = mb_trim(($data['title_before'] ?? '').' '.($data['title_after'] ?? ''));
            $data['heading'] = $heading !== '' ? $heading : 'Bereit für Ihren stressfreien Umzug mit Moovato?';
            $data['subtext'] ??= self::DEFAULT_SUBTEXT;

            unset($data['title_before'], $data['title_after'], $data['inline_image_alt']);

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
            ->where('page_widgets.type', 'cta_work')
            ->select('page_widget_translation.id', 'page_widget_translation.data')
            ->get();

        foreach ($rows as $row) {
            $data = (array) json_decode((string) $row->data, true) ?: [];

            unset($data['heading'], $data['subtext']);

            DB::table('page_widget_translation')->where('id', $row->id)->update([
                'data' => json_encode($data),
            ]);
        }
    }
};
