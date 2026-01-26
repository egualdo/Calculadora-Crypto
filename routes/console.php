<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('rates:update')->hourly()->runInBackground()->withoutOverlapping(5);

// Schedule::command('get:data-competitors')->monthlyOn(5, '12:10')->runInBackground()->withoutOverlapping(5);
// Schedule::command('get:make-competitor-conclusions')->monthlyOn(5, '12:15')->runInBackground()->withoutOverlapping(5);
// Schedule::command('get:make-client-conclusions')->monthlyOn(5, '12:20')->runInBackground()->withoutOverlapping(5);
// Schedule::command('get:make-versus-conclusions')->monthlyOn(5, '12:25')->runInBackground()->withoutOverlapping(5);
// Schedule::command('get:googledata')->monthlyOn(5, '12:30')->runInBackground()->withoutOverlapping(5);
// Schedule::command('make:process-pending-logs')->cron('0,30 * 4-6 * *')->withoutOverlapping(10);
// Schedule::command('test:daily-log')->daily();
