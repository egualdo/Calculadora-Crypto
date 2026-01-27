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
        // \App\Console\Commands\BroadcastExchangeRates::class,   agregarlo si es necesario
    ];

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule)
    {
        // Run a single broadcast every minute. Adjust frequency as needed.
        $schedule->command('rates:serve')->everyThirtySeconds()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/scheduler.log'));

        // Update exchange rates (Binance / DolarAPI / BCV scraper) every 5 minutes
        $schedule->command('rates:update')->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/rates_update.log'));
    }
}
