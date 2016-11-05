<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TablePromoWeek extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('promo_week', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('promo_id');
            $table->date('week');
            $table->string('quarter');
            $table->float('pos_sales', 10, 2);
            $table->integer('pos_qty');
            $table->float('ordered_amount', 10, 2);
            $table->integer('ordered_units');
            $table->float('pos_shipped_cog_sold', 10, 2);
            $table->float('ordered_cogs', 10, 2);
            $table->float('wkly_avg_oa_quarterly', 10, 2);
            $table->float('normalized_ordered_amount', 10, 2);
            $table->float('avg_weekly_ordered_units_quarterly', 10, 2);
            $table->float('normalized_ordered_units', 10, 2);
            $table->float('normalized_ordered_cogs', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('promo_week');
    }

}
