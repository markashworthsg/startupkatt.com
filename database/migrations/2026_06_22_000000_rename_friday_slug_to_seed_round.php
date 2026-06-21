<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Comic #1's URL slug was the placeholder filename ("friday"); rename it to
     * match its title, "Seed Round". The image lives at its own stored path, so
     * only the public URL changes (/comic/friday → /comic/seed-round).
     */
    public function up(): void
    {
        DB::table('comics')
            ->where('slug', 'friday')
            ->update(['slug' => 'seed-round']);
    }

    public function down(): void
    {
        DB::table('comics')
            ->where('slug', 'seed-round')
            ->update(['slug' => 'friday']);
    }
};
