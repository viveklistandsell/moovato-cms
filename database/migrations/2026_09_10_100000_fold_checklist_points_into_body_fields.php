<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Data-only migration. The dedicated checklist fields (`points`/`features`,
 * rendered as a separate `<ul>`/`<ol>` block with an icon) were removed from
 * several widgets in favor of typing the list straight into the widget's
 * body/description RTE field, styled by `.mv-rte ul li::before` /
 * `.mv-rte ol li::before` in gs.css. defaultData() now seeds *new* widget
 * instances with the list already folded into that field, but that never
 * touches page_widget_translation rows saved before the change — their old
 * points/features array is simply ignored by the renderer now, so the
 * checklist silently disappeared from already-published pages. Fold each
 * row's existing points/features into its target field as an HTML list,
 * then drop the old key.
 *
 * Idempotent: a row whose points/features key is already gone (never had
 * one, or already migrated) is left untouched.
 */
return new class extends Migration
{
    /**
     * @var array<string, array{field: string, target: string, tag: string}>
     */
    private const WIDGETS = [
        'about_experience' => ['field' => 'points', 'target' => 'body', 'tag' => 'ul'],
        'content_collage' => ['field' => 'points', 'target' => 'body', 'tag' => 'ul'],
        'media_checklist' => ['field' => 'points', 'target' => 'body', 'tag' => 'ul'],
        'why_choose_media' => ['field' => 'points', 'target' => 'body', 'tag' => 'ul'],
        'supporting_media' => ['field' => 'points', 'target' => 'body', 'tag' => 'ul'],
        'content_style_1' => ['field' => 'points', 'target' => 'body', 'tag' => 'ul'],
        'content_style_2' => ['field' => 'points', 'target' => 'body', 'tag' => 'ul'],
        'split_media' => ['field' => 'points', 'target' => 'body', 'tag' => 'ul'],
        'split_media_left' => ['field' => 'points', 'target' => 'body', 'tag' => 'ul'],
        'stat_features' => ['field' => 'features', 'target' => 'body', 'tag' => 'ol'],
        'promo_cta' => ['field' => 'features', 'target' => 'description', 'tag' => 'ul'],
    ];

    public function up(): void
    {
        foreach (self::WIDGETS as $type => $config) {
            $rows = DB::table('page_widget_translation')
                ->join('page_widgets', 'page_widgets.id', '=', 'page_widget_translation.page_widget_id')
                ->where('page_widgets.type', $type)
                ->select('page_widget_translation.id', 'page_widget_translation.data')
                ->get();

            foreach ($rows as $row) {
                $data = (array) json_decode((string) $row->data, true) ?: [];

                if (! array_key_exists($config['field'], $data)) {
                    continue;
                }

                $items = is_array($data[$config['field']]) ? $data[$config['field']] : [];
                unset($data[$config['field']]);

                $listItems = '';

                foreach ($items as $item) {
                    if (! is_array($item)) {
                        continue;
                    }

                    $title = mb_trim((string) ($item['title'] ?? ''));
                    $description = mb_trim((string) ($item['description'] ?? ''));

                    if ($title === '' && $description === '') {
                        continue;
                    }

                    $titleHtml = e($title);
                    $listItems .= $description !== ''
                        ? "<li><strong>{$titleHtml}</strong> – {$description}</li>"
                        : "<li><strong>{$titleHtml}</strong></li>";
                }

                if ($listItems !== '') {
                    $existing = (string) ($data[$config['target']] ?? '');
                    $data[$config['target']] = $existing.'<'.$config['tag'].'>'.$listItems.'</'.$config['tag'].'>';
                }

                DB::table('page_widget_translation')->where('id', $row->id)->update([
                    'data' => json_encode($data),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // One-directional: once a checklist is folded into freeform body/
        // description HTML (possibly alongside pre-existing content), it
        // can't be reliably split back out into structured points/features.
    }
};
