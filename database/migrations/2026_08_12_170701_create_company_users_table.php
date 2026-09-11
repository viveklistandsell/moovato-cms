<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Company Users — the auth accounts for company owners / partners.
 *
 * Registration flow:
 *   1. Public POST fills this table with status='pending' + no password
 *   2. Admin sees pending row, clicks Accept → status='approved' + we
 *      email a password-set link (reused reset flow)
 *   3. User clicks link → sets password → can log in
 *
 * Rejection flow:
 *   Admin clicks Reject → status='rejected' + we email a rejection
 *   notice. Row is kept for audit; user can never log in.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_users', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('company_id')->nullable()
                ->constrained('companies')->nullOnDelete();
            $t->string('first_name', 100);
            $t->string('last_name', 100);
            $t->string('email', 190)->unique();
            $t->string('phone', 50)->nullable();
            $t->string('password')->nullable();
            $t->timestamp('email_verified_at')->nullable();
            $t->rememberToken();
            $t->string('status', 20)->default('pending')->index();
            $t->string('rejection_reason', 500)->nullable();
            $t->foreignId('reviewed_by_user_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $t->timestamp('reviewed_at')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_users');
    }
};
