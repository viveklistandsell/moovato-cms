<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Password reset tokens for company_users.
 *
 * Mirrors the shape of the built-in `password_reset_tokens` table so
 * we can reuse Laravel's PasswordBroker under a different config
 * key (see config/auth.php passwords section).
 *
 * The same table backs BOTH flows:
 *   - "Set initial password" (fired from admin approval)
 *   - "Reset forgotten password" (fired from /partner/forgot-password)
 * Because the underlying primitive is identical: a token that grants
 * one-time password write access to a given email address.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_password_reset_tokens', function (Blueprint $t): void {
            $t->string('email')->primary();
            $t->string('token');
            $t->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_password_reset_tokens');
    }
};
