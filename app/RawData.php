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

    function init($mode) {

        Config::set('database.fetch', \PDO::FETCH_ASSOC);



        switch ($mode) {

            case 'sample':
                
                $date = $this->calendar->init('2016-07-09', '2016-10-12');
                
                
                break;

            case 'process':
                $this->mockup->promotion_chunk();
                break;

            case 'csv':
                $records = $this->read_csv();
                foreach ($records as $key => $input) {
                    $this->process($input);
                }
                break;

            case 'table_truncate':
                $this->table_truncate();
                break;

            case 'refresh_master_input':
                // php artisan raw_data refresh_master_input
                $this->refresh_table_promotions_master_input();
                $this->recreate_table_promotions_master_input();
                break;

            case 'refresh_table_dim_retailer_channel':
                // php artisan raw_data refresh_table_dim_retailer_channel

                $this->refresh_table_dim_retailer_channel();
                break;

            case 'refresh_table_item':
                // php artisan raw_data refresh_table_item

                $this->refresh_table_item();
                break;

            case 'refresh_table_basic':
                // php artisan raw_data refresh_table_basic

                $this->refresh_table_basic();
                break;

            case 'print_array_key_value':
                // php artisan raw_data print_array_key_value
                $this->printm->print_array_key_value();
                break;

            case 'print_array_simple':
                // php artisan raw_data print_array_simple
                $this->printm->print_array_simple();
                break;

            case 'sample_test':
                $this->printm->sample_test();
                break;
            
            case 'db_change_next_commit':
                Option::create_table();
                break;
            
            case 'db_create_materials':
                // php artisan raw_data don_not_run_local_db_create_materials
                // Find material to test category level of promotions
                $this->db_create_materials();
                break;



            default:
                echo "Command not found \n";
        }
    }

    function refresh_table_promotions_master_input() {
        Schema::dropIfExists('promotions.promotions_master_input');
        Schema::create('promotions.promotions_master_input', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('promotions_name');
            $table->text('promotions_description')->nullable();
            $table->date('promotions_startdate');
            $table->date('promotions_enddate');
            $table->string('retailer')->nullable();
            $table->string('retailer_country_id')->nullable();
            $table->string('retailer_country')->nullable();
            $table->string('newell_status')->nullable();
            $table->string('promotions_status')->nullable();
            $table->string('promotions_type')->nullable();
            $table->string('level_of_promotions')->nullable();
            $table->string('marketing_type')->nullable();
            $table->boolean('annivarsaried')->default(0)->nullable();
            $table->double('promotions_budget', 15, 8)->nullable();
            $table->double('promotions_projected_sales', 15, 8)->nullable();
            $table->double('promotions_expected_lift', 15, 8)->nullable();
            $table->string('promotions_budget_type')->nullable();
            $table->string('brand_id')->nullable();
            $table->string('brand')->nullable();
            $table->string('category')->nullable();
//            $table->string('product_family')->nullable();
//            $table->string('product_line')->nullable();
            $table->string('division')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    function refresh_table_promotions_child_input() {
        $table_name = 'promotions.promotions_child_input';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            // @todo
            $table->timestamps();
        });
    }

    function refresh_table_dim_retailer_channel() {
        $table_name = 'nwl_pos.dim_retailer_channel';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('retailer');
        });

        DB::table($table_name)->insert(['retailer' => 'Amazone']);
        DB::table($table_name)->insert(['retailer' => 'Flipkart']);
        DB::table($table_name)->insert(['retailer' => 'Walmart']);
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

    /**
     * 
     * Back up for recreate the promotions.promotions_master_input on 16.11.2016
     * @return array
     */
    function recreate_table_promotions_master_input() {

        $create = [
            [
                'promotions_name' => 'Graco Black Friday',
                'promotions_description' => 'All BF products with promotions',
                'promotions_startdate' => '2016-11-18',
                'promotions_enddate' => '2016-11-25',
                'retailer' => 'Amazon',
                'retailer_country_id' => '',
                'retailer_country' => 'US',
                'newell_status' => 'Approved',
                'promotions_status' => 'Not Started',
                'promotions_type' => 'Price Discount',
                'level_of_promotions' => 'Item Level',
                'marketing_type' => 'Price Promotion',
                'annivarsaried' => 0,
                'promotions_budget' => 0,
                'promotions_projected_sales' => 0,
                'promotions_expected_lift',
                'promotions_budget_type' => '',
                'brand_id' => '',
                'brand' => 'Graco',
                'category' => '',
                'product_family' => '',
                'product_line' => '',
                'division' => 'Baby',
                'status' => 'Not Processed',
            ],
        ];

        foreach ($create as $key => $value) {
            Promotion::create($value);
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

    function refresh_table_item() {

        $table_name = 'promotions.promotions_child_input';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('promotions_id');
            $table->date('promotions_startdate')->nullable();
            $table->date('promotions_enddate')->nullable();
            $table->string('material_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('asin')->nullable();
            $table->string('rtl_id')->nullable();
            $table->double('promotions_budget', 15, 8)->nullable();
            $table->double('promotions_projected_sales', 15, 8)->nullable();
            $table->double('promotions_expected_lift', 15, 8)->nullable();
            $table->string('x_plant_material_status')->nullable();
            $table->date('x_plant_status_date')->nullable();
            $table->string('promotions_budget_type')->nullable();
            $table->double('funding_per_unit', 15, 8)->nullable();
            $table->bigInteger('forecaseted_qty')->nullable();
            $table->double('forecasted_unit_sales', 15, 8)->nullable();
            $table->boolean('promoted')->default(0);
            $table->boolean('user_input')->default(0);
            $table->boolean('validated')->default(0);
            $table->double('percent_discount', 15, 8)->nullable();
            $table->double('price_discount', 15, 8)->nullable();
            $table->string('reference')->nullable();
        });

//        $users = [
//            [
//                'name' => '2016-11-18',
//                'username' => '2016-11-25',
//                'email' => 'dinoop@sparksupport.com',
//                'role' => 'admin',
//                'password' => bcrypt('promo2016'),
//            ]
//        ];
//
//        foreach ($users as $value) {
//            DB::table($table_name)->insert($value);
//        }
    }
    
    
    function db_create_materials() {
        
        $category = 'Car Seats';
        
        $records = Pgquery::get_items_category($category);
        foreach ($records as $key => $value) {
            Dmaterial::create($value);
        }
        
    }

}
