<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('glossary_terms')->where('slug', 'minio')->delete();
    }

    public function down(): void
    {
        // The term is gone from glossary-terms.json, so there is nothing to restore.
    }
};
