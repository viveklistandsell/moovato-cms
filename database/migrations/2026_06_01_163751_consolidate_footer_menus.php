<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Collapse the two seeded footer menus (`footer_quick_links` and
 * `footer_services`) into a single `footer` menu where top-level items
 * are column headings (rendered as <h3>, never as links) and their
 * children are the actual footer links.
 *
 * Idempotent: re-running detects the `footer` menu and skips. Down() is
 * a best-effort restore of the previous two-menu shape.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Bail if the new menu already exists — keeps the migration safe to
        // re-run after a partial failure.
        if (DB::table('menus')->where('key', 'footer')->exists()) {
            return;
        }

        $now = now();

        DB::transaction(function () use ($now): void {
            $footerId = DB::table('menus')->insertGetId([
                'key' => 'footer',
                'name' => 'Footer Menu',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $columns = [
                ['quick_links', 1, 'Quick Links', 'Schnelllinks', 'footer_quick_links'],
                ['services', 2, 'Our Services', 'Unsere Leistungen', 'footer_services'],
            ];

            $langCodes = DB::table('languages')->where('status', true)->pluck('code')->all();

            foreach ($columns as [$slug, $order, $labelEn, $labelDe, $oldKey]) {
                $headerId = DB::table('menu_items')->insertGetId([
                    'menu_id' => $footerId,
                    'parent_id' => null,
                    'sort_order' => $order,
                    'link_type' => 'url',
                    'link_id' => null,
                    'open_in_new_tab' => false,
                    'css_class' => null,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($langCodes as $code) {
                    DB::table('menu_item_translation')->insert([
                        'menu_item_id' => $headerId,
                        'lang' => $code,
                        'label' => match ($code) {
                            'de' => $labelDe,
                            default => $labelEn,
                        },
                        'link_url' => '#',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                // 3. Move every item from the old menu under this column
                //    header — including `home` type so the Home link stays
                //    available under Quick Links as it was originally seeded.
                $oldMenu = DB::table('menus')->where('key', $oldKey)->first();
                if ($oldMenu === null) {
                    continue;
                }

                $oldItems = DB::table('menu_items')
                    ->where('menu_id', $oldMenu->id)
                    ->whereNull('parent_id')
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get(['id']);

                foreach ($oldItems as $i => $row) {
                    DB::table('menu_items')
                        ->where('id', $row->id)
                        ->update([
                            'menu_id' => $footerId,
                            'parent_id' => $headerId,
                            'sort_order' => $i + 1,
                            'updated_at' => $now,
                        ]);
                }

                // 4. Drop the now-empty old menu (orphaned children, if any,
                //    are removed by the FK cascade).
                DB::table('menu_items')->where('menu_id', $oldMenu->id)->delete();
                DB::table('menus')->where('id', $oldMenu->id)->delete();
            }
        });
    }

    public function down(): void
    {
        // Best-effort revert: split the consolidated menu back into the two
        // original slots so a `migrate:rollback` doesn't leave the system
        // wedged. Children are moved back as top-level items in the
        // re-created old menus, preserving their order.
        if (! Schema::hasTable('menus')) {
            return;
        }

        $footer = DB::table('menus')->where('key', 'footer')->first();
        if ($footer === null) {
            return;
        }

        $now = now();

        DB::transaction(function () use ($footer, $now): void {
            $columnMap = [
                'quick_links' => ['key' => 'footer_quick_links', 'name' => 'Footer · Quick Links'],
                'services' => ['key' => 'footer_services', 'name' => 'Footer · Our Services'],
            ];

            // We identify which column-header item is which by the first
            // English translation label.
            $headers = DB::table('menu_items')
                ->where('menu_id', $footer->id)
                ->whereNull('parent_id')
                ->get();

            foreach ($headers as $header) {
                $label = DB::table('menu_item_translation')
                    ->where('menu_item_id', $header->id)
                    ->where('lang', 'en')
                    ->value('label') ?? '';

                $target = match (true) {
                    str_contains($label, 'Quick') => $columnMap['quick_links'],
                    str_contains($label, 'Service') => $columnMap['services'],
                    default => null,
                };
                if ($target === null) {
                    continue;
                }

                $oldMenuId = DB::table('menus')->insertGetId([
                    'key' => $target['key'],
                    'name' => $target['name'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $children = DB::table('menu_items')
                    ->where('parent_id', $header->id)
                    ->orderBy('sort_order')
                    ->get(['id']);

                foreach ($children as $i => $child) {
                    DB::table('menu_items')
                        ->where('id', $child->id)
                        ->update([
                            'menu_id' => $oldMenuId,
                            'parent_id' => null,
                            'sort_order' => $i + 1,
                            'updated_at' => $now,
                        ]);
                }

                DB::table('menu_item_translation')->where('menu_item_id', $header->id)->delete();
                DB::table('menu_items')->where('id', $header->id)->delete();
            }

            DB::table('menus')->where('id', $footer->id)->delete();
        });
    }
};
