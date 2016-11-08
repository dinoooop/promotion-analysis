<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TablePromoPod extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('promo_pod', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('promo_id');
            $table->integer('year');
            $table->float('ordered_amount_during', 10, 2);
            $table->float('wkly_baseline', 10, 2);
            $table->float('baseline', 10, 2);
            $table->float('incremental_d', 10, 2);
            $table->float('incremental_p', 5, 2);
            $table->float('wkly_avg_ordered_amount_post_2_wks', 10, 2);
            $table->float('wkly_pull_forward_halo_d', 10, 2);
            $table->float('pull_forward_halo_d', 10, 2);
            $table->float('pull_forward_halo_p', 5, 2);
            $table->float('pos_during', 10, 2);
            $table->float('cogs_during', 10, 2);
            $table->float('ppm_p_during', 5, 2);
            $table->float('pos_during_baseline_period', 10, 2);
            $table->float('cogs_during_baseline_period', 10,2);
            $table->float('ppm_p_baseline', 5, 2);
            $table->string('ordered_qty_during');
            $table->float('investment_unit', 10, 2);
            $table->float('funding_source', 10, 2);
            $table->float('investment', 10, 2);
            $table->float('roi', 10, 2);
            $table->string('forecast_qty');
            $table->float('fcst_investment', 10, 2);
            $table->float('discount_amount', 10, 2);
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('promo_pod');
    }

}
