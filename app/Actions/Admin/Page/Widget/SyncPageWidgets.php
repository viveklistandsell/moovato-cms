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
