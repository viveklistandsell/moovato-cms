<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Data-only migration. Several widgets' checklist fields (rendered with a
 * check icon) used to store a plain array of strings. Their editors and
 * renderers now use `{title, description}` objects per item so each point
 * can carry its own rich-text description, matching the StatFeatures
 * numbered-item pattern. Existing page_widget_translation rows saved before
 * that change still hold plain strings, which no longer match `point.title`
 * in the renderer. Wrap each existing string as `{title: <string>, description: ''}`.
 *
 * Idempotent: an item that is already an object is left untouched.
 */
return new class extends Migration
{
    /** @var array<string, array<int, string>> */
    private const FIELDS_BY_TYPE = [
        'text_columns' => ['points'],
        'mission_vision' => ['panel_one_points', 'panel_two_points'],
        'dark_feature' => ['points'],
        'dark_intro' => ['points'],
        'content_collage' => ['points'],
        'promo_cta' => ['features'],
        'cta_banner' => ['points'],
        'about_experience' => ['points'],
        'media_checklist' => ['points'],
        'why_choose_media' => ['points'],
        'supporting_media' => ['points'],
        'content_style_1' => ['points'],
        'content_style_2' => ['points'],
        'team_cta' => ['points'],
        'split_media' => ['points'],
        'split_media_left' => ['points'],
    ];

    public function up(): void
    {
        $this->eachRow(function (array $data, string $field): array {
            return array_map(
                fn ($item) => is_string($item) ? ['title' => $item, 'description' => ''] : $item,
                $data[$field],
            );
        });
    }

    public function down(): void
    {
        $this->eachRow(function (array $data, string $field): array {
            return array_map(
                fn ($item) => is_array($item) ? (string) ($item['title'] ?? '') : $item,
                $data[$field],
            );
        });
    }

    private function eachRow(callable $transformField): void
    {
        foreach (self::FIELDS_BY_TYPE as $type => $fields) {
            $rows = DB::table('page_widget_translation')
                ->join('page_widgets', 'page_widgets.id', '=', 'page_widget_translation.page_widget_id')
                ->where('page_widgets.type', $type)
                ->select('page_widget_translation.id', 'page_widget_translation.data')
                ->get();

            foreach ($rows as $row) {
                $data = (array) json_decode((string) $row->data, true) ?: [];
                $changed = false;

                foreach ($fields as $field) {
                    if (! is_array($data[$field] ?? null)) {
                        continue;
                    }

                    $before = $data[$field];
                    $data[$field] = $transformField($data, $field);

                    if ($data[$field] !== $before) {
                        $changed = true;
                    }
                }

                if ($changed) {
                    DB::table('page_widget_translation')->where('id', $row->id)->update([
                        'data' => json_encode($data),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
};
