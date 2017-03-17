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
        $this->item = new Item;
    }

    /**
     * 
     * Execute promotion analysis calculation (Cron Job)
     */
    function process() {

        Dot::iecho("Cron Name : Promotion analysis calculation------------------", true);
        Dot::iecho("Cron Start time " . date('Y-m-d H:i:s'), true);
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        $this->mockup->promotion_chunk();
        Dot::iecho("Cron end time " . date('Y-m-d H:i:s'), true);
        Dot::iecho("------------------------------------------------------------", true);
    }

    /**
     * 
     * Cron job
     * Find items for category level and brand level
     */
    function find_items() {
        Dot::iecho("Cron Name : Find items for category level and brand level---", true);
        Dot::iecho("Cron Start time " . date('Y-m-d H:i:s'), true);

        $this->mockup->find_items();
        Dot::iecho("Cron end time " . date('Y-m-d H:i:s'), true);
        Dot::iecho("------------------------------------------------------------", true);
    }

    /**
     * 
     * Refresh items under category/Brand
     */
    function refresh_items_under_all_promotions() {

        Promotion::whereRaw("level_of_promotions ='Category' OR level_of_promotions ='Brand'")->orderBy('id')->chunk(100, function ($promotions) {
            foreach ($promotions as $promotion) {
                if (!in_array($promotion->id, [0])) {
                    Dot::iecho("Reseting items under promotion id: {$promotion->id}");
                    
                    $this->mockup->reset_records($promotion->id);
                    Item::where('promotions_id', $promotion->id)->delete();
                    
                    $this->item->insert_items_under_promotion($promotion);
                    $this->item->set_have_child_items($promotion);
                    Promotion::update_promotion_status($promotion->id, 'active');
                }
            }
        });
    }

    /**
     * 
     * Refresh items under promotion of $id
     */
    function refresh_items_under_promotion($id) {

        $promotion = Promotion::find($id);
        if (isset($promotion->id)) {
            Dot::iecho("Reseting items under promotion id: {$promotion->id}");
            Item::where('promotions_id', $promotion->id)->delete();
            $this->item->insert_items_under_promotion($promotion);
            $this->item->set_have_child_items($promotion);
            Promotion::update_promotion_status($promotion->id, 'active');
        }
    }

    /**
     * 
     * Manually run a promotion with id
     * @param int $id promotion id
     */
    function run_promotion($id) {
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        $promotion = Promotion::find($id);
        if (isset($promotion->id)) {
            $this->mockup->promo_specific($promotion);
        }
    }

    /**
     * 
     * Manually run a promotion with id
     * @param int $id item id
     */
    function run_item($id) {
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        $item = Item::find($id);
        if (isset($item->id)) {
            $this->mockup->item_specific($item);
        }
    }
    
    /**
     * 
     * Change promotion status
     */
    function set_status_processing_active() {
        Promotion::where('status', 'processing')
                ->update(['status' => 'active']);
    }

    
    /**
     * 
     * Change promotion status
     */
    function set_status_completed_active() {
        Promotion::where('status', 'completed')
                ->update(['status' => 'active']);
    }

    function run_raw_code() {
        Dot::iecho("Reset all non amazon promotions to active");
        Promotion::where('retailer', '<>', 'Amazon')->update(['status' => 'active']);
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
        Dot::iecho("Logs cleared on " . date('Y-m-d H:i:s'));
    }

    function test() {
    }

}
