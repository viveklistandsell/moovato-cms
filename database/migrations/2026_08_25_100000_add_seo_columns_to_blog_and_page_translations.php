<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-language SEO metadata for blog posts + custom pages.
 *
 *   meta_title       – overrides the auto-generated <title> tag
 *   meta_description – <meta name="description">
 *   schema           – free-form JSON-LD blob rendered inside a
 *                      <script type="application/ld+json"> tag
 *   meta_image       – storage path (same shape as `image` cols),
 *                      populated by the MediaPicker; drives
 *                      <meta property="og:image">
 *
 * The Blog action already wrote meta_title/meta_description into
 * blog_translation on create — the write was silently dropped
 * because the columns didn't exist yet. This migration lands them
 * for real; nothing else to backfill since the writes were no-ops.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_translation', function (Blueprint $t): void {
            if (! Schema::hasColumn('blog_translation', 'meta_title')) {
                $t->string('meta_title', 255)->nullable()->after('content');
            }
            if (! Schema::hasColumn('blog_translation', 'meta_description')) {
                $t->text('meta_description')->nullable()->after('meta_title');
            }
            if (! Schema::hasColumn('blog_translation', 'schema')) {
                $t->json('schema')->nullable()->after('meta_description');
            }
            if (! Schema::hasColumn('blog_translation', 'meta_image')) {
                $t->string('meta_image', 255)->nullable()->after('schema');
            }
        });

        Schema::table('page_translation', function (Blueprint $t): void {
            if (! Schema::hasColumn('page_translation', 'meta_title')) {
                $t->string('meta_title', 255)->nullable()->after('permalink');
            }
            if (! Schema::hasColumn('page_translation', 'meta_description')) {
                $t->text('meta_description')->nullable()->after('meta_title');
            }
            if (! Schema::hasColumn('page_translation', 'schema')) {
                $t->json('schema')->nullable()->after('meta_description');
            }
            if (! Schema::hasColumn('page_translation', 'meta_image')) {
                $t->string('meta_image', 255)->nullable()->after('schema');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blog_translation', function (Blueprint $t): void {
            $t->dropColumn(['meta_title', 'meta_description', 'schema', 'meta_image']);
        });

        Schema::table('page_translation', function (Blueprint $t): void {
            $t->dropColumn(['meta_title', 'meta_description', 'schema', 'meta_image']);
        });
    }
};
