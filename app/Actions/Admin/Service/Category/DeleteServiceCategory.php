<?php

declare(strict_types=1);

namespace App\Actions\Admin\Service\Category;

use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;

final readonly class DeleteServiceCategory
{
    public function handle(ServiceCategory $category): void
    {
        DB::transaction(function () use ($category): void {
            $category->children()->update(['parent_id' => $category->parent_id]);

            $category->delete();
        });
    }
}
