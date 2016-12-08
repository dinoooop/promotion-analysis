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

        $this->year = date('Y', strtotime($this->data['promotions_startdate']));

        $this->validate = $this->validate();

        if (!$this->validate) {
            return false;
        }
        
        $settings = $this->merge->admin_settings($this->data);
        $this->baseline_weeks = $settings['baseline_weeks'];
        $this->post_weeks = $settings['post_weeks'];
        $this->baseline_threshold = $settings['baseline_threshold'];
        $this->post_week_avail_week_count = $this->post_weeks + 1;

        $this->promotions_id = $input['promotions_id'];
        $this->promo_child_id = $input['promo_child_id'];


        $this->material_id = isset($this->data['material_id']) ? $this->data['material_id'] : '';
        $this->retailer_id = isset($this->data['retailer_id']) ? $this->data['retailer_id'] : '';
        $this->asin = isset($this->data['asin']) ? $this->data['asin'] : '';


        $this->is_single_day = ($this->data['promotions_startdate'] == $this->data['promotions_enddate']);

        $this->calendar_dates = $this->calendar->init($this->data['promotions_startdate'], $this->data['promotions_enddate'], $this->baseline_weeks, $this->post_weeks);


        echo "Promotion start date - {$this->data['promotions_startdate']} \n";
        echo "Promotion end date   - {$this->data['promotions_enddate']} \n";
    }

    function validate() {

        if ($this->data['promotions_startdate'] > $this->today) {
            echo "Skip, future promotion \n";
            return false;
        }

        if ((!isset($this->data['material_id']) || $this->data['material_id'] == '')) {

            if (!isset($this->data['retailer_id']) || $this->data['retailer_id'] == '') {
                echo "material_id or retailer_id does't exist \n";
                return false;
            }
        }


        if (!Dot::validate_date($this->data['promotions_startdate']) || !Dot::validate_date($this->data['promotions_enddate'])) {
            echo "Input date is not valid \n";
            return false;
        }

        if ($this->data['promotions_startdate'] > $this->data['promotions_enddate']) {
            echo "Input date is not  valid since start date greater than end date \n";
            return false;
        }

        if (!$this->calendar->is_avail_post_week($this->data)) {
            echo "Future promotion since post week not available \n";
            return false;
        }

        return true;
    }

    

}
