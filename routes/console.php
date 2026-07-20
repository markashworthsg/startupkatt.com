<?php

use Illuminate\Support\Facades\Schedule;

// Re-scan the incoming folder every day just after midnight so any files you
// dropped get scheduled onto the next open dates automatically.
// Requires a single cron entry on the server:
//   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
Schedule::command('comics:import')->dailyAt('00:05');

// Push whatever went live today to the Telegram channel. Runs after the import
// so a strip dropped in overnight still goes out the same day. No-op unless
// TELEGRAM_BOT_TOKEN + TELEGRAM_CHANNEL are set; safe to run repeatedly, the
// telegram_posted_at stamp is what prevents double-posting.
Schedule::command('comics:post-telegram')->dailyAt('00:10')->withoutOverlapping();
