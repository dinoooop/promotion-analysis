<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TablePromoInput extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('promo_input', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('material_id');
            $table->string('retailer_id');
            $table->string('promotions_name');
            $table->string('promotion_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('promo_description');
            $table->string('item_name');
            $table->float('investment_d', 8, 2);
            $table->integer('forecasted_units');
            $table->float('forecasted_d', 8, 2);
            $table->string('customer_name');
            $table->string('level_of_promotion');
            $table->float('discount_price_d', 8, 2);
            $table->float('discount_p', 5, 2);
            $table->string('comments');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('promo_input');
    }

}
