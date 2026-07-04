<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Split the service category tree into two entities:
 *
 *   service_parent_categories       — the top-level umbrellas (Umzüge,
 *                                     Spezialtransport, …). Managed on the
 *                                     dedicated "Parent Categories" admin.
 *   service_categories              — flat list of categories, each
 *                                     pinned to ONE parent via
 *                                     `parent_category_id`. Managed on the
 *                                     "Service Categories" admin.
 *
 * Data path:
 *   1. Add `parent_category_id` to `service_categories` (nullable).
 *   2. Copy every row where `parent_id IS NULL` into the new
 *      service_parent_categories table (with its translations). Track the
 *      old→new id map.
 *   3. For each remaining service_categories row, walk up the self-ref
 *      chain until we hit a former top-level row, then use its mapped
 *      parent-category id as `parent_category_id`.
 *   4. Delete the (now duplicated) top-level rows from service_categories.
 *   5. Drop the old `parent_id` FK + column.
 *   6. Make `parent_category_id` NOT NULL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_categories', function (Blueprint $table): void {
            $table->foreignId('parent_category_id')
                ->nullable()
                ->after('id')
                ->constrained('service_parent_categories')
                ->cascadeOnDelete();
        });

        $now = Carbon::now();
        $topLevel = DB::table('service_categories')->whereNull('parent_id')->get();
        $oldTopIdToNewParentId = [];

        foreach ($topLevel as $row) {
            $newParentId = DB::table('service_parent_categories')->insertGetId([
                'name' => $row->name,
                'icon' => $row->icon,
                'is_featured' => $row->is_featured,
                'is_popular' => $row->is_popular,
                'status' => $row->status,
                'sort_order' => $row->sort_order,
                'created_at' => $row->created_at ?? $now,
                'updated_at' => $row->updated_at ?? $now,
            ]);
            $oldTopIdToNewParentId[$row->id] = $newParentId;

            $translations = DB::table('service_category_translation')
                ->where('category_id', $row->id)
                ->get();
            foreach ($translations as $t) {
                DB::table('service_parent_category_translation')->insert([
                    'parent_category_id' => $newParentId,
                    'lang' => $t->lang,
                    'name' => $t->name,
                    'permalink' => $t->permalink,
                    'short_description' => $t->short_description,
                    'created_at' => $t->created_at ?? $now,
                    'updated_at' => $t->updated_at ?? $now,
                ]);
            }
        }

        // Walk each surviving row up its parent_id chain until we hit a
        // former top-level id, then map that to its new-parent id.
        $all = DB::table('service_categories')->get(['id', 'parent_id'])->keyBy('id');
        $findTopId = function (int $id) use ($all, &$findTopId): ?int {
            $current = $all->get($id);
            if ($current === null) {
                return null;
            }
            if ($current->parent_id === null) {
                return (int) $current->id;
            }

            return $findTopId((int) $current->parent_id);
        };

        foreach ($all as $row) {
            if ($row->parent_id === null) {
                // Top-level rows are being deleted below; skip.
                continue;
            }
            $topId = $findTopId((int) $row->id);
            if ($topId !== null && isset($oldTopIdToNewParentId[$topId])) {
                DB::table('service_categories')
                    ->where('id', $row->id)
                    ->update(['parent_category_id' => $oldTopIdToNewParentId[$topId]]);
            }
        }

        DB::table('service_categories')->whereNull('parent_id')->delete();

        Schema::table('service_categories', function (Blueprint $table): void {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id', 'sort_order']);
            $table->dropColumn('parent_id');
        });

        Schema::table('service_categories', function (Blueprint $table): void {
            $table->foreignId('parent_category_id')->nullable(false)->change();
            $table->index(['parent_category_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('service_categories', function (Blueprint $table): void {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('parent_category_id')
                ->constrained('service_categories')
                ->nullOnDelete();
            $table->index(['parent_id', 'sort_order']);
        });

        Schema::table('service_categories', function (Blueprint $table): void {
            $table->dropForeign(['parent_category_id']);
            $table->dropIndex(['parent_category_id', 'sort_order']);
            $table->dropColumn('parent_category_id');
        });
    }
};
