<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableRedshiftDimMaterial extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('redshift_dim_material', function (Blueprint $table) {
            $table->string('insert_pid');
            $table->string('insert_ts');
            $table->string('update_pid');
            $table->string('update_ts');
            $table->string('item_id');
            $table->string('retailer_country_id');
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
        Schema::drop('redshift_dim_material');
    }

}
