<?php

namespace App\Console;

use App\Console\Commands\CreateUserCommand;
use App\Console\Commands\DomainDetectCommand;
use App\Console\Commands\DrawBackgroundCommand;
use App\Console\Commands\SyncDomainHitsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        CreateUserCommand::class,
        DrawBackgroundCommand::class,
        DomainDetectCommand::class,
        SyncDomainHitsCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
