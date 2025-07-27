<?php


use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// return function (Schedule $schedule) {
//     Log::info('Schedule is being registered.');
//     $schedule->command('calls:process-scheduled')->everyMinute();
// };
