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
        if (env('CRON_TIME') == 'hourly') {
            $schedule->command('promo process')->hourly()->sendOutputTo($logfile);
            $schedule->command('promo find_items')->everyFiveMinutes()->sendOutputTo($logfile);
        }else{
            $schedule->command('promo process')->everyMinute()->sendOutputTo($logfile);
            $schedule->command('promo find_items')->everyFiveMinutes()->sendOutputTo($logfile);
        }

        $schedule->command('promo clear_promo_logs')->weekly()->sendOutputTo($logfile);
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
