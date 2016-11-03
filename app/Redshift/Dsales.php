<?php

namespace App\Redshift;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Dsales extends Model {

    protected $table = 'nwl_pos.metric_sales';
    //protected $connection = 'redshift';
    public $timestamps = false;
    protected $guarded = array('id');
    protected $fillable = [
        'insert_pid',
        'insert_ts',
        'update_pid',
        'update_ts',
        'insert_key',
        'item_id',
        'retailer_country_id',
        'date_day',
        'channel_attribute_id',
        'pos_sales',
        'pos_units',
        'pos_shipped_cogs',
        'retailer_list_price',
    ];

    function generate() {
        
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        
        $item_id = 65413983;
        $start = '2016-01-01';
        $end = '2016-12-31';
        
        
        $sql = "SELECT * FROM nwl_pos.metric_sales WHERE item_id = '{$item_id}' AND date_day BETWEEN '{$start}' AND '{$end}'";
        
        $records = DB::connection('redshift')->select($sql);
        
        echo "\n total records " . count($records);
        foreach ($records as $key => $value) {
            self::create($value);
        }
        echo "\n completed";
        
    }

    

}
