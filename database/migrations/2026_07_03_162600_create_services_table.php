<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        DB::table('services')->insert([
            ['name' => 'mysql'],
            ['name' => 'pgsql'],
            ['name' => 'mariadb'],
            ['name' => 'mongodb'],
            ['name' => 'redis'],
            ['name' => 'valkey'],
            ['name' => 'memcached'],
            ['name' => 'meilisearch'],
            ['name' => 'typesense'],
            ['name' => 'minio'],
            ['name' => 'rustfs'],
            ['name' => 'mailpit'],
            ['name' => 'rabbitmq'],
            ['name' => 'selenium'],
            ['name' => 'soketi'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
