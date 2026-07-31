<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Navigation;

use App\Actions\Admin\Menu\BulkAddMenuItems;
use App\Actions\Admin\Menu\CreateMenuItem;
use App\Actions\Admin\Menu\DeleteMenuItem;
use App\Actions\Admin\Menu\ReorderMenuItems;
use App\Actions\Admin\Menu\UpdateMenuItem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Menu\BulkAddMenuItemsRequest;
use App\Http\Requests\Admin\Menu\ReorderMenuItemsRequest;
use App\Http\Requests\Admin\Menu\StoreMenuItemRequest;
use App\Http\Requests\Admin\Menu\UpdateMenuItemRequest;
use App\Models\Language;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemTranslation;
use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Navigation management. A 'menu' is a named slot (header/footer/etc.);
 * each slot owns a tree of items the admin can drag-and-drop to reorder.
 * The frontend resolves these to per-locale links via MenuResolver.
 */
final class MenuController extends Controller
{
    /**
     * Lists all menus so the admin can click into one. Initially only the
     * 'header' slot is seeded, but the page renders any rows present so
     * adding 'footer' later doesn't require a UI change.
     */
    public function index(): Response
    {
        $menus = Menu::query()
            ->orderBy('id')
            ->withCount('items')
            ->get()
            ->map(fn (Menu $m): array => [
                'id' => $m->id,
                'key' => $m->key,
                'name' => $m->name,
                'is_active' => $m->is_active,
                'item_count' => $m->items_count,
            ])
            ->all();

        return Inertia::render('admin/menus/Index', [
            'menus' => $menus,
        ]);
    }

    /**
     * The tree editor for a single menu — bound by the 'key' column rather
     * than the numeric ID so the URL is human-readable ('header', 'footer').
     */
    public function edit(Menu $menu): Response
    {
        $menu->load([
            'items.translations',
            'items.linkedPage:id,title',
            'items.linkedCategory:id,title',
        ]);

        return Inertia::render('admin/menus/Edit', [
            'menu' => [
                'id' => $menu->id,
                'key' => $menu->key,
                'name' => $menu->name,
                'is_active' => $menu->is_active,
            ],
            'items' => $menu->items->map(fn (MenuItem $i): array => $this->presentItem($i))->all(),
            'languages' => $this->presentLanguages(),
            'pageOptions' => fn (): array => $this->pageOptions(),
            'categoryOptions' => fn (): array => $this->categoryOptions(),
        ]);
    }

    public function storeItem(StoreMenuItemRequest $request, Menu $menu, CreateMenuItem $action): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $action->handle($menu, $data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Item added.']);
    }

    public function bulkAddItems(BulkAddMenuItemsRequest $request, Menu $menu, BulkAddMenuItems $action): RedirectResponse
    {
        /** @var array{items: array<int, array{link_type: string, link_id?: ?int, translations: array<int, array{lang: string, label: string, link_url?: ?string}>}>} $data */
        $data = $request->validated();
        $action->handle($menu, $data['items']);

        $count = count($data['items']);

        return back()->with('toast', [
            'type' => 'success',
            'message' => "{$count} item".($count === 1 ? '' : 's').' added.',
        ]);
    }

    public function updateItem(
        UpdateMenuItemRequest $request,
        Menu $menu,
        MenuItem $item,
        UpdateMenuItem $action,
    ): RedirectResponse {
        abort_unless($item->menu_id === $menu->id, 404);

        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $action->handle($item, $data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Item updated.']);
    }

    public function destroyItem(Menu $menu, MenuItem $item, DeleteMenuItem $action): RedirectResponse
    {
        abort_unless($item->menu_id === $menu->id, 404);
        $action->handle($item);

        return back()->with('toast', ['type' => 'success', 'message' => 'Item deleted.']);
    }

    public function reorder(ReorderMenuItemsRequest $request, Menu $menu, ReorderMenuItems $action): RedirectResponse
    {
        /** @var array{items: array<int, array{id: int, parent_id: ?int, sort_order: int}>} $data */
        $data = $request->validated();
        $action->handle($menu, $data['items']);

        return back()->with('toast', ['type' => 'success', 'message' => 'Order updated.']);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentItem(MenuItem $item): array
    {
        return [
            'id' => $item->id,
            'menu_id' => $item->menu_id,
            'parent_id' => $item->parent_id,
            'sort_order' => $item->sort_order,
            'link_type' => $item->link_type,
            'link_id' => $item->link_id,
            'open_in_new_tab' => $item->open_in_new_tab,
            'css_class' => $item->css_class,
            'is_active' => $item->is_active,
            'linked_title' => match ($item->link_type) {
                MenuItem::TYPE_PAGE => $item->linkedPage?->title,
                MenuItem::TYPE_CATEGORY => $item->linkedCategory?->title,
                default => null,
            },
            'translations' => $item->translations
                ->mapWithKeys(fn (MenuItemTranslation $t): array => [
                    $t->lang => [
                        'label' => $t->label,
                        'link_url' => $t->link_url,
                    ],
                ])->all(),
        ];
    }

    /**
     * @return array<int, array{code: string, name: string, native_name: string, is_default: bool}>
     */
    private function presentLanguages(): array
    {
        return Language::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Language $lang): array => [
                'code' => $lang->code,
                'name' => $lang->name,
                'native_name' => $lang->native_name,
                'is_default' => (bool) $lang->lang_is_default,
            ])
            ->values()
            ->all();
    }

    /**
     * Page options include per-language translation titles so the sidebar
     * "Add to Menu" can populate sensible default labels in every active
     * language without an extra round-trip.
     *
     * @return array<int, array{id: int, title: string, translations: array<string, string>}>
     */
    private function pageOptions(): array
    {
        return Page::query()
            ->where('status', 'published')
            ->with('translations:id,page_id,lang,title')
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(fn (Page $p): array => [
                'id' => $p->id,
                'title' => $p->title,
                'translations' => $p->translations
                    ->mapWithKeys(fn ($t): array => [$t->lang => $t->title])
                    ->all(),
            ])
            ->all();
    }

    /**
     * @return array<int, array{id: int, title: string, translations: array<string, string>}>
     */
    private function categoryOptions(): array
    {
        return PageCategory::query()
            ->where('status', 'published')
            ->with('translations:id,category_id,lang,title')
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(fn (PageCategory $c): array => [
                'id' => $c->id,
                'title' => $c->title,
                'translations' => $c->translations
                    ->mapWithKeys(fn ($t): array => [$t->lang => $t->title])
                    ->all(),
            ])
            ->all();
    }
}
