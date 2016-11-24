<?php

namespace App\Redshift;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Redshift\Pgquery;

class Dmaterial extends Model {

    protected $table = 'nwl_pos.dim_material';
    public $timestamps = false;
    protected $guarded = array('id');
    protected $fillable = [
        'insert_pid',
        'insert_ts',
        'update_pid',
        'update_ts',
        'item_id',
        'retailer_country_id',
        'material_id',
        'material_description',
        'description1',
        'brand',
        'sub_brand',
        'division',
        'segment',
        'sub_segment',
        'business_team',
        'product_family',
        'product_line',
        'product_platform',
        'retailer_sku',
        'retailer_upc',
        'x_plant_matl_status',
        'x_plant_valid_from',
    ];

    public static function insert_sample_data() {

        Config::set('database.fetch', \PDO::FETCH_ASSOC);

        $material_id = 1954840;
        $sql = "SELECT * FROM nwl_pos.dim_material WHERE material_id = '{$material_id}' LIMIT 1";

        $records = DB::connection('redshift')->select($sql);

        foreach ($records as $key => $value) {
            self::create($value);
        }
        
    }

    public static function insert_sample_data_category() {

        $category = 'Car Seats';

        $records = Pgquery::get_items_category($category);

        foreach ($records as $key => $value) {
            echo $value['material_id'] . "\n";
            self::create($value);
        }
    }

    public static function refresh() {
        $table_name = 'nwl_pos.dim_material';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('insert_pid')->nullable();
            $table->dateTime('insert_ts')->nullable();
            $table->string('update_pid')->nullable();
            $table->dateTime('update_ts')->nullable();
            $table->integer('item_id')->nullable();
            $table->integer('retailer_country_id')->nullable();
            $table->string('material_id')->nullable();
            $table->string('material_description')->nullable();
            $table->string('description1')->nullable();
            $table->string('brand')->nullable();
            $table->string('sub_brand')->nullable();
            $table->string('division')->nullable();
            $table->string('segment')->nullable();
            $table->string('sub_segment')->nullable();
            $table->string('business_team')->nullable();
            $table->string('product_family')->nullable();
            $table->string('product_line')->nullable();
            $table->string('product_platform')->nullable();
            $table->string('retailer_sku')->nullable();
            $table->string('retailer_upc')->nullable();
            $table->string('x_plant_matl_status')->nullable();
            $table->string('x_plant_valid_from')->nullable();
        });
    }

    function sample_data() {
        return [
            [
                'insert_pid' => 1001,
                'insert_ts' => 253625,
                'update_pid' => 253625,
                'update_ts' => 253625,
                'item_id' => 12541,
                'retailer_country_id' => 2536,
                'material_id' => 1954840,
                'material_description' => 'hello ',
                'description1' => 'sample',
                'brand' => 'MOto',
                'sub_brand' => 'Lenovo',
                'division' => 'G',
                'segment' => 'G',
                'sub_segment' => 'G4',
                'business_team' => 'Len',
                'product_family' => 'cell',
                'product_line' => 'celphone',
                'product_platform' => 'test',
                'retailer_sku' => 'test',
                'retailer_upc' => 'test',
                'x_plant_matl_status' => 'test',
                'x_plant_valid_from' => 'test',
            ],
            [
                'insert_pid' => 1002,
                'insert_ts' => 253625,
                'update_pid' => 253625,
                'update_ts' => 253625,
                'item_id' => 12541,
                'retailer_country_id' => 2536,
                'material_id' => 25366,
                'material_description' => 'hello ',
                'description1' => 'sample',
                'brand' => 'MOto',
                'sub_brand' => 'Lenovo',
                'division' => 'G',
                'segment' => 'G',
                'sub_segment' => 'G4',
                'business_team' => 'Len',
                'product_family' => 'cell',
                'product_line' => 'celphone',
                'product_platform' => 'test',
                'retailer_sku' => 'test',
                'retailer_upc' => 'test',
                'x_plant_matl_status' => 'test',
                'x_plant_valid_from' => 'test',
            ]
        ];
    }

}
