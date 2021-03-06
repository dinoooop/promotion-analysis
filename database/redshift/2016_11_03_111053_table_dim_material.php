<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableDimMaterial extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('nwl_pos.dim_material', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('insert_pid');
            $table->dateTime('insert_ts');
            $table->string('update_pid');
            $table->dateTime('update_ts');
            $table->integer('item_id');
            $table->integer('retailer_country_id');
            $table->string('material_id');
            $table->string('material_description');
            $table->string('description1');
            $table->string('brand');
            $table->string('sub_brand');
            $table->string('division');
            $table->string('segment');
            $table->string('sub_segment');
            $table->string('business_team');
            $table->string('product_family');
            $table->string('product_line');
            $table->string('product_platform');
            $table->string('retailer_sku');
            $table->string('retailer_upc');
            $table->string('x_plant_matl_status');
            $table->string('x_plant_valid_from');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('nwl_pos.dim_material');
    }

}
