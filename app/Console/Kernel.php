<?php

namespace App\Console;

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
        // Commands\Inspire::class,
        Commands\ScheduleMail::class,
        Commands\Aging::class,
        Commands\Tat::class,
        Commands\AutomatedSales::class,
        Commands\FailedPickup::class,
        Commands\FailedDelivery::class,
        Commands\FailedReturn::class,
        Commands\InTransit::class,
        Commands\CollectFeedback::class,
        Commands\FastbazzarOrderUpdate::class,
        Commands\TaskManager::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('email:daily')
        ->dailyAt('23:30');

        $schedule->command('command:Aging')
        ->dailyAt('23:30');

        $schedule->command('command:Tat')
        ->dailyAt('23:30');

        $schedule->command('command:automatedsales')
        ->dailyAt('23:30');

        $schedule->command('command:failedpickup')
        ->dailyAt('23:30');

        $schedule->command('command:faileddelivery')
        ->dailyAt('23:30');

        $schedule->command('command:failedreturn')
        ->dailyAt('23:30');

        $schedule->command('command:intransit')
        ->dailyAt('23:30');
        //Feedback
        $schedule->command('command:collectFeedback')
        ->dailyAt('23:45');

        $schedule->command('command:task_manage')
        ->everyMinute();
        
//        $schedule->command('fb-order:update')
//                ->everyMinute();
    }
}
