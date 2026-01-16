<?php

namespace App\Console;

use App\Console\Commands\UpdateExchangeRates;
// use App\Console\Commands\BroadcastExchangeRates;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\UpdateExchangeRates::class,
        // \App\Console\Commands\BroadcastExchangeRates::class,
    ];

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule)
    {
        // Run a single broadcast every minute. Adjust frequency as needed.
        $schedule->command('rates:serve')->everyFiveSeconds()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/scheduler_broadcast.log'));
    }
}
