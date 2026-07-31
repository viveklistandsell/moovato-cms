<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            // Actor: nullable so anonymous/system-triggered events log too.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Request fingerprint, captured at the point of the action.
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 1000)->nullable();

            // Short verb-style identifier (e.g. user.created, role.deleted) used
            // for filtering. `description` is the human sentence shown in the UI.
            $table->string('action', 100)->index();
            $table->string('description')->nullable();

            // Polymorphic subject — the model affected by the action.
            $table->string('subject_type', 100)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->index(['subject_type', 'subject_id']);

            // Arbitrary structured payload (e.g. old/new values, role list).
            $table->json('properties')->nullable();

            // Only created_at — activity logs are immutable.
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
