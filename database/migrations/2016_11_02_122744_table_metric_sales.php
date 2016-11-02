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
            $table->string('insert_ts');
            $table->string('update_pid');
            $table->string('update_ts');
            $table->string('insert_key');
            $table->string('item_id');
            $table->string('retailer_country_id');
            $table->string('date_day');
            $table->string('channel_attribute_id');
            $table->string('pos_sales');
            $table->string('pos_units');
            $table->string('pos_shipped_cogs');
            $table->string('retailer_list_price');
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
