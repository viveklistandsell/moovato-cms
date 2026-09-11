<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * In-app notification feed for partner (company_user) accounts —
 * shown on the partner dashboard's "What's new" card.
 *
 * Standalone, minimalist table (not Laravel's polymorphic
 * `notifications` table) because every row is scoped to a
 * CompanyUser — no other notifiable ever writes here.
 *
 * `type` is a short slug the frontend uses to pick an icon /
 * colour (plan_updated, plan_change_approved, plan_change_rejected,
 * ...). `data` is a JSON blob with type-specific payload — e.g. for
 * `plan_updated` it holds the diff rows [{label, from, to}, ...] so
 * the dashboard card can render them without another query.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_notifications', function (Blueprint $t): void {
            $t->id();

            $t->foreignId('company_user_id')
                ->constrained('company_users')
                ->cascadeOnDelete();

            $t->string('type', 64);
            $t->string('title', 200);
            $t->text('body')->nullable();
            $t->json('data')->nullable();

            $t->timestamp('read_at')->nullable();
            $t->timestamps();

            $t->index(['company_user_id', 'read_at']);
            $t->index(['company_user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_notifications');
    }
};
