<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\HappyBirthday;

class Kernel extends ConsoleKernel {

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        HappyBirthday::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
//         $schedule->command('db_change master_input_refresh')
//                  ->everyFiveMinutes();
        $logfile = storage_path('logs/promotions.log');
        $CRON_TIME = env('CRON_TIME');
        if ($CRON_TIME == 'development') {
            $schedule->command('promo process')->everyMinute()->appendOutputTo($logfile);
            $schedule->command('promo find_items')->everyFiveMinutes()->appendOutputTo($logfile);
        } elseif ($CRON_TIME == 'production') {
            $schedule->command('promo process')->hourly()->appendOutputTo($logfile);
            $schedule->command('promo find_items')->everyFiveMinutes()->appendOutputTo($logfile);
        }

        $schedule->command('promo clear_promo_logs')->weekly()->appendOutputTo($logfile);
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands() {
        require base_path('routes/console.php');
    }

}
