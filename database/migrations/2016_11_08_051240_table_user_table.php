<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_input', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('material_id');
            $table->string('retailer_id');
            $table->string('promotions_name');
            $table->string('promotion_type');
            $table->string('start_date');
            $table->string('end_date');
            $table->string('promo_description');
            $table->string('item_name');
            $table->string('investment_d');
            $table->string('forecasted_units');
            $table->string('forecasted_d');
            $table->string('customer_name');
            $table->string('level_of_promotion');
            $table->string('discount_price_d');
            $table->string('discount_p');
            $table->string('comments');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('user_input');
    }
}
