<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_widgets', function (Blueprint $table): void {
            // Per-instance display rules. `visibility` is {desktop,tablet,mobile}
            // booleans that gate which breakpoints render the widget. NULL means
            // "show on all" (the default for legacy rows that pre-date this column).
            $table->json('visibility')->nullable()->after('settings');
            // Optional CSS class added to the widget's wrapper on the public
            // frontend — lets admins layer custom styles per instance.
            $table->string('css_class', 64)->nullable()->after('visibility');
        });
    }

    public function down(): void
    {
        Schema::table('page_widgets', function (Blueprint $table): void {
            $table->dropColumn(['visibility', 'css_class']);
        });
    }
};
