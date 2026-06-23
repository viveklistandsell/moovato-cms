<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit table for cookie consent choices.
 *
 * GDPR Art. 7(1): the controller must be able to *demonstrate* that the
 * data subject consented. Without a stored record we have no proof. The
 * data we keep here is deliberately pseudonymised (last IP octet zeroed,
 * user-agent hashed) so it does NOT itself contain personal data — that
 * means it can be retained indefinitely without triggering DSAR cleanup
 * or special storage limits.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cookie_consents', function (Blueprint $table): void {
            $table->id();
            $table->string('anonymized_ip', 45)->nullable();
            $table->char('user_agent_hash', 64)->nullable();
            $table->json('categories_accepted');
            $table->string('action', 16);
            $table->string('banner_version', 16)->default('v1');
            $table->string('locale', 5)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('created_at');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cookie_consents');
    }
};
