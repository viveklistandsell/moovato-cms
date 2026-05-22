<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Widget;

use App\Models\Page;
use App\Models\PageWidget;
use App\Models\PageWidgetTranslation;
use App\Widgets\Registry\WidgetRegistry;
use Illuminate\Support\Facades\DB;

/**
 * Replace-all sync: the request always sends the FULL widget stack the admin
 * sees on screen. We diff against the DB, delete missing rows, upsert the rest,
 * and rewrite per-language translations so deletes propagate cleanly.
 *
 * Each incoming widget shape:
 *   - id?: int (present when updating an existing widget)
 *   - type: string
 *   - position: int
 *   - is_active?: bool
 *   - settings: array<string, mixed>
 *   - translations: array<string, array<string, mixed>>   // keyed by lang
 */
final readonly class SyncPageWidgets
{
    public function __construct(private WidgetRegistry $registry) {}

    /**
     * @param  array<int, array{
     *     id?: int|null,
     *     type: string,
     *     position?: int,
     *     is_active?: bool,
     *     settings?: array<string, mixed>,
     *     translations?: array<string, array<string, mixed>>
     * }>  $incoming
     */
    public function handle(Page $page, array $incoming): void
    {
        DB::transaction(function () use ($page, $incoming): void {
            $existingIds = $page->widgets()->pluck('id')->all();
            $keepIds = [];

            foreach (array_values($incoming) as $position => $row) {
                if (! $this->registry->has($row['type'])) {
                    continue;
                }

                $settings = $row['settings'] ?? [];
                $isActive = (bool) ($row['is_active'] ?? true);

                // Per-instance display rules. Defaults to "show on all" so
                // legacy rows + widgets without an explicit toggle stay
                // visible. css_class is trimmed to drop accidental whitespace.
                $rawVisibility = is_array($row['visibility'] ?? null) ? $row['visibility'] : [];
                $visibility = [
                    'desktop' => filter_var($rawVisibility['desktop'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'tablet' => filter_var($rawVisibility['tablet'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'mobile' => filter_var($rawVisibility['mobile'] ?? true, FILTER_VALIDATE_BOOLEAN),
                ];
                $rawCssClass = $row['css_class'] ?? null;
                $cssClass = is_string($rawCssClass) ? mb_trim($rawCssClass) : '';

                $widget = (isset($row['id']) && $row['id'] !== null)
                    ? PageWidget::query()
                        ->where('page_id', $page->id)
                        ->find($row['id'])
                    : null;

                if ($widget === null) {
                    $widget = new PageWidget();
                    $widget->page_id = $page->id;
                    $widget->type = $row['type'];
                }

                $widget->position = $position;
                $widget->settings = $settings;
                $widget->is_active = $isActive;
                $widget->visibility = $visibility;
                $widget->css_class = $cssClass !== '' ? $cssClass : null;
                $widget->save();

                $keepIds[] = $widget->id;

                $this->syncTranslations($widget, $row['translations'] ?? []);
            }

            $toDelete = array_diff($existingIds, $keepIds);
            if ($toDelete !== []) {
                PageWidget::query()->whereIn('id', $toDelete)->delete();
            }
        });
    }

    /**
     * @param  array<string, array<string, mixed>>  $translations
     */
    private function syncTranslations(PageWidget $widget, array $translations): void
    {
        $keptLangs = [];

        foreach ($translations as $lang => $data) {
            if (! is_string($lang) || $lang === '') {
                continue;
            }

            PageWidgetTranslation::query()->updateOrCreate(
                ['page_widget_id' => $widget->id, 'lang' => $lang],
                ['data' => $data],
            );

            $keptLangs[] = $lang;
        }

        PageWidgetTranslation::query()
            ->where('page_widget_id', $widget->id)
            ->whereNotIn('lang', $keptLangs === [] ? [''] : $keptLangs)
            ->delete();
    }
}
