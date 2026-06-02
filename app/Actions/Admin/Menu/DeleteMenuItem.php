<?php

declare(strict_types=1);

namespace App\Actions\Admin\Menu;

use App\Models\MenuItem;
use App\Services\MenuResolver;
use Illuminate\Support\Facades\DB;

final readonly class DeleteMenuItem
{
    public function handle(MenuItem $item): void
    {
        DB::transaction(function () use ($item): void {
            $menuKey = $item->menu->key;
            $item->delete();
            MenuResolver::flush($menuKey);
        });
    }
}
