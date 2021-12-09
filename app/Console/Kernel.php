<?php

namespace App\Console;

use App\Console\Commands\CacheClearCommand;
use App\Console\Commands\CalculationCacheClearCommand;
use App\Console\Commands\CompaniesCacheClearCommand;
use App\Console\Commands\UpdateCompaniesCacheCommand;
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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(CompaniesCacheClearCommand::class)->weekly();
        $schedule->command(CalculationCacheClearCommand::class)->hourly();
        $schedule->command(CacheClearCommand::class)->hourly();
        $schedule->command(UpdateCompaniesCacheCommand::class)->daily();
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
