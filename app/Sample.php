<?php

/**
 * 
 * For test validation
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Sdcalc;
use App\Swcalc;
use App\Calendar;
use App\Merge;
use App\Dot;
use App\Redshift\Dsales;
use App\promotions\Item;
use Illuminate\Support\Facades\Log;

class Sample extends Model {

    protected $table = 'promotions.sample_promo_test';
    protected $guarded = array('id');
    protected $fillable = [
        'promotions_id',
        'material_id',
        'asin',
        'product_name',
        'promotions_startdate',
        'promotions_enddate',
        'daily_baseline_pos_sales',
        'daily_baseline_pos_units',
        'annivarsaried',
        'status',
    ];
    public static $messages = [
        'itemcomp' => 'Given :attribute repeated for promotion',
        'eaqualafter' => 'Given :attribute must be eaqual or greater than promotions start date',
    ];

    public static function store_rules($param) {
        return [
            'promotions_id' => 'required|integer',
            'material_id' => "required_if:asin,|itemcomp:promotions_id,{$param['promotions_id']}",
            'asin' => 'required_if:material_id,',
            'promotions_startdate' => 'date',
            'promotions_enddate' => "bail|date|eaqualafter:{$param['promotions_startdate']}",
            'product_name' => 'required',
            'daily_baseline_pos_sales' => 'numeric|nullable',
            'status' => 'required|in:sleep,active,completed',
            'annivarsaried' => 'boolean',
        ];
    }

    public static function status($input) {
        $input = Dot::random_input_prepare($input);
        $validation = Validator::make($input, self::store_rules($input), self::$messages);
        if ($validation->passes()) {
            return ['status' => true, 'input' => $input];
        } else {
            return [
                'status' => false,
                'validation' => $validation,
            ];
        }
    }

}
