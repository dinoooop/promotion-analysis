<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableMetricSales extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('nwl_pos.metric_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('insert_pid');
            $table->dateTime('insert_ts');
            $table->string('update_pid');
            $table->dateTime('update_ts');
            $table->string('insert_key');
            $table->integer('item_id');
            $table->integer('retailer_country_id');
            $table->date('date_day');
            $table->integer('channel_attribute_id');
            $table->float('pos_sales', 10, 2);
            $table->integer('pos_units');
            $table->float('pos_shipped_cog_sold', 10, 2);
            $table->float('retailer_list_price', 10, 2);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('nwl_pos.metric_sales');
    }

}
