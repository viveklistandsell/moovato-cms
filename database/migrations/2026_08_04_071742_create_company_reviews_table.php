<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_reviews', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('author_name', 120);
            $t->string('author_email', 190);
            $t->string('author_initials', 8);
            $t->boolean('is_anonymous')->default(false);
            $t->unsignedTinyInteger('rating');
            $t->text('body');
            $t->json('advantages')->nullable();
            $t->json('disadvantages')->nullable();
            $t->string('source', 40)->nullable();
            $t->string('proof_document', 255)->nullable();
            $t->string('status', 20)->default('published');
            $t->text('reply_body')->nullable();
            $t->timestamp('replied_at')->nullable();
            $t->foreignId('reply_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->unsignedInteger('helpful_count')->default(0);
            $t->string('ip_address', 45)->nullable();
            $t->string('user_agent', 255)->nullable();
            $t->timestamp('email_verified_at')->nullable();
            $t->timestamp('published_at')->nullable();
            $t->timestamps();
            $t->index(['company_id', 'status', 'published_at']);
            $t->index(['status', 'created_at']);
            $t->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_reviews');
    }
};
