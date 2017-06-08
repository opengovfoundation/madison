<?php

namespace App\Console;

use App\Models\LoginToken;
use Carbon\Carbon;
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
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\CreateUser::class,
        \App\Console\Commands\UserRole::class,
        \App\Console\Commands\SponsorCommand::class,
        \App\Console\Commands\ActivityExport::class,
        \App\Console\Commands\DatabaseBackup::class,
        \App\Console\Commands\CreateRole::class,
        \App\Console\Commands\dbUpdateSponsors::class,
        \App\Console\Commands\DatabaseClear::class,
        \App\Console\Commands\DatabaseRebuild::class,
        \App\Console\Commands\DatabaseRestore::class,
        \App\Console\Commands\SendDailyNotifications::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            LoginToken::where('expires_at', '<', Carbon::now())->delete();
        })->daily();

        // Runs at midnight
        $schedule->command('send-daily-notifications')->daily();
    }

    protected function bootstrappers()
    {
        $bootstrappers = parent::bootstrappers();

        $bootstrappers[] = 'App\Config\Bootstrap\LoadConfiguration';

        return $bootstrappers;
    }
}
