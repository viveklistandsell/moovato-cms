<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_faq_translation', function (Blueprint $t) {
            $t->id();
            $t->foreignId('faq_id')->constrained('company_faqs')->cascadeOnDelete();
            $t->string('lang', 10)->index();
            $t->string('question', 500);
            $t->text('answer');
            $t->timestamps();

            $t->unique(['faq_id', 'lang']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_faq_translation');
    }
};
