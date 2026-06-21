<?php

use Illuminate\Support\Facades\Schedule;

// Re-scan the incoming folder every day just after midnight so any files you
// dropped get scheduled onto the next open dates automatically.
// Requires a single cron entry on the server:
//   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
Schedule::command('comics:import')->dailyAt('00:05');
