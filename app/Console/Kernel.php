<?php

namespace App\Console;

use App\Console\Commands\FetchLiveGames;
use App\Console\Commands\FetchUpcomingGames;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        FetchUpcomingGames::class,
        FetchLiveGames::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('worldcup:daily-schedule')->dailyAt('06:00')->timezone('America/New_York');
        $schedule->command('worldcup:live')->everyMinute()->timezone('America/New_York')->between('6:00', '17:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
