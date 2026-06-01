<?php

declare(strict_types=1);

namespace App\Actions\Admin\Menu;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemTranslation;
use App\Services\MenuResolver;
use Illuminate\Support\Facades\DB;

final readonly class CreateMenuItem
{
    /**
     * @param  array{
     *   parent_id?: ?int,
     *   link_type: string,
     *   link_id?: ?int,
     *   open_in_new_tab?: bool,
     *   css_class?: ?string,
     *   is_active?: bool,
     *   translations: array<int, array{lang: string, label: string, link_url?: ?string}>,
     * }  $data
     */
    public function handle(Menu $menu, array $data): MenuItem
    {
        return DB::transaction(function () use ($menu, $data): MenuItem {
            // New items go to the end of their parent's child list.
            $sortOrder = MenuItem::query()
                ->where('menu_id', $menu->id)
                ->where('parent_id', $data['parent_id'] ?? null)
                ->max('sort_order') ?? 0;

            $item = MenuItem::query()->create([
                'menu_id' => $menu->id,
                'parent_id' => $data['parent_id'] ?? null,
                'sort_order' => $sortOrder + 1,
                'link_type' => $data['link_type'],
                'link_id' => $data['link_id'] ?? null,
                'open_in_new_tab' => $data['open_in_new_tab'] ?? false,
                'css_class' => $data['css_class'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            foreach ($data['translations'] as $row) {
                MenuItemTranslation::query()->create([
                    'menu_item_id' => $item->id,
                    'lang' => $row['lang'],
                    'label' => $row['label'],
                    'link_url' => $row['link_url'] ?? null,
                ]);
            }

            MenuResolver::flush($menu->key);

            return $item;
        });
    }
}
