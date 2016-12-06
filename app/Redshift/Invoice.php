<?php

/**
 * 
 * 
 * Create schema nwl_sap_sales
 * Create schema nwl_pcm
 * Create nwl_sap_sales.metric_invoice_sales table similar to redshift
 * Create nwl_pcm.sap_material_additional table similar to redshift
 * 
 */

namespace App\Redshift;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Invoice {

    function __construct() {
        
    }

    function create_schema() {
        DB::statement('CREATE SCHEMA IF NOT EXISTS nwl_sap_sales');
        DB::statement('CREATE SCHEMA IF NOT EXISTS nwl_pcm');
    }

    function create_table() {
        $table_name = 'nwl_sap_sales.metric_invoice_sales';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->string('insert_ts')->nullable();
            $table->string('insert_pid')->nullable();
            $table->string('order_status')->nullable();
            $table->string('sales_area')->nullable();
            $table->string('order_id')->nullable();
            $table->string('material_number')->nullable();
            $table->string('line_number')->nullable();
            $table->string('line_transaction_code')->nullable();
            $table->string('shipto')->nullable();
            $table->string('invoice_date')->nullable();
            $table->string('entry_date')->nullable();
            $table->string('sales_region')->nullable();
            $table->string('global_region')->nullable();
            $table->string('currency')->nullable();
            $table->string('dollars_local')->nullable();
            $table->string('dollars_usd')->nullable();
            $table->string('units')->nullable();
            $table->string('standard_cost_local')->nullable();
            $table->string('standard_cost_usd')->nullable();
            $table->string('freight')->nullable();
        });
        $table_name = 'nwl_pcm.sap_material_additional';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->string('material')->nullable();
            $table->string('division')->nullable();
            $table->string('ean_upc')->nullable();
            $table->string('ean_upc_category')->nullable();
            $table->string('ean_upc_category_text')->nullable();
            $table->string('alt_uom')->nullable();
            $table->string('alt_uom_text')->nullable();
            $table->string('numerator')->nullable();
            $table->string('denominator')->nullable();
            $table->string('length')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('volume')->nullable();
            $table->string('gross_weight')->nullable();
            $table->string('weight_uom')->nullable();
            $table->string('metric_length')->nullable();
            $table->string('metric_weight')->nullable();
            $table->string('metric_height')->nullable();
            $table->string('metric_volume')->nullable();
            $table->string('metric_vol_unit')->nullable();
            $table->string('metric_gross_weight')->nullable();
            $table->string('metric_net_weight')->nullable();
            $table->string('metric_weight_uom')->nullable();
            $table->string('insert_ts')->nullable();
            $table->string('insert_pid')->nullable();
            $table->string('update_ts')->nullable();
            $table->string('update_pid')->nullable();
        });
    }

    function metric_invoice_sales_seed() {
        $material_id = 1954840;
        $total_records = 1764;
        $limit = 100;
        

        $max_page = ceil($total_records / $limit);


        for ($page_num = 0; $page_num <= $max_page; $page_num++) {
            $offset = ($limit * $page_num) + 1;

            $records = DB::connection('redshift')->select("SELECT * FROM nwl_sap_sales.metric_invoice_sales WHERE material_number={$material_id} LIMIT {$limit} OFFSET {$offset}");

            foreach ($records as $key => $record) {
                $record = (array) $record;
//                $keys = array_keys($record);
//                $values = array_values($record);
//                $implode = implode(', ', $keys);
//                DB::insert("insert into nwl_sap_sales.metric_invoice_sales ({$implode}) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", $values);
                DB::table('nwl_sap_sales.metric_invoice_sales')->insert($record);
            }
            
            echo "$page_num \t";
        }
    }

}
