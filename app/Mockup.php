<?php

namespace App;

use App\Calendar;
use App\Block;
use App\Stock;
use App\Spinput;
use App\Sdcalc;
use App\Swcalc;
use App\Spod;
use Illuminate\Support\Facades\DB;
use App\promotions\Promotion;
use App\promotions\Item;

class Mockup {
    
    private $spinput;
    private $sdcalc;
    private $swcalc;

    function __construct() {
        
    }

    function promotion_chunk() {
        Promotion::where('status', 'Active')->orderBy('id')->chunk(100, function ($promotions) {
            foreach ($promotions as $promotion) {
                $this->promotion = $promotion;
                $this->item_chunk();
            }
        });
    }

    function item_chunk() {
        Item::where('promotions_id', $this->promotion->id)->orderBy('id')->chunk(100, function ($items) {
            foreach ($items as $item) {
                $input = $this->set_input_array($item);
                $this->process($input);
            }
        });
    }

    function set_input_array($item) {
        $promotion = $this->promotion;
        // required
        return [
            'promo_id' => $item['id'],// Let's take child input table id as promo id
            'promotions_name' => $promotion['promotions_name'],
            'promotion_type' => $promotion['promotions_type'],
            'start_date' => $item['promotions_startdate'],
            'end_date' => $item['promotions_enddate'],
            'retailer_id' => $item['rtl_id'],
            'material_id' => $item['material_id'],
            'promo_description' => $item['promotions_description'],
            'item_name' => $item['product_name'],
            'investment_d' => $item['funding_per_unit'],
            'forecasted_units' => $item['forecaseted_qty'],
            'forecasted_d' => $item['forecasted_unit_sales'],
            'customer_name' => $promotion['retailer'],
            'level_of_promotion' => $promotion['level_of_promotions'],
            'discount_price_d' => $item['price_discount'],
            'discount_p' => $item['percent_discount'],
            'comments' => $item['reference'],
        ];
    }

    function get_from_item($key, $promotion, $item) {
        if (!isset($item[$key]) || $item[$key] == '' || $item[$key] == null) {
            if (isset($promotion[$key])) {
                return $promotion[$key];
            }
        } else {
            return $item[$key];
        }

        return 0;
    }

    function process($input) {

//        @testing 
//        Sdcalc::truncate();
//        Swcalc::truncate();
//        Spod::truncate();
//        Spinput::truncate();



        $this->spinput = new Spinput;
        $this->sdcalc = new Sdcalc;
        $this->swcalc = new Swcalc;
        
        $this->spod = new Spod;


        $this->spinput->set_vars($input);

        if (!$this->spinput->validate) {
            echo "The given input is not valid \n";
            return false;
        }
        
        echo "Executing the promotion with id {$this->spinput->promo_id} \n";

        $this->sdcalc->set_vars($this->spinput);
        
        $this->swcalc->set_vars($this->spinput, $this->sdcalc);
        
        exit('completed.');
        

        
        $this->spinput->set_vars_nh();
        if ($this->spinput->is_require_nhqs) {
            echo "Neighbourhood quarter required (start) \n";
            $this->nh_spinput = new Spinput;
            $this->nh_sdcalc = new Sdcalc;
            $this->nh_swcalc = new Swcalc;
            $input['start_date'] = $this->spinput->weekly_baseline_date;
            $input['end_date'] = $this->spinput->weekly_baseline_date;

            $this->nh_spinput->set_vars($input);
            $this->nh_spinput->promo_id = $this->spinput->promo_id;
            $this->nh_sdcalc->set_vars($this->nh_spinput);

            if ($this->nh_sdcalc->record_count) {
                $this->nh_swcalc->set_vars($this->nh_sdcalc);
            }
        }

        if ($this->spinput->is_require_nhqe) {

            echo "Neighbourhood quarter required (end) \n";

            $this->nh_spinput = new Spinput;
            $this->nh_sdcalc = new Sdcalc;
            $this->nh_swcalc = new Swcalc;
            $input['start_date'] = $this->spinput->post_weekly_baseline_date;
            $input['end_date'] = $this->spinput->post_weekly_baseline_date;

            $this->nh_spinput->set_vars($input);
            $this->nh_spinput->promo_id = $this->spinput->promo_id;
            $this->nh_sdcalc->set_vars($this->nh_spinput);

            if ($this->nh_sdcalc->record_count) {
                $this->nh_swcalc->set_vars($this->nh_sdcalc);
            }
        }

        if ($this->sdcalc->record_count) {
            $this->spod->set_vars($this->swcalc);
            $this->spod->create_record();
        }

        echo "Promotion {$this->spinput->promo_id} completed ------------------------------------------\n";
    }

}
