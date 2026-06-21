<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comics', function (Blueprint $table) {
            $table->id();

            // Sequential display number (#1, #2 ...). Assigned on import.
            $table->unsignedInteger('number')->unique();

            // URL slug, e.g. /comic/the-pivot
            $table->string('slug')->unique();

            // Human-facing metadata
            $table->string('title');
            $table->text('alt_text');                 // accessibility + SEO
            $table->text('caption')->nullable();      // transcript / caption shown under the strip
            $table->text('description')->nullable();   // meta description; falls back to caption

            // Art
            $table->string('image_path');              // relative to the "public" disk
            $table->string('og_image_path')->nullable(); // optional dedicated social image
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();

            // Ingestion bookkeeping
            $table->string('file_hash', 64)->unique();  // sha256 of the source file (de-dupe)
            $table->string('original_filename');
            $table->timestamp('file_created_at');       // source file mtime — drives ordering

            // Release scheduling — one comic per calendar day
            $table->date('published_at')->unique()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comics');
    }
};
