<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('package_stats', function (Blueprint $table) {
            $table->id();
            $table->string('php_version');
            $table->boolean('config')->default(false);
            $table->boolean('routes')->default(false);
            $table->boolean('views')->default(false);
            $table->boolean('translations')->default(false);
            $table->boolean('migrations')->default(false);
            $table->boolean('assets')->default(false);
            $table->boolean('commands')->default(false);
            $table->boolean('facade')->default(false);
            $table->boolean('boost_skill')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_stats');
    }
};
