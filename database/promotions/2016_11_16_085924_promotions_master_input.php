<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PromotionsMasterInput extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        Schema::create('promotions.promotions_master_input', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('promotions_name');
            $table->text('promotions_description');
            $table->date('promotions_startdate');
            $table->date('promotions_enddate');
            $table->string('retailer')->nullable();
            $table->string('retailer_country_id')->nullable();
            $table->string('retailer_country')->nullable();
            $table->string('newell_status');
            $table->string('promotions_status');
            $table->string('promotions_type');
            $table->string('level_of_promotions');
            $table->string('marketing_type');
            $table->boolean('annivarsaried')->default(0);
            $table->double('promotions_budget', 15, 8);
            $table->double('promotions_projected_sales', 15, 8);
            $table->double('promotions_expected_lift', 15, 8);
            $table->string('promotions_budget_type');
            $table->string('brand_id');
            $table->string('brand');
            $table->string('category');
            $table->string('product_family');
            $table->string('product_line');
            $table->string('division');
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
        Schema::drop('promotions.promotions_master_input');
    }

}
