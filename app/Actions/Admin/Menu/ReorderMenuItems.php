<?php

declare(strict_types=1);

namespace App\Actions\Admin\Menu;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\MenuResolver;
use Illuminate\Support\Facades\DB;

final readonly class ReorderMenuItems
{
    /**
     * Apply the new tree shape in a single transaction so the menu never
     * renders in a half-reordered state if a request races.
     *
     * @param  array<int, array{id: int, parent_id: ?int, sort_order: int}>  $items
     */
    public function handle(Menu $menu, array $items): void
    {
        DB::transaction(function () use ($menu, $items): void {
            foreach ($items as $row) {
                MenuItem::query()
                    ->where('id', $row['id'])
                    ->where('menu_id', $menu->id)
                    ->update([
                        'parent_id' => $row['parent_id'],
                        'sort_order' => $row['sort_order'],
                    ]);
            }
            MenuResolver::flush($menu->key);
        });
    }
}
