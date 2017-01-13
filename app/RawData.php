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
use App\DBChange;
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
    private $mockup;
    private $dbchange;

    public function __construct() {
        $this->calendar = new Calendar;
        $this->printm = new Printm;
        $this->mockup = new Mockup;
        $this->dbchange = new DBChange;
    }

    /**
     * 
     * Execute promotion analysis calculation (Cron Job)
     */
    function process() {

        echo "Cron Name : Promotion analysis calculation \n";
        echo "Cron Start time " . date('Y-m-d H:i:s') . "\n";
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        $this->mockup->promotion_chunk();
        echo "Cron end time " . date('Y-m-d H:i:s') . "\n";
        echo "------------------------------------------------------------------\n";
    }
    
    
    /**
     * 
     * Cron job
     * Find items for category level and brand level
     */
    function find_items() {
        echo "Cron Name : Find items for category level and brand level \n";
        echo "Cron Start time " . date('Y-m-d H:i:s') . "\n";

        $this->mockup->find_items();
        echo "Cron end time " . date('Y-m-d H:i:s') . "\n";
        echo "------------------------------------------------------------------\n";
    }
    
    /**
     * 
     * Manually run a promotion with id
     * @param int $id promotion id
     */
    function run_promotion($id) {
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        $promotion = Promotion::find($id);
        if(isset($promotion->id)){
            $this->mockup->promo_specific($promotion);
        }
    }
    
    
    function set_active_all() {
        Promotion::where('status', 'processing')
                    ->update(['status' => 'active']);
    }
    
    
    
    function run_raw_code() {
        echo "Reset all non amazon promotions to active \n";
        Promotion::where('retailer', '<>' ,'Amazon')->update(['status' => 'active']);
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

    function app_reset() {
        $this->table_truncate();
        Multiple::truncate();
        $this->dbchange->master_input_refresh();
        $this->dbchange->master_input_seed();
        $this->dbchange->child_input_refresh();
        $this->dbchange->child_input_seed();
    }

    function clear_promo_logs() {
        $filename = storage_path('logs/promotions.log');
        $handle = fopen($filename, 'r+');
        ftruncate($handle, 0);
        rewind($handle);
        fclose($handle);
        echo "Logs cleared on " . date('Y-m-d H:i:s') . " \n";
    }

    function test() {
        $obj = new Spod;
        $obj->sample_test();
    }

}
