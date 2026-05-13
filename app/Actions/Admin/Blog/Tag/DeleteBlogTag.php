<?php

declare(strict_types=1);

namespace App\Actions\Admin\Blog\Tag;

use App\Models\BlogTag;
use Illuminate\Support\Facades\DB;

final readonly class DeleteBlogTag
{
    public function handle(BlogTag $tag): void
    {
        DB::transaction(function () use ($tag): void {
            $tag->delete();
            BlogTag::compactAll();
        });
    }
}
