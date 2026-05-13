<?php

declare(strict_types=1);

namespace App\Actions\Admin\Blog\Category;

use App\Models\BlogCategory;
use Illuminate\Support\Facades\DB;

final readonly class DeleteBlogCategory
{
    public function handle(BlogCategory $category): void
    {
        DB::transaction(function () use ($category): void {
            // Children get their parent_id reassigned to this category's parent (or null)
            $category->children()->update(['parent_id' => $category->parent_id]);

            $category->delete();
        });
    }
}
