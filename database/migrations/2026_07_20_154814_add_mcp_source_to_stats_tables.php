<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_stats', function (Blueprint $table) {
            $table->string('mcp_source', 50)->default('web')->after('database_driver');
        });

        Schema::table('package_stats', function (Blueprint $table) {
            $table->string('mcp_source', 50)->default('web')->after('boost_skill');
        });
    }

    public function down(): void
    {
        Schema::table('application_stats', function (Blueprint $table) {
            $table->dropColumn('mcp_source');
        });

        Schema::table('package_stats', function (Blueprint $table) {
            $table->dropColumn('mcp_source');
        });
    }
};
