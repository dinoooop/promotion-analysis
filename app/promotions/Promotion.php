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

    function set_vars($input) {

        $this->merge = new Merge;
        $this->calendar = new Calendar;

        $this->today = date("Y-m-d");


        // Record to be inserted to promo_input
        $this->data = $this->sanitize($input);


        $this->year = date('Y', strtotime($this->data['start_date']));


        $this->validate = $this->validate();

        if (!$this->validate) {
            return false;
        }

        $this->material_id = isset($this->data['material_id']) ? $this->data['material_id'] : '';
        $this->retailer_id = isset($this->data['retailer_id']) ? $this->data['retailer_id'] : '';

        $this->retailer_sku = isset($this->data['retailer_sku']) ? $this->data['retailer_sku'] : '';

        $this->is_single_day_promo = ($this->data['start_date'] == $this->data['end_date']);
        $this->quarter = $this->calendar->get_quarter($this->data['start_date']);


        $this->during_dates = $this->calendar->get_req_dates($this->data['start_date']);


        echo "Promotion start date - {$this->data['start_date']} \n";
        echo "Promotion end date   - {$this->data['end_date']} \n";
        echo "Quarter: {$this->quarter['quarter']}, week count: {$this->quarter['week_count']} records from {$this->quarter['start']} to {$this->quarter['end']} \n";
    }

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
            'promotions_budget' => (isset($input['promotions_budget']) && isset($input['promotions_budget']) != '') ? $input['promotions_budget'] : 0,
            'promotions_projected_sales' => (isset($input['promotions_projected_sales']) && $input['promotions_projected_sales'] != '')? $input['promotions_projected_sales'] : 0,
            'promotions_expected_lift' => (isset($input['promotions_expected_lift']) && $input['promotions_expected_lift'] != '')? $input['promotions_expected_lift'] : 0,
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

    function create_record($data) {
        $spinput = self::create($data);
        return $spinput->id;
    }

    function set_vars_nh() {
        $weekly_baseline_number = $this->merge->admin_settings('weekly_baseline_number');
        $this->weekly_baseline_date = date('Y-m-d', strtotime($this->data['start_date'] . " -{$weekly_baseline_number} weeks"));

        $this->is_require_nhqs = $this->is_nh_quarter_require($this->weekly_baseline_date);

        $post_weekly_baseline = $this->merge->admin_settings('post_weekly_baseline_number');
        $this->post_weekly_baseline_date = date('Y-m-d', strtotime($this->data['end_date'] . " +{$post_weekly_baseline} weeks"));
        $this->is_require_nhqe = $this->is_nh_quarter_require($this->post_weekly_baseline_date);
    }

    function is_nh_quarter_require($date) {
        $calander = new Calendar();
        $nh_quarter = $calander->get_quarter($date);
        return ($this->quarter['quarter'] == $nh_quarter['quarter']) ? false : true;
    }

}
