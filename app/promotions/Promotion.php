<?php

namespace App\promotions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Dot;
use App\Merge;
use App\Calendar;

class Promotion extends Model {

    protected $table = 'promotions.promotions_master_input';
    protected $guarded = array('id');
    protected $fillable = [
        'promotions_name',
        'promotions_description',
        'promotions_startdate',
        'promotions_enddate',
        'retailer',
        'retailer_country_id',
        'retailer_country',
        'newell_status',
        'promotions_status',
        'promotions_type',
        'level_of_promotions',
        'marketing_type',
        'annivarsaried',
        'promotions_budget',
        'promotions_projected_sales',
        'promotions_expected_lift',
        'promotions_budget_type',
        'brand_id',
        'brand',
        'category',
//        'product_family',
//        'product_line',
        'division',
        'status'
    ];
    protected $csv = [
        'promotions_name',
        'promotions_description',
        'promotions_startdate',
        'promotions_enddate',
        'retailer',
        'retailer_country_id',
        'retailer_country',
        'newell_status',
        'promotions_status',
        'promotions_type',
        'level_of_promotions',
        'marketing_type',
        'annivarsaried',
        'promotions_budget',
        'promotions_projected_sales',
        'promotions_expected_lift',
        'promotions_budget_type',
        'brand_id',
        'brand',
        'category',
        'division',
    ];
    private $merge;
    private $calendar;
    public $data;
    public $promo_id;
    public static $form_create_rules = [
        'promotions_name' => 'required',
        'promotions_startdate' => 'required',
        'promotions_enddate' => 'required',
        'level_of_promotions' => 'required',
    ];
    public static $form_edit_rules = [
        'promotions_name' => 'required',
        'promotions_startdate' => 'required',
        'promotions_enddate' => 'required',
        'level_of_promotions' => 'required',
    ];

    /**
     * 
     * Custom validate user input to DB (form, CSV)
     * @param array $input
     * @return boolean
     */
    public static function validate($input) {

        $error = [];

//        if (!isset($input['retailer']) || $input['retailer'] != 'AMZ') {
//            $error['message'][] = 'Sample custom error';
//            $error['status'] = false;
//            return $error;
//        }
        
        
        if (!Dot::validate_date($input['promotions_startdate']) || !Dot::validate_date($input['promotions_enddate'])) {
            $error['message'][] = 'Please enter a valid date';
            $error['status'] = false;
            return $error;
        }

        if ($input['promotions_startdate'] > $input['promotions_enddate']) {
            $error['message'][] = 'Start date is greater than end date';
            $error['status'] = false;
            return $error;
        }


        if ($input['level_of_promotions'] == 'Category') {
            if (!Dot::validate_true('category', $input)) {
                $error['message'][] = 'For category level of promotion you must specify the category';
                $error['status'] = false;
                return $error;
            }
        }
        
        if ($input['level_of_promotions'] == 'Brand') {
            if (!Dot::validate_true('brand', $input)) {
                $error['message'][] = 'For brand level of promotion you must specify the brand';
                $error['status'] = false;
                return $error;
            }
        }


        $error['status'] = true;
        return $error;
    }

    /**
     * 
     * Change not exist and unexpected values to default
     * @param type $input
     * @return type
     */
    public static function sanitize($input) {
        $sanitize = [
            'promotions_name' => trim($input['promotions_name']),
            //'promotions_description' => $input['promotions_description'],
            //'promotions_startdate' => date('Y-m-d', strtotime($input['promotions_startdate'])),
            //'promotions_enddate' => date('Y-m-d', strtotime($input['promotions_enddate'])),
            //'retailer',
            //'retailer_country_id',
            //'retailer_country',
            //'newell_status',
            //'promotions_status',
            //'promotions_type',
            //'level_of_promotions',
            //'marketing_type',
            'annivarsaried' => Dot::sanitize_boolean('annivarsaried', $input),
            'promotions_budget' => Dot::sanitize_numeric('promotions_budget', $input),
            'promotions_projected_sales' => Dot::sanitize_numeric('promotions_projected_sales', $input),
            'promotions_expected_lift' => Dot::sanitize_numeric('promotions_expected_lift', $input),
            //'promotions_budget_type',
            //'brand_id',
            //'brand',
            //'category',
            //'product_family',
            //'product_line',
            //'division',
            'status' => isset($input['status']) ? $input['status'] : 'active',
        ];

        return array_merge($input, $sanitize);
    }

    public static function status($input) {
        $validation = Validator::make($input, self::$form_create_rules);
        $custom_validation = self::validate($input);
        if ($validation->passes() && $custom_validation['status']) {
            $input = self::sanitize($input);
            return ['status' => true, 'input' => $input];
        } else {
            return [
                'status' => false,
                'validation' => $validation,
                'custom_validation' => $custom_validation
            ];
        }
    }

    public static function display_prepare($input) {
        $input->promotions_startdate = date('m/d/Y', strtotime($input->promotions_startdate));
        $input->promotions_enddate = date('m/d/Y', strtotime($input->promotions_enddate));
        return $input;
    }

    public static function update_promotion_status($promotion_id, $status) {
        self::where('id', $promotion_id)
                ->update(['status' => $status]);
    }

    function csv_match_data($record) {
        $row = [];
        foreach ($this->csv as $key => $value) {
            if (!isset($record[$key])) {
                continue;
            }
            $row[$value] = $record[$key];
        }
        return $row;
    }

    function csv_validate_file($records) {

        return $this->csv === $records;
    }

}
