<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sdcalc;
use App\Swcalc;
use App\Calendar;
use App\Merge;
use App\Redshift\Dsales;
use App\promotions\Item;
use Illuminate\Support\Facades\Log;

class Spod extends Model {

    protected $table = 'promotions.promotions_results';
    public $timestamps = false;
    protected $guarded = array('id');
    protected $fillable = [
        'promotions_id',
        'promo_child_id',
        'material_id',
        'asin',
        'rtl_id',
        'product_name',
        'daily_baseline_pos_sales',
        'daily_baseline_pos_units',
        'daily_baseline_ordered_amount',
        'daily_baseline_ordered_units',
        'daily_during_pos_sales',
        'daily_during_pos_units',
        'daily_during_ordered_amount',
        'daily_during_ordered_units',
        'daily_post_pos_sales',
        'daily_post_pos_units',
        'daily_post_ordered_amount',
        'daily_post_ordered_units',
        'during_incremental_ordered_amount',
        'during_incremental_ordered_units',
        'during_incremental_pos_sales',
        'during_incremental_pos_units',
        'post_incremental_ordered_amount',
        'post_incremental_ordered_units',
        'post_incremental_pos_sales',
        'post_incremental_pos_units',
        'during_lift_ordered_amount',
        'during_lift_ordered_units',
        'post_lift_ordered_amount',
        'post_lift_ordered_units',
        'calculated_investment_amount',
        'no_of_promotion_days',
    ];

}
