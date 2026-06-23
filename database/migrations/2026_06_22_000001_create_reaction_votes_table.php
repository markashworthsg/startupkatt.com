<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reaction_votes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('comic_id')->constrained()->cascadeOnDelete();

            // HMAC of (comic_id + voter IP) keyed by the app key — never the raw
            // IP. Lets us enforce one vote per network per strip without storing
            // anything personally identifying or correlatable across comics.
            $table->string('ip_hash', 64);

            // The voter's current pick for this comic.
            $table->string('reaction', 32);

            $table->timestamps();

            // The anti-spam floor: at most one count-bearing vote per IP per comic.
            $table->unique(['comic_id', 'ip_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reaction_votes');
    }
};
