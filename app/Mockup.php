<?php

namespace App;

use App\Calendar;
use App\Option;
use App\Stock;
use App\Spinput;
use App\Sdcalc;
use App\Swcalc;
use App\Spod;
use Illuminate\Support\Facades\DB;
use App\promotions\Promotion;
use App\promotions\Item;
use App\Redshift\Pgquery;

class Mockup {

    private $spinput;
    private $sdcalc;
    private $swcalc;
    private $promotion;
    private $calendar;

    function __construct() {
        $this->calendar = new Calendar;
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
        
        // Validation 1
        if(!$this->calendar->is_avail_post_week($this->promotion->promotions_enddate)){
            echo "Future promotion since post week not available \n";
            return false;
        }

        if ($this->promotion->level_of_promotions == 'Category') {
            echo "Executing a category level promotion \n";
            
            if($this->promotion->category == ''){
                return false;
            }
            $this->insert_items_under_promotion();
        }

        Item::where('promotions_id', $this->promotion->id)->orderBy('id')->chunk(100, function ($items) {
            echo "Promotion id {$this->promotion->id} count child items {$items->count()} \n";
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
            'promo_id' => $item['id'], // Let's take child input table id as promo id
            'promotions_name' => $promotion['promotions_name'],
            'promotion_type' => $promotion['promotions_type'],
            'start_date' => $item['promotions_startdate'],
            'end_date' => $item['promotions_enddate'],
            'retailer_id' => $item['rtl_id'],
            'material_id' => $item['material_id'],
            'promo_description' => $item['promotions_description'],
            'item_name' => $item['product_name'],
            'investment_d' => $item['funding_per_unit'],
            'forecasted_units' => $item['forecasted_qty'],
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

        $this->spinput = new Spinput;
        $this->spinput->set_vars($input);

        if (!$this->spinput->validate) {
            echo "The given input is not valid \n";
            return false;
        }

        echo "Executing the promotion with id {$this->spinput->promo_id} \n";
        
        $this->sdcalc = new Sdcalc;
        $this->swcalc = new Swcalc;
        $this->spod = new Spod;
        $this->sdcalc->inject($this->spinput);
        
        if ($this->sdcalc->record_count) {
            $this->swcalc->set_vars($this->spinput, $this->sdcalc);
        }
        
        echo "Promotion {$this->spinput->promo_id} completed ------------------------------------------\n";
    }

    /**
     * 
     * Category level promotion may not contain items, create items
     */
    function insert_items_under_promotion() {

        if ($this->have_child_items()) {
            echo "Items already exist";
            return true;
        }

        $records = Pgquery::get_items_category($this->promotion->category);

        echo "Found " . count($records) . " number of items for the category {$this->promotion->category} \n";
        foreach ($records as $key => $record) {
            $item = $this->prepare_redshift_item($record);
            Item::create($item);
        }
        
        //$this->set_have_child_items(1);
    }

    /**
     * 
     * Check wheather the child are availbale for a category level of promotion
     * @return boolean
     */
    function have_child_items() {
        $mata_key = 'have_child_items_' . $this->promotion->id;
        return Option::get($mata_key);
    }

    /**
     * 
     * Set or change option have_child_items_{n} value
     */
    function set_have_child_items($value) {
        $mata_key = 'have_child_items_' . $this->promotion->id;
        Option::add($mata_key, $value);
    }

    /**
     * 
     * 
     * Prepare records from dim_material for promotions_child_input table
     * @param array $record
     */
    function prepare_redshift_item($record) {

        return [
            'promotions_id' => $this->promotion->id,
            'promotions_startdate' => $this->promotion->promotions_startdate,
            'promotions_enddate' => $this->promotion->promotions_enddate,
            'material_id' => $record['material_id'],
            'product_name' => $record['material_description'],
            'asin' => $record['retailer_sku'],
            'rtl_id' => $record['retailer_upc'],
            'promotions_budget', // inheritted
            'promotions_projected_sales', // inheritted
            'promotions_expected_lift', // inheritted
            'x_plant_material_status' => $record['x_plant_matl_status'],
            'x_plant_status_date' => $record['x_plant_valid_from'],
            'promotions_budget_type', // inheritted
            'funding_per_unit', // @todo
            'forecasted_qty', // @todo
            'forecasted_unit_sales', // @todo
            'promoted' => 1, // @todo
            'user_input' => 0, // @todo
            'validated' => 1, // @todo
            'percent_discount', // @todo
            'price_discount', // @todo
            'reference', // @todo
        ];
    }

}
