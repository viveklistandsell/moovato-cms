<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Company Users — the auth accounts for company owners / partners.
 *
 * Fully SEPARATE from the `users` table (which powers the admin
 * dashboard). Different guard (`company`), different reset-tokens
 * table, different Vue pages, different login URL.
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

            // Company link — nullable because the linked Company row
            // is created in the SAME transaction as the CompanyUser,
            // but we want the schema to allow "detached" users for
            // future use cases (e.g. an admin creates a CompanyUser
            // and links to an existing Company row later).
            $t->foreignId('company_id')->nullable()
                ->constrained('companies')->nullOnDelete();

            // Contact fields from the register form (screenshot 1).
            $t->string('first_name', 100);
            $t->string('last_name', 100);
            $t->string('email', 190)->unique();
            $t->string('phone', 50)->nullable();

            // Auth. Password NULLABLE — a fresh pending registration
            // has no password yet; the user sets it via the "welcome,
            // set your password" email after admin approval.
            $t->string('password')->nullable();
            $t->timestamp('email_verified_at')->nullable();
            $t->rememberToken();

            // Lifecycle: pending → approved → (login enabled)
            //           pending → rejected → (login denied, kept for audit)
            //           approved → inactive → (admin can suspend later)
            $t->string('status', 20)->default('pending')->index();

            // Free-text captured during rejection so the applicant
            // sees a specific reason in the rejection email.
            $t->string('rejection_reason', 500)->nullable();

            // Audit — who processed the application + when.
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
