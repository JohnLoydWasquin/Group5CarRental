<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\SendBookingReminders::class,
    ];
    
    protected function schedule(Schedule $schedule): void
    {
        // Run the booking reminders command every hour
        $schedule->command('bookings:send-reminders')->hourly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
