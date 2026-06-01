<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemTranslation;
use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Database\Seeder;

/**
 * Seeds the 'header' and 'footer' menu slots with starter items.
 *
 * The footer is a single menu with top-level "column heading" items —
 * SiteFooter.vue renders top-level entries as <h3> column titles and their
 * children as the actual links, so one menu drives both Quick Links and
 * Our Services columns. Re-running is idempotent: each menu's items table
 * is only populated when empty.
 */
final class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $langCodes = Language::query()
            ->where('status', true)
            ->pluck('code')
            ->all();

        $this->seedHeader($langCodes);
        $this->seedFooter($langCodes);
    }

    /**
     * @param  array<int, string>  $langCodes
     */
    private function seedHeader(array $langCodes): void
    {
        $menu = Menu::query()->firstOrCreate(
            ['key' => Menu::HEADER],
            ['name' => 'Header Menu', 'is_active' => true],
        );

        if ($menu->items()->exists()) {
            return;
        }

        $this->addHomeItem($menu, $langCodes, ['de' => 'Startseite', 'en' => 'Home']);
    }

    /**
     * @param  array<int, string>  $langCodes
     */
    private function seedFooter(array $langCodes): void
    {
        $menu = Menu::query()->firstOrCreate(
            ['key' => Menu::FOOTER],
            ['name' => 'Footer Menu', 'is_active' => true],
        );

        if ($menu->items()->exists()) {
            return;
        }

        $quickLinks = $this->addColumnHeader(
            $menu,
            $langCodes,
            sortOrder: 1,
            labels: ['de' => 'Schnelllinks', 'en' => 'Quick Links'],
        );

        // First few published categories become the Quick Links column body.
        // Admin can drop/reorder from the navigation editor.
        $categoryIds = PageCategory::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->limit(10)
            ->pluck('id', 'title')
            ->all();

        $sort = 0;
        foreach ($categoryIds as $title => $id) {
            $sort++;
            $this->addChildLink(
                $menu,
                $langCodes,
                parentId: $quickLinks->id,
                sortOrder: $sort,
                linkType: MenuItem::TYPE_CATEGORY,
                linkId: (int) $id,
                label: (string) $title,
            );
        }

        $services = $this->addColumnHeader(
            $menu,
            $langCodes,
            sortOrder: 2,
            labels: ['de' => 'Unsere Leistungen', 'en' => 'Our Services'],
        );

        $pageIds = Page::query()
            ->where('status', 'published')
            ->orderBy('id')
            ->limit(10)
            ->pluck('id', 'title')
            ->all();

        $sort = 0;
        foreach ($pageIds as $title => $id) {
            $sort++;
            $this->addChildLink(
                $menu,
                $langCodes,
                parentId: $services->id,
                sortOrder: $sort,
                linkType: MenuItem::TYPE_PAGE,
                linkId: (int) $id,
                label: (string) $title,
            );
        }
    }

    /**
     * Top-level "column header" — link_type=url + link_url='#' satisfies the
     * existing validator without a dedicated TYPE_HEADING. SiteFooter renders
     * top-level items as headings regardless of the URL.
     *
     * @param  array<int, string>  $langCodes
     * @param  array<string, string>  $labels
     */
    private function addColumnHeader(
        Menu $menu,
        array $langCodes,
        int $sortOrder,
        array $labels,
    ): MenuItem {
        $item = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'parent_id' => null,
            'sort_order' => $sortOrder,
            'link_type' => MenuItem::TYPE_URL,
            'link_id' => null,
            'open_in_new_tab' => false,
            'is_active' => true,
        ]);

        foreach ($langCodes as $code) {
            MenuItemTranslation::query()->create([
                'menu_item_id' => $item->id,
                'lang' => $code,
                'label' => $labels[$code] ?? $labels['en'] ?? reset($labels),
                'link_url' => '#',
            ]);
        }

        return $item;
    }

    /**
     * @param  array<int, string>  $langCodes
     */
    private function addChildLink(
        Menu $menu,
        array $langCodes,
        int $parentId,
        int $sortOrder,
        string $linkType,
        ?int $linkId,
        string $label,
    ): void {
        $item = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'parent_id' => $parentId,
            'sort_order' => $sortOrder,
            'link_type' => $linkType,
            'link_id' => $linkId,
            'open_in_new_tab' => false,
            'is_active' => true,
        ]);

        foreach ($langCodes as $code) {
            MenuItemTranslation::query()->create([
                'menu_item_id' => $item->id,
                'lang' => $code,
                'label' => $label,
                'link_url' => null,
            ]);
        }
    }

    /**
     * @param  array<int, string>  $langCodes
     * @param  array<string, string>  $labels
     */
    private function addHomeItem(Menu $menu, array $langCodes, array $labels): void
    {
        $home = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'parent_id' => null,
            'sort_order' => 1,
            'link_type' => MenuItem::TYPE_HOME,
            'link_id' => null,
            'open_in_new_tab' => false,
            'is_active' => true,
        ]);

        foreach ($langCodes as $code) {
            MenuItemTranslation::query()->create([
                'menu_item_id' => $home->id,
                'lang' => $code,
                'label' => $labels[$code] ?? $labels['de'] ?? $labels[array_key_first($labels)],
                'link_url' => null,
            ]);
        }
    }
}
