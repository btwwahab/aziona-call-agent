<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

return function (Schedule $schedule) {
    $schedule->command('calls:process-scheduled')->everyMinute();
};
