<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Page;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\PageWidget;
use App\Models\PageWidgetTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Removes a Page and every trace of it from the system.
 *
 * Most children have `cascadeOnDelete` declared in their migrations, but
 * a subset of the FKs never actually landed on the live database (see
 * `2026_06_24_074840_repair_pages_foreign_keys.php` for the earlier
 * repair on `page_category` and `site_settings`). Rather than rely on
 * FK cascade — which silently leaves orphans on any DB where the FK
 * isn't enforced — this action deletes every child row explicitly so
 * the outcome is identical across environments.
 *
 * Handles:
 *   - page_widget_translation  (via page_widgets ids — no FK on this DB)
 *   - page_widgets
 *   - page_translation
 *   - page_category pivot      (also cascade-safe via DB, but redundant)
 *   - menu_items pointing at the page (polymorphic, no FK possible)
 *   - the image file on the `public` disk
 *   - the Page row itself
 *
 * Site-settings columns (`imprint_page_id`, `terms_page_id`,
 * `privacy_page_id`) are `nullOnDelete` and that FK IS in place — it
 * fires when the pages row goes.
 */
final readonly class DeletePage
{
    public function handle(Page $page): void
    {
        DB::transaction(function () use ($page): void {
            $pageId = $page->id;
            $imagePath = $page->image;

            $widgetIds = PageWidget::query()
                ->where('page_id', $pageId)
                ->pluck('id')
                ->all();

            if ($widgetIds !== []) {
                PageWidgetTranslation::query()
                    ->whereIn('page_widget_id', $widgetIds)
                    ->delete();
            }

            PageWidget::query()->where('page_id', $pageId)->delete();
            PageTranslation::query()->where('page_id', $pageId)->delete();

            $page->categories()->detach();

            MenuItem::query()
                ->where('link_type', 'page')
                ->where('link_id', $pageId)
                ->delete();

            $page->delete();

            if ($imagePath !== null && $imagePath !== '') {
                Storage::disk('public')->delete($imagePath);
            }
        });
    }
}
