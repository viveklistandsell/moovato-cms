<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            // Avatar = relative storage path under disk `public` (e.g. avatars/abc.webp).
            $table->string('avatar')->nullable()->after('password');

            // Status drives the user-management filters and gates login attempts
            // (active = ok; inactive / banned / pending block access).
            $table->string('status', 16)->default('active')->index()->after('avatar');

            // Login telemetry surfaced in the user table + activity panel.
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->unsignedInteger('login_count')->default(0)->after('last_login_ip');

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropSoftDeletes();
            $table->dropColumn(['avatar', 'status', 'last_login_at', 'last_login_ip', 'login_count']);
        });
    }
};
