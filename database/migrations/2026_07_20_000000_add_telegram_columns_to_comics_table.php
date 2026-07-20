<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comics', function (Blueprint $table) {
            // When this strip was pushed to the Telegram channel. Null means
            // "never posted", and it is the sole guard against double-posting,
            // so the command is safe to run as often as the scheduler likes.
            $table->timestamp('telegram_posted_at')->nullable()->after('published_at');

            // Telegram's id for the resulting channel message. Not used yet;
            // kept so a future command can edit or delete a post it made.
            $table->unsignedBigInteger('telegram_message_id')->nullable()->after('telegram_posted_at');
        });
    }

    public function down(): void
    {
        Schema::table('comics', function (Blueprint $table) {
            $table->dropColumn(['telegram_posted_at', 'telegram_message_id']);
        });
    }
};
