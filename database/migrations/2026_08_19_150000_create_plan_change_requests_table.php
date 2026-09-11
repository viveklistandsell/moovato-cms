<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Partners no longer flip their plan_tier directly. They open a
 * plan-change request; a Super Admin approves or rejects it, and
 * only on approval does the tier actually change (with the same
 * enforceLimits() prune on downgrade).
 *
 * from_tier / to_tier are stored as plain strings (not FKs to an
 * enum table) — the PlanTier enum lives in code, and snapshotting
 * the string keeps the audit row intelligible even if a tier is
 * renamed later.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_change_requests', function (Blueprint $t): void {
            $t->id();

            $t->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $t->foreignId('company_user_id')
                ->nullable()
                ->constrained('company_users')
                ->nullOnDelete();

            $t->string('from_tier', 20);
            $t->string('to_tier', 20);

            $t->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            $t->text('admin_note')->nullable();

            $t->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $t->timestamp('reviewed_at')->nullable();

            $t->timestamps();

            $t->index(['company_id', 'status']);
            $t->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_change_requests');
    }
};
