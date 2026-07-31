<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Persistent log of every email Laravel attempted to send. Filled by the
 * LogOutgoingEmail / MarkEmailSent / MarkEmailFailed listeners.
 *
 * Body columns are LONGTEXT — keeps the table flexible for marketing-style
 * HTML emails. If retention matters, pair this table with the existing
 * media:prune-trash pattern (a scheduled command that deletes rows older
 * than N days).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('message_id', 255)->nullable()->unique();
            $table->string('mailer', 32);
            $table->string('from_address', 255);
            $table->string('from_name', 255)->nullable();
            $table->json('to_addresses');
            $table->json('cc_addresses')->nullable();
            $table->json('bcc_addresses')->nullable();
            $table->json('reply_to')->nullable();
            $table->string('subject', 500);
            $table->longText('body_html')->nullable();
            $table->longText('body_text')->nullable();
            $table->json('headers')->nullable();
            $table->unsignedSmallInteger('attachment_count')->default(0);
            $table->string('status', 16)->default('pending');
            $table->text('error')->nullable();
            $table->foreignId('triggered_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->index('created_at');
            $table->index('status');
            $table->index('triggered_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
