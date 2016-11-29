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
        'new_segment',
        'new_sub_segment',
        'new_business_team',
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

}
