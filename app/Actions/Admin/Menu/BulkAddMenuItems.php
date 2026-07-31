<?php

declare(strict_types=1);

namespace App\Actions\Admin\Menu;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemTranslation;
use App\Services\MenuResolver;
use Illuminate\Support\Facades\DB;

/**
 * Append several items to the end of a menu's top-level list in one
 * transaction. Used by the sidebar's "Add to Menu" buttons so picking
 * 5 pages produces one DB round-trip, not five.
 */
final readonly class BulkAddMenuItems
{
    /**
     * @param  array<int, array{
     *   link_type: string,
     *   link_id?: ?int,
     *   translations: array<int, array{lang: string, label: string, link_url?: ?string}>,
     * }>  $items
     */
    public function handle(Menu $menu, array $items): void
    {
        DB::transaction(function () use ($menu, $items): void {
            $nextOrder = (int) (MenuItem::query()
                ->where('menu_id', $menu->id)
                ->whereNull('parent_id')
                ->max('sort_order') ?? 0);

            foreach ($items as $row) {
                $nextOrder++;
                $item = MenuItem::query()->create([
                    'menu_id' => $menu->id,
                    'parent_id' => null,
                    'sort_order' => $nextOrder,
                    'link_type' => $row['link_type'],
                    'link_id' => $row['link_id'] ?? null,
                    'open_in_new_tab' => false,
                    'is_active' => true,
                ]);

                foreach ($row['translations'] as $tr) {
                    MenuItemTranslation::query()->create([
                        'menu_item_id' => $item->id,
                        'lang' => $tr['lang'],
                        'label' => $tr['label'],
                        'link_url' => $tr['link_url'] ?? null,
                    ]);
                }
            }

            MenuResolver::flush($menu->key);
        });
    }
}
