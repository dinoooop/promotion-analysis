<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableMetricOnlineChannel extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('nwl_pos.metric_online_channel', function (Blueprint $table) {
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
            $table->string('unfilled_ordered_units');
            $table->string('pre_order_sales_price');
            $table->string('pre_order_amount');
            $table->string('pre_order_sales_rank');
            $table->string('pre_order_units');
            $table->string('pre_order_quantity_rank');
            $table->string('orders');
            $table->string('ordered_sales_rank');
            $table->string('ordered_units');
            $table->string('ordered_amount');
            $table->string('ordered_units_rank');
            $table->string('shipped_sales_rank');
            $table->string('shipped_units_rank');
            $table->string('cat_average_selling_price');
            $table->string('cat_perorder_sales_rank');
            $table->string('cat_perorder_units_rank');
            $table->string('cat_ordered_sales_rank');
            $table->string('cat_ordered_units_rank');
            $table->string('cat_shipped_sales_rank');
            $table->string('cat_shipped_units_rank');
            $table->string('subcat_perorder_sales_rank');
            $table->string('subcat_perorder_units_rank');
            $table->string('subcat_ordered_sales_rank');
            $table->string('subcat_ordered_units_rank');
            $table->string('subcat_shipped_sales_rank');
            $table->string('subcat_shipped_units_rank');
            $table->string('avg_customer_review');
            $table->string('conversion_percentile');
            $table->string('customer_reviews');
            $table->string('page_view_index');
            $table->string('page_view_rank');
            $table->string('unique_visitor_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('nwl_pos.metric_online_channel');
    }

}
