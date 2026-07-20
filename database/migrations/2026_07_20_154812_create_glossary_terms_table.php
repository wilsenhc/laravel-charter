<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('glossary_terms', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('category');
            $table->json('builder_params')->nullable();
            $table->json('related');
            $table->json('translations');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('glossary_terms');
    }
};
