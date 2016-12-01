<?php

namespace App\promotions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Dot;
use App\Merge;
use App\Calendar;

class Configuration extends Model {

    protected $table = 'promotions.promotions_config';
    protected $guarded = array('id');
    protected $fillable = [
        'promotions_type',
        'level_of_promotions',
        'retailer',
        'brand',
        'division',
        'category',
        'sub_category',
        'baseline_weeks',
        'post_weeks',
        'baseline_threshold',
    ];
    public static $form_create_rules = [
        'promotions_type' => 'required',
        'level_of_promotions' => 'required',
        'retailer' => 'required|numeric',
        'baseline_weeks' => 'required',
        'post_weeks' => 'required',
        'baseline_threshold' => 'required',
    ];
    public static $form_edit_rules = [
        'promotions_type' => 'required',
        'level_of_promotions' => 'required',
        'retailer' => 'required',
        'baseline_weeks' => 'required',
        'post_weeks' => 'required',
        'baseline_threshold' => 'required',
    ];

    

    /**
     * 
     * Change not exist and unexpected values to default
     * @param type $input
     * @return type
     */
    public static function sanitize($input) {
        $sanitize = [
//            'promotions_type',
//            'level_of_promotions',
//            'retailer',
//            'brand',
//            'division',
//            'category',
//            'sub_category',
            'baseline_weeks' => Dot::sanitize_numeric('baseline_weeks', $input),
            'post_weeks' => Dot::sanitize_numeric('post_weeks', $input),
            'baseline_threshold' => Dot::sanitize_numeric('baseline_threshold', $input),
        ];

        return array_merge($input, $sanitize);
    }

    public static function status($input) {
        $validation = Validator::make($input, self::$form_create_rules);
        if ($validation->passes()) {
            $input = self::sanitize($input);
            return ['status' => true, 'input' => $input];
        } else {
            return [
                'status' => false,
                'validation' => $validation,
            ];
        }
    }

    public static function display_prepare($input) {
        $input->promotions_startdate = date('m/d/Y', strtotime($input->promotions_startdate));
        $input->promotions_enddate = date('m/d/Y', strtotime($input->promotions_enddate));
        return $input;
    }

}
