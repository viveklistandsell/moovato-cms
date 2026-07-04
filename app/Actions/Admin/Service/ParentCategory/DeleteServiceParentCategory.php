<?php

declare(strict_types=1);

namespace App\Actions\Admin\Service\ParentCategory;

use App\Models\ServiceParentCategory;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final readonly class DeleteServiceParentCategory
{
    /**
     * Delete a parent category. Refuses when child categories still reference
     * it — the admin has to move or delete the children first, otherwise the
     * cascadeOnDelete on service_categories.parent_category_id would silently
     * wipe every category under it.
     */
    public function handle(ServiceParentCategory $parent): void
    {
        DB::transaction(function () use ($parent): void {
            $childCount = $parent->categories()->count();
            if ($childCount > 0) {
                throw new RuntimeException(
                    "Cannot delete '{$parent->name}': {$childCount} service categor".
                    ($childCount === 1 ? 'y' : 'ies').
                    ' still belong to it. Reassign or delete them first.'
                );
            }

            $parent->delete();
        });
    }
}
