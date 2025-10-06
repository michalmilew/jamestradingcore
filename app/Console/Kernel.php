<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\MetatraderAccountStatus;
use App\Console\Commands\SendDummyPnlNotification;
use App\Console\Commands\UpdateAccountBalances;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        MetatraderAccountStatus::class,
        SendDummyPnlNotification::class,
        \App\Console\Commands\UpdateAccountBalances::class,
        // \App\Console\Commands\FetchTradingDataCommand::class,
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('calculate:userslots')->everyFiveMinutes();
        $schedule->command('calculate:userslots')->hourlyAt(0);
        // $schedule->command('calculate:userslots')->hourly();
        // $schedule->job(new FetchTradingData)->hourly();
        $schedule->command('email:sendprofit')->daily()->at('00:00');
        $schedule->command('email:invite')->daily()->at('01:00');
        $schedule->command('check:trading-accounts')->daily()->at('02:00');
        $schedule->command('email:sendrisk')->daily()->at('03:00');
        $schedule->command('save:topuser')->daily()->at('05:00');
        
        $schedule->command('email:meta-status')->everyMinute();
        $schedule->command('accounts:cleanup')->everyMinute();
        $schedule->command('email:sendmargin')->everyTenMinutes();
        $schedule->command('accounts:update-balances')->everyMinute();
        $schedule->command('queue:work --tries=3 --max-time=30')->everyMinute();    

        $schedule->command('queue:restart')->daily();
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
