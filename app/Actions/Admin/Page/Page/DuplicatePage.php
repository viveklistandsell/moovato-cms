<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Page;

use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\PageWidget;
use App\Models\PageWidgetTranslation;
use Illuminate\Support\Facades\DB;

/**
 * Clones a page along with its translations, category pivots, and widget tree.
 * The new copy is forced to status=draft and is_home=false so the duplicate
 * never accidentally goes live or hijacks the homepage slot. Permalinks are
 * suffixed with "-copy" (and "-copy-2", "-copy-3" … on collision) per
 * translation row, since each lang has its own uniqueness constraint.
 */
final readonly class DuplicatePage
{
    public function handle(Page $source): Page
    {
        return DB::transaction(function () use ($source): Page {
            $source->loadMissing(['translations', 'categories', 'widgets.translations']);

            $copy = Page::query()->create([
                'title' => $source->title.' (copy)',
                'permalink' => $this->uniquePagePermalink($source->permalink),
                'image' => $source->image,
                'template' => $source->template,
                'is_home' => false,
                'user_id' => $source->user_id,
                'status' => 'draft',
            ]);

            foreach ($source->translations as $tr) {
                PageTranslation::query()->create([
                    'page_id' => $copy->id,
                    'lang' => $tr->lang,
                    'title' => $tr->title.' (copy)',
                    'permalink' => $this->uniqueTranslationPermalink($tr->permalink, $tr->lang),
                ]);
            }

            if ($source->categories->isNotEmpty()) {
                $sync = [];
                foreach ($source->categories as $i => $cat) {
                    $sync[$cat->id] = ['is_primary' => $i === 0];
                }
                $copy->categories()->sync($sync);
            }

            foreach ($source->widgets as $widget) {
                $newWidget = PageWidget::query()->create([
                    'page_id' => $copy->id,
                    'type' => $widget->type,
                    'position' => $widget->position,
                    'settings' => $widget->settings,
                    'is_active' => $widget->is_active,
                    'visibility' => $widget->visibility,
                    'css_class' => $widget->css_class,
                ]);

                foreach ($widget->translations as $wtr) {
                    PageWidgetTranslation::query()->create([
                        'page_widget_id' => $newWidget->id,
                        'lang' => $wtr->lang,
                        'data' => $wtr->data,
                    ]);
                }
            }

            return $copy->load(['translations', 'categories', 'widgets.translations']);
        });
    }

    private function uniquePagePermalink(string $base): string
    {
        $candidate = $base.'-copy';
        $n = 2;
        while (Page::query()->where('permalink', $candidate)->exists()) {
            $candidate = $base.'-copy-'.$n;
            $n++;
        }

        return $candidate;
    }

    private function uniqueTranslationPermalink(string $base, string $lang): string
    {
        $candidate = $base.'-copy';
        $n = 2;
        while (
            PageTranslation::query()
                ->where('lang', $lang)
                ->where('permalink', $candidate)
                ->exists()
        ) {
            $candidate = $base.'-copy-'.$n;
            $n++;
        }

        return $candidate;
    }
}
