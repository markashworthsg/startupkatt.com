<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('comic_id')->constrained()->cascadeOnDelete();

            // Reaction key: one of the keys in config('comics.reactions').
            $table->string('reaction', 32);

            // Denormalised running tally for this (comic, reaction) pair.
            $table->unsignedInteger('count')->default(0);

            $table->timestamps();

            // One row per reaction per comic.
            $table->unique(['comic_id', 'reaction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
