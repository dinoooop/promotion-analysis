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
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        $this->mockup->promotion_chunk();
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
    
    function next_commit_db_change() {
        
        Schema::dropIfExists('promo_input');
        
        Schema::dropIfExists('promo_items');
        Schema::dropIfExists('promo_date');
        Schema::dropIfExists('promo_week');
        Schema::dropIfExists('promo_pod');
    }
    
    
    function create_passe($param) {
        // Create schema nwl_sap_sales
        Schema::dropIfExists('nwl_sap_sales.metric_invoice_sales');
    }

}
