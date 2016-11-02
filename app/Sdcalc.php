<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Merge;

class Sdcalc extends Model {

    protected $table = 'promo_date';
    protected $guarded = array('id');
    protected $fillable = [
        'item_id',
        'week',
        'quarter',
        'date',
        'pos_sales',
        'pos_qty',
        'ordered_amount',
        'ordered_units',
        'pos_shipped_cog_sold',
        'ordered_cogs',
    ];

    function calc($find, $input) {

        switch ($find) {
            case 'ordered_cogs':
                return ($input['pos_shipped_cog_sold'] / $input['pos_qty']) * $input['ordered_units'];
                break;
        }
    }

}
