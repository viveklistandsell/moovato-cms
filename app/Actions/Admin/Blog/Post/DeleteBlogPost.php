<?php

declare(strict_types=1);

namespace App\Actions\Admin\Blog\Post;

use App\Models\Blog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final readonly class DeleteBlogPost
{
    public function handle(Blog $post): void
    {
        DB::transaction(function () use ($post): void {
            if ($post->image !== null) {
                Storage::disk('public')->delete($post->image);
            }

            $post->delete();
            Blog::compactAll();
        });
    }
}
