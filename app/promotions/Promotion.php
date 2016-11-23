<?php

namespace App\promotions;

use Illuminate\Database\Eloquent\Model;
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
    private $merge;
    private $calendar;
    public $data;
    public $promo_id;
    public static $form_create_rules = [];
    public static $form_edit_rules = [];

   

    function validate() {

        if ($this->data['start_date'] > $this->today) {
            return false;
        }

        if ((!isset($this->data['material_id']) || $this->data['material_id'] == '')) {

            if (!isset($this->data['retailer_id']) || $this->data['retailer_id'] == '') {
                return false;
            }
        }


        if (!Dot::validate_date($this->data['start_date']) || !Dot::validate_date($this->data['end_date'])) {
            return false;
        }

        if ($this->data['start_date'] > $this->data['end_date']) {
            return false;
        }
        return true;
    }

    public static function sanitize($input) {
        $sanitize =  [
            'promotions_name' => trim($input['promotions_name']),
            'promotions_description' => $input['promotions_description'],
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
            'annivarsaried' => isset($input['annivarsaried']) ? 1 : 0,
            'promotions_budget' => (isset($input['promotions_budget']) && isset($input['promotions_budget']) != '') ? $input['promotions_budget'] : null,
            'promotions_projected_sales' => (isset($input['promotions_projected_sales']) && $input['promotions_projected_sales'] != '')? $input['promotions_projected_sales'] : null,
            'promotions_expected_lift' => (isset($input['promotions_expected_lift']) && $input['promotions_expected_lift'] != '')? $input['promotions_expected_lift'] : null,
            //'promotions_budget_type',
            //'brand_id',
            //'brand',
            //'category',
            //'product_family',
            //'product_line',
            //'division',
            //'status'
        ];
        
        return array_merge($input, $sanitize);
    }
    
    public static function display_prepare($input) {
        $input->promotions_startdate = date('m/d/Y', strtotime($input->promotions_startdate));
        $input->promotions_enddate = date('m/d/Y', strtotime($input->promotions_enddate));
        return $input;
    }
    
    
    public static function get_next_promotion() {
        return self::where('status', 'Active')->orderBy('id')->first();
    }
    
    

    

}
