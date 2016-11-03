<?php

namespace App\Redshift;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

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

    function generate() {
        
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        
        $material_id = 1954840;
        $sql = "SELECT * FROM nwl_pos.dim_material WHERE material_id = '{$material_id}' LIMIT 1";
        
        $records = DB::connection('redshift')->select($sql);
        
        foreach ($records as $key => $value) {
            self::create($value);
        }
        
        echo "\n completed";
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
