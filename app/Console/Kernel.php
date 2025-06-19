<?php

namespace App\Console;

use App\Console\Commands\SendLunchReminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        SendLunchReminder::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('lunch:reminder')->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
