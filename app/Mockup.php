<?php

namespace App;

use App\Calendar;
use App\Option;
use App\Stock;
use App\Spinput;
use App\Sdcalc;
use App\Swcalc;
use App\Spod;
use App\Retailers\Amazon;
use App\Retailers\Walmart;
use Illuminate\Support\Facades\DB;
use App\promotions\Promotion;
use App\promotions\Item;
use App\Redshift\Pgquery;
use App\Merge;

class Mockup {

    private $spinput;
    private $sdcalc;
    private $swcalc;
    private $promotion;
    private $calendar;
    private $merge;

    function __construct() {
        $this->calendar = new Calendar;
        $this->merge = new Merge;
        $this->item = new Item;
    }

    function promotion_chunk() {
        // Promotion status => active, completed

        Promotion::whereRaw("(status ='active') AND newell_status = 'Approved'")->orderBy('id')->chunk(100, function ($promotions) {
            foreach ($promotions as $promotion) {
                $this->promotion = $promotion;
                $this->items_count = 0;
                echo "Promotion started for ID : {$this->promotion->id} \n";
                if ($this->run_validity()) {
                    Promotion::update_promotion_status($this->promotion->id, 'processing');
                    $this->reset_records($this->promotion->id);
                    $this->item_chunk();
//                    Promotion::update_promotion_status($this->promotion->id, 'completed');
                }
                $this->update_process_status();
                echo "Promotion ends for ID    : {$this->promotion->id} \n";
            }
        });
    }

    function update_process_status() {
        if ($this->items_count == 0) {
            // Keep status active
            // The promotion deosn't contain item means - user may forget add items
            // It is a category/brand level promotion and system doesn't find items still
            // Promotion::update_promotion_status($this->promotion->id, 'active');
            echo "Promotion not contain items, status remain active \n ";
        } elseif ($this->items_count >= 1) {
            echo "Promotion contains {$this->items_count} items, status completed \n ";
            Promotion::update_promotion_status($this->promotion->id, 'completed');
        }
    }

    /**
     * 
     * Check the validity of a promotion (not to insert into promotion table) to run calculation
     */
    function run_validity() {
        if (!$this->calendar->is_avail_post_week($this->promotion)) {
            echo "Future promotion since post week not available \n";
            return false;
        }

        return true;
    }

    function item_chunk() {


        Item::where('promotions_id', $this->promotion->id)->orderBy('id')->chunk(100, function ($items) {
            
            echo "Promotion id {$this->promotion->id} count child items {$items->count()} \n";

            foreach ($items as $item) {
                $this->item = $item;
                $input = $this->set_input_array();
                $this->process($input);
            }
        });
    }

    function set_input_array() {

        return [
            'promotions_id' => $this->promotion['id'],
            'promo_child_id' => $this->item['id'],
            //master
            'annivarsaried' => $this->promotion['annivarsaried'],
            'brand' => $this->promotion['brand'],
            'brand_id' => $this->promotion['brand_id'],
            'category' => $this->promotion['category'],
            'division' => $this->promotion['division'],
            'level_of_promotions' => $this->promotion['level_of_promotions'],
            'marketing_type' => $this->promotion['marketing_type'],
            'newell_status' => $this->promotion['newell_status'],
            'product_family' => $this->promotion['product_family'],
            'product_line' => $this->promotion['product_line'],
            'promotions_description' => $this->promotion['promotions_description'],
            'promotions_status' => $this->promotion['promotions_status'],
            'promotions_type' => $this->promotion['promotions_type'],
            'retailer' => $this->promotion['retailer'],
            'retailer_country' => $this->promotion['retailer_country'],
            'retailer_country_id' => $this->promotion['retailer_country_id'],
            'status' => $this->promotion['status'],
            //child
            'asin' => $this->item['asin'],
            'forecaseted_qty' => $this->item['forecaseted_qty'],
            'forecasted_unit_sales' => $this->item['forecasted_unit_sales'],
            'funding_per_unit' => $this->item['funding_per_unit'],
            'material_id' => $this->item['material_id'],
            'percent_discount' => $this->item['percent_discount'],
            'price_discount' => $this->item['price_discount'],
            'product_name' => $this->item['product_name'],
            'promoted' => $this->item['promoted'],
            'reference' => $this->item['reference'],
            'retailer_id' => $this->item['rtl_id'],
            'user_input' => $this->item['user_input'],
            'validated' => $this->item['validated'],
            'x_plant_material_status' => $this->item['x_plant_material_status'],
            'x_plant_status_date' => $this->item['x_plant_status_date'],
            // common
            'promotions_startdate' => $this->get_from_item('promotions_startdate'),
            'promotions_enddate' => $this->get_from_item('promotions_enddate'),
            'promotions_budget' => $this->get_from_item('promotions_budget'),
            'promotions_budget_type' => $this->get_from_item('promotions_budget_type'),
            'promotions_expected_lift' => $this->get_from_item('promotions_expected_lift'),
            'promotions_projected_sales' => $this->get_from_item('promotions_projected_sales'),
        ];
    }

    function get_from_item($key) {
        if (!isset($this->item[$key]) || $this->item[$key] == '' || $this->item[$key] == null) {
            if (isset($this->promotion[$key])) {
                return $this->promotion[$key];
            }
        } else {
            return $this->item[$key];
        }

        return 0;
    }

    function process($input) {

        $this->spinput = new Spinput;
        $this->spinput->set_vars($input);

        if (!$this->spinput->validate) {
            echo "The given child item input is not valid \n";
            return false;
        }

        echo "Execution start for child item id {$this->spinput->promo_child_id} \n";
        $this->items_count = $this->items_count + 1;

        $this->sdcalc = new Sdcalc;
        $this->swcalc = new Swcalc;
        $this->spod = new Spod;
        $this->sdcalc->inject($this->spinput);

        if ($this->sdcalc->record_count) {
            // $this->sdcalc->set_invoice_price();
            $this->swcalc->inject($this->spinput, $this->sdcalc);
            $this->spod->inject($this->spinput, $this->sdcalc, $this->swcalc);
            $this->spod->create_record();
        } else {
            echo "No items found sales table (redshift) \n";
        }


        echo "Execution end for child item id {$this->spinput->promo_child_id} \n";
        return true;
    }

    /**
     * 
     * Find items for category level and brand level
     */
    function find_items() {
        Promotion::whereRaw("level_of_promotions ='Category' OR level_of_promotions ='Brand'")->orderBy('id')->chunk(100, function ($promotions) {
            echo "There are {$promotions->count()} records for execute \n";
            foreach ($promotions as $promotion) {
                echo "Find items for the category {$promotion->category} \n";
                $this->item->insert_items_under_promotion($promotion);
            }
        });
    }
    
     /**
     * 
     * Delete records for the item from the table :-
     * promo_week
     * promotions_preperations
     * promotions_results
     * @param int $promotion_id
     */
    function reset_records($promotion_id) {
        $ids = Item::where('promotions_id', $promotion_id)->distinct()->pluck('id')->toArray();

        Sdcalc::whereIn('promo_child_id', $ids)->delete();
        Swcalc::whereIn('promo_child_id', $ids)->delete();
        Spod::whereIn('promo_child_id', $ids)->delete();
    }

}
