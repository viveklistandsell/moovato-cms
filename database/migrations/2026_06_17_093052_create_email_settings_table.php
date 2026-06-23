<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Single-row table for global mail / SMTP configuration. Kept separate from
 * site_settings deliberately — site_settings already carries ~38 columns
 * across branding, SEO, analytics, maintenance and security, and mail
 * config is its own bounded concern (likely to grow: DKIM, return-path,
 * bounce webhook URL, throttle limits, …).
 *
 * The password column is stored as a Laravel-encrypted string via the
 * model's $casts, so credentials never sit in plaintext at rest.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('mail_transport', 20)->default('smtp');
            $table->string('mail_host')->nullable();
            $table->unsignedSmallInteger('mail_port')->nullable();
            $table->string('mail_username')->nullable();
            $table->text('mail_password')->nullable();
            $table->string('mail_encryption', 10)->nullable();
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_settings');
    }
};
