<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Category;

use App\Models\PageCategory;
use Illuminate\Support\Facades\DB;

final readonly class DeletePageCategory
{
    public function handle(PageCategory $category): void
    {
        DB::transaction(function () use ($category): void {
            $category->delete();
            PageCategory::compactAll();
        });
    }
}
