<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TablePromoDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('promo_date', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('item_id');
            $table->date('week');
            $table->string('quarter');
            $table->date('date');
            $table->float('pos_sales', 10, 2);
            $table->integer('pos_qty');
            $table->float('ordered_amount', 10, 2);
            $table->integer('ordered_units');
            $table->float('pos_shipped_cog_sold', 10, 2);
            $table->float('ordered_cogs', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('promo_items');
    }
}
