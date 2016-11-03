<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sdcalc;
use App\Redshift\Dsales;
use Illuminate\Support\Facades\Log;

class Swcalc extends Model {

    protected $table = 'promo_week';
    protected $guarded = array('id');
    protected $fillable = [
        'item_id',
        'pos_sales',
        'pos_qty',
        'ordered_amount',
        'ordered_units',
        'pos_shipped_cog_sold',
        'ordered_cogs',
        'wkly_avg_oa_quarterly',
        'normalized_ordered_amount',
        'avg_weekly_ordered_units_quarterly',
        'normalized_ordered_units',
        'normalized_ordered_cogs',
    ];

    function calc() {
        $test = Dsales::select()
                ->where('item_id', 65413983)
                ->sum('pos_sales');
        echo '<pre>', print_r($test), '</pre>';
        exit();
    }

}
