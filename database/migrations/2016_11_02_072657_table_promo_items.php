<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TablePromoItems extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('promo_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('item_id');
            $table->string('material_id');
            $table->string('retailer_id');
            $table->text('material_description');
            $table->string('x_plant_material_status');
            $table->string('segment');
            $table->string('brand');
            $table->string('prod_platform');
            $table->string('prod_category');
            $table->string('prod_fam');
            $table->string('prod_line');
            $table->string('retailer');
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
