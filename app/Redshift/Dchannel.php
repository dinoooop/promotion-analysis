<?php

namespace App\Redshift;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Redshift\Dsales;

class Dchannel extends Model {

    protected $table = 'nwl_pos.metric_online_channel';
    public $timestamps = false;
    protected $guarded = array('id');
    protected $fillable = ['insert_pid',
        'insert_ts',
        'update_pid',
        'update_ts',
        'insert_key',
        'item_id',
        'retailer_country_id',
        'date_day',
        'channel_attribute_id',
        'unfilled_ordered_units',
        'pre_order_sales_price',
        'pre_order_amount',
        'pre_order_sales_rank',
        'pre_order_units',
        'pre_order_quantity_rank',
        'orders',
        'ordered_sales_rank',
        'ordered_units',
        'ordered_amount',
        'ordered_units_rank',
        'shipped_sales_rank',
        'shipped_units_rank',
        'cat_average_selling_price',
        'cat_perorder_sales_rank',
        'cat_perorder_units_rank',
        'cat_ordered_sales_rank',
        'cat_ordered_units_rank',
        'cat_shipped_sales_rank',
        'cat_shipped_units_rank',
        'subcat_perorder_sales_rank',
        'subcat_perorder_units_rank',
        'subcat_ordered_sales_rank',
        'subcat_ordered_units_rank',
        'subcat_shipped_sales_rank',
        'subcat_shipped_units_rank',
        'avg_customer_review',
        'conversion_percentile',
        'customer_reviews',
        'page_view_index',
        'page_view_rank',
        'unique_visitor_index',
    ];

    function generate() {

        $country_id = 3301;
        $start = '2016-01-01';
        $end = '2016-12-31';


        $sql = "SELECT * FROM nwl_pos.metric_online_channel WHERE retailer_country_id = {$country_id} AND date_day BETWEEN '{$start}' AND '{$end}'";

        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        $records = DB::connection('redshift')->select($sql);
        //$records = $this->sample_data();

        foreach ($records as $key => $value) {
            self::create($value);
        }

        echo '<br> total record is ' . count($records);
    }

}
