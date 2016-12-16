<?php

namespace App;

use App\promotions\Promotion;
use App\promotions\Configuration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Redshift\Dmaterial;
use App\promotions\Item;

class DBChange {

    function __construct() {
        
    }

    function master_input_refresh() {
        $table_name = 'promotions.promotions_master_input';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('promotions_name');
            $table->text('promotions_description')->nullable();
            $table->date('promotions_startdate');
            $table->date('promotions_enddate');
            $table->string('retailer');
            $table->string('retailer_country_id')->nullable();
            $table->string('retailer_country')->nullable();
            $table->string('newell_status')->nullable();
            $table->string('promotions_status')->nullable();
            $table->string('promotions_type')->nullable();
            $table->string('level_of_promotions');
            $table->string('marketing_type')->nullable();
            $table->boolean('annivarsaried')->default(0)->nullable();
            $table->double('promotions_budget', 15, 8)->nullable();
            $table->double('promotions_projected_sales', 15, 8)->nullable();
            $table->double('promotions_expected_lift', 15, 8)->nullable();
            $table->string('promotions_budget_type')->nullable();
            $table->string('brand_id')->nullable();
            $table->string('brand')->nullable();
            $table->string('category')->nullable();
            $table->string('division')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    function master_input_seed() {

        $create = [
            // Item Level Promotion example
            [
                'promotions_name' => 'Graco Black Friday',
                'promotions_description' => 'All BF products with promotions',
                'promotions_startdate' => '07/12/2016',
                'promotions_enddate' => '07/12/2016',
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
                'status' => 'active',
            ],
            // Category Level Promotion example
            [
                'promotions_name' => 'Sample test category level',
                'promotions_description' => 'sample',
                'promotions_startdate' => '2016-11-18',
                'promotions_enddate' => '2016-11-25',
                'retailer' => 'Amazon',
                'retailer_country_id' => '',
                'retailer_country' => 'US',
                'newell_status' => 'Approved',
                'promotions_status' => 'Not Started',
                'promotions_type' => 'Price Discount',
                'level_of_promotions' => 'Category',
                'marketing_type' => 'Price Promotion',
                'annivarsaried' => 0,
                'promotions_budget' => 0,
                'promotions_projected_sales' => 0,
                'promotions_expected_lift',
                'promotions_budget_type' => '',
                'brand_id' => '',
                'brand' => 'Graco',
                'category' => 'Car Seats',
                'product_family' => '',
                'product_line' => '',
                'division' => 'Baby',
                'status' => 'active',
            ],
        ];

        foreach ($create as $key => $value) {
            Promotion::create($value);
        }
    }

    function child_input_refresh() {

        $table_name = 'promotions.promotions_child_input';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('promotions_id');
            $table->string('material_id')->nullable();
            $table->string('asin')->nullable();
            $table->string('rtl_id')->nullable();
            $table->string('product_name')->nullable();
            $table->date('promotions_startdate')->nullable();
            $table->date('promotions_enddate')->nullable();
            $table->double('promotions_budget', 15, 8)->nullable();
            $table->double('promotions_projected_sales', 15, 8)->nullable();
            $table->double('promotions_expected_lift', 15, 8)->nullable();
            $table->string('x_plant_material_status')->nullable();
            $table->date('x_plant_status_date')->nullable();
            $table->string('promotions_budget_type')->nullable();
            $table->double('funding_per_unit', 15, 8)->nullable();
            $table->integer('forecasted_qty')->nullable();
            $table->double('forecasted_unit_sales', 15, 8)->nullable();
            $table->boolean('promoted')->default(0);
            $table->boolean('user_input')->default(0);
            $table->boolean('validated')->default(0);
            $table->double('percent_discount', 15, 8)->nullable();
            $table->double('price_discount', 15, 8)->nullable();
            $table->string('reference')->nullable();
        });
    }

    function child_input_seed() {
        $table_name = 'promotions.promotions_child_input';
        $records = [
            [
                'promotions_id' => 1,
                'promotions_startdate' => '07/12/2016',
                'promotions_enddate' => '07/12/2016',
                'material_id' => '1954840',
                'product_name' => '',
                'asin' => 'B01ABQBYSO',
                'rtl_id' => 'B01ABQBYSO',
                'promotions_budget',
                'promotions_projected_sales',
                'promotions_expected_lift',
                'x_plant_material_status',
                'x_plant_status_date',
                'promotions_budget_type',
                'funding_per_unit' => '42.84',
                'forecasted_qty' => 1800,
                'forecasted_unit_sales',
                'promoted',
                'user_input',
                'validated',
                'percent_discount',
                'price_discount',
                'reference',
            ]
        ];

        foreach ($records as $value) {
            Item::create($value);
        }
    }

    /**
     * Sample redshift table
     */
    function dim_material_refresh() {
        $table_name = 'nwl_pos.dim_material';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('insert_pid')->nullable();
            $table->string('insert_ts')->nullable();
            $table->string('update_pid')->nullable();
            $table->string('update_ts')->nullable();
            $table->integer('item_id')->nullable();
            $table->string('retailer_country_id')->nullable();
            $table->string('material_id')->nullable();
            $table->string('material_description')->nullable();
            $table->string('description1')->nullable();
            $table->string('brand')->nullable();
            $table->string('sub_brand')->nullable();
            $table->string('division')->nullable();
            $table->string('segment')->nullable();
            $table->string('sub_segment')->nullable();
            $table->string('business_team')->nullable();
            $table->string('product_family')->nullable();
            $table->string('product_line')->nullable();
            $table->string('product_platform')->nullable();
            $table->string('retailer_sku')->nullable();
            $table->string('retailer_upc')->nullable();
            $table->string('x_plant_matl_status')->nullable();
            $table->string('x_plant_valid_from')->nullable();
            $table->string('new_segment')->nullable();
            $table->string('new_sub_segment')->nullable();
            $table->string('new_business_team')->nullable();
        });
    }

    function dim_material_seed() {
        $records = [
            [
                'insert_pid' => '20161124061001_QQLu1',
                'insert_ts' => '2016-11-24 06:17:04',
                'update_pid' => '20161124061001_QQLu1',
                'update_ts' => '2016-11-24 06:17:04',
                'item_id' => '65413983',
                'retailer_country_id' => '3301',
                'material_id' => '1954840',
                'material_description' => 'CS NAUTILUS 80 E GO GREEN_8J201GOG',
                'description1' => 'Graco Nautilus 80 Elite 3-in-1 Harness Booster, Go Green',
                'brand' => 'GRACO',
                'sub_brand' => 'GRACO-NO SUB BRAND',
                'division' => '10',
                'segment' => 'Baby & Parenting',
                'sub_segment' => 'Baby & Parenting Essentials',
                'business_team' => 'Toddler Car Seats',
                'product_family' => 'Harnessed Booster 1/2/3',
                'product_line' => 'Nautilus 80 Elite',
                'product_platform' => 'TODDLER MOBILITY',
                'retailer_sku' => 'B01ABQBYSO',
                'retailer_upc' => '0004740613505',
                'x_plant_matl_status' => 'Active Material',
                'x_plant_valid_from' => '2015-05-22',
                'new_segment' => '',
                'new_sub_segment' => '',
                'new_business_team' => '',
            ]
        ];

        foreach ($records as $key => $record) {
            Dmaterial::create($record);
        }
    }

    function dim_retailer_channel_refresh_seed() {
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

    function multiples_csv_refresh() {
        $table_name = 'multiples_csv';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('file');
            $table->string('type');
            $table->bigInteger('start_id');
            $table->bigInteger('end_id');
            $table->timestamps();
        });
    }

    function promotions_config_refresh() {
        $table_name = 'promotions.promotions_config';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('promotions_type');
            $table->string('level_of_promotions');
            $table->string('retailer');
            $table->string('brand')->nullable();
            $table->string('division')->nullable();
            $table->string('category')->nullable();
            $table->string('sub_category')->nullable();
            $table->integer('baseline_weeks');
            $table->integer('post_weeks');
            $table->decimal('baseline_threshold', 5, 2);
            $table->timestamps();
        });
    }

    function promotions_config_seed() {
        $records = [
            [
                'promotions_type' => 'DOTD',
                'level_of_promotions' => 'Item Level',
                'retailer' => 'Amazon',
//                'brand',
//                'division',
//                'category',
//                'sub_category',
                'baseline_weeks' => 4,
                'post_weeks' => 2,
                'baseline_threshold' => 0.25,
            ]
        ];

        foreach ($records as $key => $record) {
            Configuration::create($record);
        }
    }

    function promotions_preperation_refresh() {
        $table_name = 'promotions.promotions_preperation';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('promo_child_id');
            $table->string('material_id')->nullable();
            $table->string('asin')->nullable();
            $table->string('rtl_id')->nullable();
            $table->string('product_name')->nullable();
            $table->date('week');
            $table->date('quarter');
            $table->date('date_day');
            $table->double('ordered_amount', 15, 8)->nullable();
            $table->double('ordered_units', 15, 8)->nullable();
            $table->double('pos_sales', 15, 8)->nullable();
            $table->double('pos_units', 15, 8)->nullable();
            $table->double('calculated_daily_pos_sales', 15, 8)->nullable();
            $table->double('calculated_daliy_pos_units', 15, 8)->nullable();
            $table->double('invoice_amounts', 15, 8)->nullable();
            $table->double('invoice_units', 15, 8)->nullable();
            $table->double('invoice_cost', 15, 8)->nullable();
        });
    }

    function promo_week_refresh() {
        $table_name = 'promotions.promo_week';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('promo_child_id');
            $table->date('week');
            $table->date('quarter');
            $table->double('ordered_amount', 15, 8)->nullable();
            $table->integer('ordered_units')->nullable();
            $table->double('pos_sales', 15, 8)->nullable();
            $table->integer('pos_units')->nullable();
            $table->double('quarter_ordered_amount', 15, 8)->nullable();
            $table->double('normalized_ordered_amount', 15, 8)->nullable();
            $table->double('quarter_ordered_units', 15, 8)->nullable();
            $table->double('normalized_ordered_units', 15, 8)->nullable();
            $table->double('quarter_pos_sales', 15, 8)->nullable();
            $table->double('normalized_pos_sales', 15, 8)->nullable();
            $table->double('quarter_pos_units', 15, 8)->nullable();
            $table->double('normalized_pos_units', 15, 8)->nullable();
        });
    }

    function promo_pod_refresh() {
        $table_name = 'promotions.promotions_results';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('promotions_id');
            $table->bigInteger('promo_child_id');
            $table->string('material_id')->nullable();
            $table->string('asin')->nullable();
            $table->string('rtl_id')->nullable();
            $table->string('product_name')->nullable();
            $table->double('daily_baseline_pos_sales', 15, 8)->nullable();
            $table->double('daily_baseline_pos_units', 15, 8)->nullable();
            $table->double('daily_baseline_ordered_amount', 15, 8)->nullable();
            $table->double('daily_baseline_ordered_units', 15, 8)->nullable();
            $table->double('daily_during_pos_sales', 15, 8)->nullable();
            $table->double('daily_during_pos_units', 15, 8)->nullable();
            $table->double('daily_during_ordered_amount', 15, 8)->nullable();
            $table->double('daily_during_ordered_units', 15, 8)->nullable();
            $table->double('daily_post_pos_sales', 15, 8)->nullable();
            $table->double('daily_post_pos_units', 15, 8)->nullable();
            $table->double('daily_post_ordered_amount', 15, 8)->nullable();
            $table->double('daily_post_ordered_units', 15, 8)->nullable();
            $table->double('during_incremental_ordered_amount', 15, 8)->nullable();
            $table->double('during_incremental_ordered_units', 15, 8)->nullable();
            $table->double('during_incremental_pos_sales', 15, 8)->nullable();
            $table->double('during_incremental_pos_units', 15, 8)->nullable();
            $table->double('post_incremental_ordered_amount', 15, 8)->nullable();
            $table->double('post_incremental_ordered_units', 15, 8)->nullable();
            $table->double('post_incremental_pos_sales', 15, 8)->nullable();
            $table->double('post_incremental_pos_units', 15, 8)->nullable();
            $table->double('during_lift_ordered_amount', 15, 8)->nullable();
            $table->double('during_lift_ordered_units', 15, 8)->nullable();
            $table->double('post_lift_ordered_amount', 15, 8)->nullable();
            $table->double('post_lift_ordered_units', 15, 8)->nullable();
            $table->double('calculated_investment_amount', 15, 8)->nullable();
            $table->integer('no_of_promotion_days');
        });
    }

    function sample_promo_refresh() {
        $table_name = 'promotions.sample_promo_test';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('promotions_id');
            $table->string('material_id')->nullable();
            $table->string('asin')->nullable();
            $table->string('product_name');
            $table->date('promotions_startdate')->nullable();
            $table->date('promotions_enddate')->nullable();
            $table->double('daily_baseline_pos_sales', 15, 8)->nullable();
            $table->integer('daily_baseline_pos_units')->nullable();
            $table->boolean('annivarsaried')->default(0);
            $table->string('status');
            $table->timestamps();
        });
    }

}
