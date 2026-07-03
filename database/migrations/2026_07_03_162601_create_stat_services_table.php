<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stat_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['stat_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stat_services');
    }
};
