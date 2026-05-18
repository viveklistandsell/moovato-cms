<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Page;

use App\Models\Page;
use Illuminate\Support\Facades\DB;

final readonly class DeletePage
{
    public function handle(Page $page): void
    {
        DB::transaction(function () use ($page): void {
            $page->delete();
        });
    }
}
