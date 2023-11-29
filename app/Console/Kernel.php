<?php

namespace App\Console;

use App\Console\Commands\CheckHorizonIsActiveCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (class_exists(TelescopeApplicationServiceProvider::class) && config('telescope.enabled')) {
            $schedule->command('telescope:prune')->daily();
        }

        $schedule->command('queue:work --stop-when-empty')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
