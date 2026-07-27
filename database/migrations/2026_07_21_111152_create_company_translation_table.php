<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_translation', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $t->string('lang', 10)->index();
            $t->string('name', 200);
            $t->string('permalink', 200);
            $t->text('short_description')->nullable();
            $t->longText('about')->nullable();
            $t->timestamps();

            $t->unique(['company_id', 'lang']);
            $t->unique(['lang', 'permalink']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_translation');
    }
};
