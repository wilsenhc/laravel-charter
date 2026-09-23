<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('application_services')) {
            DB::table('application_services')->updateOrInsert(
                ['name' => 'mailtrap-local'],
                ['name' => 'mailtrap-local'],
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('application_services')) {
            DB::table('application_services')->where('name', 'mailtrap-local')->delete();
        }
    }
};
