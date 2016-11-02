<?php

namespace App\Redshift;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Dmaterial extends Model {

    protected $table = 'redshift_dim_material';
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
        $material_id = 1954840;
        $sql = "SELECT * FROM nwl_pos.dim_material WHERE material_id = '{$material_id}' LIMIT 1";
        echo $sql;
        
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        $records = DB::connection('redshift')->select($sql);
        
        foreach ($records as $key => $value) {
            self::create($value);
        }
        
    }

}
