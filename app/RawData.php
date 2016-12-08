<?php

namespace App;

use Illuminate\Database\Schema\Blueprint;
use App\Option;
use App\Calendar;
use App\Stock;
use App\Smaterial;
use App\Spinput;
use App\Sdcalc;
use App\Swcalc;
use App\Spod;
use App\Printm;
use App\Mockup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\promotions\Promotion;
use App\promotions\Item;
use Illuminate\Support\Facades\Schema;
use App\Redshift\Pgquery;
use App\Redshift\Dmaterial;

class RawData {

    private $calendar;
    private $sdcalc;
    private $smaterial;
    private $printm;

    public function __construct() {
        $this->calendar = new Calendar;
        $this->printm = new Printm;
        $this->mockup = new Mockup;
    }

    function process() {
        
        echo "Cron Start time " . date('Y-m-d H:i:s') . "\n";
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        $this->mockup->promotion_chunk();
        echo "Cron end time " . date('Y-m-d H:i:s') . "\n";
        echo "------------------------------------------------------------------\n";
    }

    /**
     * 
     * Refresh the user table
     */
    function refresh_table_basic() {
        $table_name = 'users';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('role');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        $users = [
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'dinoop@sparksupport.com',
                'role' => 'admin',
                'password' => bcrypt('promo2016'),
            ]
        ];

        foreach ($users as $value) {
            DB::table($table_name)->insert($value);
        }
    }

    function csv_write($list) {
        //$header[] = Stock::get_headers();
        //$list = array_merge($header, $records);

        $csv = storage_path('app/sample_02.csv');

        $fp = fopen($csv, 'a+');

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
    }

    function read_csv() {

        $csv = storage_path('app/input_01.csv');

        if (($handle = fopen($csv, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row[] = $data;
            }
            fclose($handle);
        }

        return $row;
    }

    function read_table_user_input() {
        return DB::table("user_input")->get();
    }

    function table_truncate() {
        Sdcalc::truncate();
        Swcalc::truncate();
        Spod::truncate();
        //Spinput::truncate();
    }

    function clear_promo_logs() {
        $filename = storage_path('logs/promotions.log');
        $handle = fopen($filename, 'r+');
        ftruncate($handle, 0);
        rewind($handle);
        fclose($handle);
        echo "Logs cleared on " . date('Y-m-d H:i:s') . " \n";
    }

}
