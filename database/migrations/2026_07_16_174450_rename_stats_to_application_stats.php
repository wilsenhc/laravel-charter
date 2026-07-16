<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stat_services', function (Blueprint $table) {
            $table->dropForeign(['stat_id']);
            $table->dropForeign(['service_id']);
        });

        Schema::rename('stats', 'application_stats');
        Schema::rename('services', 'application_services');
        Schema::rename('stat_services', 'application_stat_services');

        Schema::table('application_stat_services', function (Blueprint $table) {
            $table->foreign('stat_id')->references('id')->on('application_stats')->cascadeOnDelete();
            $table->foreign('service_id')->references('id')->on('application_services')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('application_stat_services', function (Blueprint $table) {
            $table->dropForeign(['stat_id']);
            $table->dropForeign(['service_id']);
        });

        Schema::rename('application_stats', 'stats');
        Schema::rename('application_services', 'services');
        Schema::rename('application_stat_services', 'stat_services');

        Schema::table('stat_services', function (Blueprint $table) {
            $table->foreign('stat_id')->references('id')->on('stats')->cascadeOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
        });
    }
};
