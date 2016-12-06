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
    public $promo_child_id;

    function set_vars($input) {

        $this->merge = new Merge;
        $this->calendar = new Calendar;
        
        $this->today = date("Y-m-d");
        
        // The data required for calculations
        $this->data = $input;
        
        
        $this->year = date('Y', strtotime($this->data['start_date']));
        

        $this->validate = $this->validate();

        if (!$this->validate) {
            return false;
        }
        
        $this->promotions_id = $input['promotions_id'];
        $this->promo_child_id = $input['promo_child_id'];
        

        $this->material_id = isset($this->data['material_id']) ? $this->data['material_id'] : '';
        $this->retailer_id = isset($this->data['retailer_id']) ? $this->data['retailer_id'] : '';
        $this->asin = isset($this->data['asin']) ? $this->data['asin'] : '';
        

        $this->is_single_day = ($this->data['start_date'] == $this->data['end_date']);
        
        $this->calendar_dates = $this->calendar->init($this->data['start_date'], $this->data['end_date']);
        $this->number_weeks_post_promotion = $this->merge->admin_settings('number_weeks_post_promotion');
        
        echo "Promotion start date - {$this->data['start_date']} \n";
        echo "Promotion end date   - {$this->data['end_date']} \n";
        
        
    }

    function validate() {
        
        if($this->data['start_date'] > $this->today){
            echo "Skip, future promotion \n";
            return false;
        }
        
        if ((!isset($this->data['material_id']) || $this->data['material_id'] == '')) {

            if (!isset($this->data['retailer_id']) || $this->data['retailer_id'] == '') {
                echo "material_id or retailer_id does't exist \n";
                return false;
            }
        }


        if (!Dot::validate_date($this->data['start_date']) || !Dot::validate_date($this->data['end_date'])) {
            echo "Input date is not valid \n";
            return false;
        }

        if ($this->data['start_date'] > $this->data['end_date']) {
            echo "Input date is not  valid since start date greater than end date \n";
            return false;
        }
        
        if (!$this->calendar->is_avail_post_week($this->data['end_date'])) {
            echo "Future promotion since post week not available \n";
            return false;
        }
        
        return true;
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
