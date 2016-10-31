<?php

namespace App;

class Swcalc extends Eloquent {

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

}