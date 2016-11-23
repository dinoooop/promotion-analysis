<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Dot;
use App\Merge;
use App\Calendar;

class Spinput extends Model {

    protected $table = 'promo_input';
    protected $guarded = array('id');
    protected $fillable = [
        'material_id',
        'retailer_id',
        'promotions_name',
        'promotion_type',
        'start_date',
        'end_date',
        'promo_description',
        'item_name',
        'investment_d',
        'forecasted_units',
        'forecasted_d',
        'customer_name',
        'level_of_promotion',
        'discount_price_d',
        'discount_p',
        'comments',
        'status'
    ];
    private $merge;
    private $calendar;
    public $data;
    public $promo_id;

    function set_vars($input) {

        $this->merge = new Merge;
        $this->calendar = new Calendar;
        
        $this->today = date("Y-m-d");
        
        // The data required for calculations
        $this->data = $this->sanitize($input);
        
        
        $this->year = date('Y', strtotime($this->data['start_date']));
        

        $this->validate = $this->validate();

        if (!$this->validate) {
            return false;
        }
        
        $this->promo_id = $input['promo_id'];

        $this->material_id = isset($this->data['material_id']) ? $this->data['material_id'] : '';
        $this->retailer_id = isset($this->data['retailer_id']) ? $this->data['retailer_id'] : '';

        $this->retailer_sku = isset($this->data['retailer_sku']) ? $this->data['retailer_sku'] : '';

        $this->is_single_day_promo = ($this->data['start_date'] == $this->data['end_date']);
        
        $this->calendar_dates = $this->calendar->init($this->data['start_date'], $this->data['end_date']);
        
        
        echo "Promotion start date - {$this->data['start_date']} \n";
        echo "Promotion end date   - {$this->data['end_date']} \n";
        
        
    }

    function validate() {
        
        if($this->data['start_date'] > $this->today){
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
        
        
        if(!$this->calendar->is_avail_post_week($this->data['end_date'])){
            return false;
        }
        return true;
    }
    
    

    function sanitize($input) {
        return [
            'material_id' => $input['material_id'],
            'retailer_id' => $input['retailer_id'],
            'promotions_name' => $input['promotions_name'],
            'promotion_type' => $input['promotion_type'],
            'start_date' => date('Y-m-d', strtotime($input['start_date'])),
            'end_date' => date('Y-m-d', strtotime($input['end_date'])),
            'retailer_id' => $input['retailer_id'],
            'material_id' => $input['material_id'],
            'promo_description' => $input['promo_description'],
            'item_name' => $input['item_name'],
            'investment_d' => $this->merge->refine_dollar($input['investment_d']),
            'forecasted_units' => $input['forecasted_units'],
            'forecasted_d' => $this->merge->refine_dollar($input['forecasted_d']),
            'customer_name' => $input['customer_name'],
            'level_of_promotion' => $input['level_of_promotion'],
            'discount_price_d' => $this->merge->refine_dollar($input['discount_price_d']),
            'discount_p' => $this->merge->refine_dollar($input['discount_p']),
            'comments' => $input['comments'],
        ];
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
        return ($this->quarter['quarter'] == $nh_quarter['quarter'])? false : true;
    }

    

}
