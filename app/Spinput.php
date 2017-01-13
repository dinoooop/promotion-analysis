<?php

namespace App;

use App\Dot;
use App\Merge;
use App\Calendar;
use App\TimeMachine;

class Spinput {


    private $merge;
    private $calendar;
    public $data;
    public $promo_child_id;

    function set_vars($input) {

        $this->merge = new Merge;
        $this->calendar = new Calendar;
        $this->time_machine = new TimeMachine;

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
        $this->normalize_weeks_count = 13;

        $this->promotions_id = $this->data['promotions_id'];
        $this->promo_child_id = $this->data['promo_child_id'];
        $this->retailer = $this->data['retailer'];
        $this->is_amazon = Dot::is_amazon($this->data);
        $this->promotions_startdate = $this->data['promotions_startdate'];
        $this->promotions_enddate = $this->data['promotions_enddate'];

        $this->material_id = isset($this->data['material_id']) ? $this->data['material_id'] : '';
        $this->retailer_id = isset($this->data['retailer_id']) ? $this->data['retailer_id'] : '';
        $this->asin = isset($this->data['asin']) ? $this->data['asin'] : '';

        $this->is_single_day = ($this->data['promotions_startdate'] == $this->data['promotions_enddate']);
        
        $this->calendar_dates = $this->time_machine->init($this->data['promotions_startdate'], $this->data['promotions_enddate'], $this->baseline_weeks, $this->post_weeks, $this->normalize_weeks_count);
        
        // echo "Promotion start date - {$this->data['promotions_startdate']} \n";
        // echo "Promotion end date   - {$this->data['promotions_enddate']} \n";
    }

    function validate() {

        if ($this->data['promotions_startdate'] > $this->today) {
            // echo "Skip, future promotion \n";
            return false;
        }

        if ((!isset($this->data['material_id']) || $this->data['material_id'] == '')) {

            if (!isset($this->data['retailer_id']) || $this->data['retailer_id'] == '') {
                // echo "material_id or retailer_id does't exist \n";
                return false;
            }
        }


        if (!Dot::validate_date($this->data['promotions_startdate']) || !Dot::validate_date($this->data['promotions_enddate'])) {
            echo "Input date is not valid \n";
            return false;
        }

        if ($this->data['promotions_startdate'] > $this->data['promotions_enddate']) {
            // echo "Input date is not  valid since start date greater than end date \n";
            return false;
        }

        if (!$this->calendar->is_avail_post_week($this->data)) {
            // echo "Future promotion since post week not available \n";
            return false;
        }

        return true;
    }

    

}
