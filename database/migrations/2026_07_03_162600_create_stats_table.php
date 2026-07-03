<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->string('php_version', 10);
            $table->string('starter_kit', 50);
            $table->boolean('custom_starter_kit')->default(false);
            $table->string('javascript_runtime', 50)->nullable();
            $table->string('auth_provider', 50)->nullable();
            $table->string('testing_framework', 50);
            $table->boolean('teams')->default(false);
            $table->boolean('boost')->default(false);
            $table->boolean('devcontainer')->default(false);
            $table->boolean('no_node')->default(false);
            $table->boolean('livewire_class_components')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
