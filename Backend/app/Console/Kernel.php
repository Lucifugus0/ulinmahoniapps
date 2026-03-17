<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Reset rental_status for rooms with expired bookings
        // Runs every 2 minutes to quickly free up rooms from expired bookings (1 hour payment deadline)
        $schedule->command('bookings:reset-expired-rooms --force')
            ->everyTwoMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        /* Multi-Tier Pricing: Extend per-date prices for daily rooms to cover next 365 days */
        $schedule->command('room-prices:extend')
            ->daily()
            ->at('00:30')
            ->withoutOverlapping()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
