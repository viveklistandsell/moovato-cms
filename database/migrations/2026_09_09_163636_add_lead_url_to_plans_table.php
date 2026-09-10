<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add `lead_url` + `lead_label` columns to the `plans` table so the admin
 * can wire an extra "Lead" CTA on a paid tier (Gold by default) without
 * touching code. Every subscribed company on that tier picks the URL up
 * automatically — the CTA renders on their public profile.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $t): void {
            $t->string('lead_url', 2048)->nullable()->after('placement');                                        
            $t->string('lead_label', 80)->nullable()->after('lead_url');   
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $t): void {  
            $t->dropColumn(['lead_url', 'lead_label']);
        });
    }
};
