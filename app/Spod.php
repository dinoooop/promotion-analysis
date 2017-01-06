<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Sdcalc;
use App\Swcalc;
use App\Merge;
use App\Redshift\Dsales;
use App\promotions\Item;
use App\TimeMachine;

class Spod extends Model {

    protected $table = 'promotions.promotions_results';
    public $timestamps = false;
    protected $guarded = array('id');
    protected $fillable = [
        'promotions_id',
        'promo_child_id',
        'material_id',
        'asin',
        'rtl_id',
        'product_name',
        'daily_baseline_pos_sales',
        'daily_baseline_pos_units',
        'daily_baseline_ordered_amount',
        'daily_baseline_ordered_units',
        'daily_during_pos_sales',
        'daily_during_pos_units',
        'daily_during_ordered_amount',
        'daily_during_ordered_units',
        'daily_post_pos_sales',
        'daily_post_pos_units',
        'daily_post_ordered_amount',
        'daily_post_ordered_units',
        'during_incremental_ordered_amount',
        'during_incremental_ordered_units',
        'during_incremental_pos_sales',
        'during_incremental_pos_units',
        'post_incremental_ordered_amount',
        'post_incremental_ordered_units',
        'post_incremental_pos_sales',
        'post_incremental_pos_units',
        'during_lift_ordered_amount',
        'during_lift_ordered_units',
        'during_lift_pos_sales',
        'during_lift_pos_units',
        'post_lift_ordered_amount',
        'post_lift_ordered_units',
        'post_lift_pos_sales',
        'post_lift_pos_units',
        'calculated_investment_amount',
        'no_of_promotion_days',
    ];

    function inject($spinput, $sdcalc, $swcalc) {

        $this->merge = new Merge;
        $this->time_machine = new TimeMachine;

        $this->spinput = $spinput;
        $this->sdcalc = $sdcalc;
        $this->swcalc = $swcalc;


        if ($this->spinput->is_single_day) {
            // Number of days based denominator for single day it is 7
            echo "Single day promotion (POD) \n";            
            $this->number_of_promotion_days = 1;
            $this->nod_based_denominator = 7;
        } else {
            echo "Multiple day promotion (POD) \n";
            $this->number_of_promotion_days = $this->time_machine->date_difference($this->spinput->data['promotions_startdate'], $this->spinput->data['promotions_enddate']) + 1;
            $this->nod_based_denominator = $this->number_of_promotion_days;
        }
    }

    function create_record() {
        $row = [];
        $row['promotions_id'] = $this->spinput->promotions_id;
        $row['promo_child_id'] = $this->spinput->promo_child_id;
        $row['material_id'] = $this->spinput->material_id;
        $row['rtl_id'] = $this->spinput->retailer_id;
        $row['asin'] = $this->spinput->asin;
        $row['product_name'] = $this->spinput->data['product_name'];

        // Basilene days (Normalized)
        $row['daily_baseline_ordered_amount'] = $this->merge->safe_division($this->swcalc->get_avg_column('normalized_ordered_amount', $this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']), 7);
        $row['daily_baseline_ordered_units'] = $this->merge->safe_division($this->swcalc->get_avg_column('normalized_ordered_units', $this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']), 7, true);

        $row['daily_baseline_pos_sales'] = $this->swcalc->get_avg_column('normalized_pos_sales', $this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']);
        $row['daily_baseline_pos_units'] = round($this->swcalc->get_avg_column('normalized_pos_units', $this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']));
        
        // Promotion  days
        // For single day promotion order amount is not avg
        if ($this->spinput->is_single_day) {
            $row['daily_during_ordered_amount'] = $this->sdcalc->get_column_val('ordered_amount', 'date_day', $this->spinput->promotions_startdate);
            $row['daily_during_ordered_units'] = $this->sdcalc->get_column_val('ordered_units', 'date_day', $this->spinput->promotions_startdate);

            $row['daily_during_pos_sales'] = $this->sdcalc->get_column_val('pos_sales', 'date_day', $this->spinput->promotions_startdate);
            $row['daily_during_pos_units'] = $this->sdcalc->get_column_val('pos_units', 'date_day', $this->spinput->promotions_startdate);
        } else {
            $row['daily_during_ordered_amount'] = $this->swcalc->get_avg_column('ordered_amount', $this->spinput->calendar_dates['during']['start_week'], $this->spinput->calendar_dates['during']['end_week']);
            $row['daily_during_ordered_units'] = round($this->swcalc->get_avg_column('ordered_units', $this->spinput->calendar_dates['during']['start_week'], $this->spinput->calendar_dates['during']['end_week']));

            $row['daily_during_pos_sales'] = $this->swcalc->get_avg_column('pos_sales', $this->spinput->calendar_dates['during']['start_week'], $this->spinput->calendar_dates['during']['end_week']);
            $row['daily_during_pos_units'] = round($this->swcalc->get_avg_column('pos_units', $this->spinput->calendar_dates['during']['start_week'], $this->spinput->calendar_dates['during']['end_week']));
        }

        // Post days
        $row['daily_post_ordered_amount'] = $this->swcalc->get_avg_column('ordered_amount', $this->spinput->calendar_dates['post']['start_week'], $this->spinput->calendar_dates['post']['end_week']);
        $row['daily_post_ordered_units'] = round($this->swcalc->get_avg_column('ordered_units', $this->spinput->calendar_dates['post']['start_week'], $this->spinput->calendar_dates['post']['end_week']));
        $row['daily_post_pos_sales'] = $this->swcalc->get_avg_column('pos_sales', $this->spinput->calendar_dates['post']['start_week'], $this->spinput->calendar_dates['post']['end_week']);
        $row['daily_post_pos_units'] = round($this->swcalc->get_avg_column('pos_units', $this->spinput->calendar_dates['post']['start_week'], $this->spinput->calendar_dates['post']['end_week']));

        //Others
        $row['during_incremental_ordered_amount'] = $this->calc('during_incremental_ordered_amount', $row);
        $row['during_incremental_ordered_units'] = $this->calc('during_incremental_ordered_units', $row);

        $row['during_incremental_pos_sales'] = $this->calc('during_incremental_pos_sales', $row);
        $row['during_incremental_pos_units'] = $this->calc('during_incremental_pos_units', $row);

        
        $row['post_incremental_pos_sales'] = $this->calc('post_incremental_pos_sales', $row);
        $row['post_incremental_pos_units'] = $this->calc('post_incremental_pos_units', $row);
        

        $row['post_incremental_ordered_amount'] = $this->calc('post_incremental_ordered_amount', $row);
        $row['post_incremental_ordered_units'] = $this->calc('post_incremental_ordered_units', $row);
        
        $row['during_lift_ordered_amount'] = $this->calc('during_lift_ordered_amount', $row);
        $row['during_lift_ordered_units'] = $this->calc('during_lift_ordered_units', $row);
        
        $row['during_lift_pos_sales'] = $this->calc('during_lift_pos_sales', $row);
        $row['during_lift_pos_units'] = $this->calc('during_lift_pos_units', $row);
        

        $row['post_lift_ordered_amount'] = $this->calc('post_lift_ordered_amount', $row);
        $row['post_lift_ordered_units'] = $this->calc('post_lift_ordered_units', $row);
        
        $row['post_lift_pos_sales'] = $this->calc('post_lift_pos_sales', $row);
        $row['post_lift_pos_units'] = $this->calc('post_lift_pos_units', $row);
        
        
        $row['no_of_promotion_days'] = $this->number_of_promotion_days;

        echo "Inserting output for child item id {$this->spinput->promo_child_id} \n";



        if ($this->spinput->retailer == 'Walmart') {
            $this->mark_promoted_items_pos_sales($row);
        } else {
            $this->mark_promoted_items_ordered_amount($row);
        }

        Spod::create($row);
    }

    function calc($key, $row) {
        switch ($key) {
            
            // INCREMENTAL
            case 'during_incremental_ordered_amount':
                return ($row['daily_during_ordered_amount'] - $row['daily_baseline_ordered_amount'] ) * $this->number_of_promotion_days;
                break;
            case 'during_incremental_ordered_units':
                return ($row['daily_during_ordered_units'] - $row['daily_baseline_ordered_units'] ) * $this->number_of_promotion_days;
                break;
            
            case 'during_incremental_pos_sales':
                return ($row['daily_during_pos_sales'] - $row['daily_baseline_pos_sales'] ) * $this->number_of_promotion_days;
                break;

            case 'during_incremental_pos_units':
                return ($row['daily_during_pos_units'] - $row['daily_baseline_pos_units'] ) * $this->number_of_promotion_days;
                break;

            case 'post_incremental_ordered_amount':
                return ($row['daily_post_ordered_amount'] - $row['daily_baseline_ordered_amount'] ) * 7 * $this->spinput->post_weeks;
                break;

            case 'post_incremental_ordered_units':
                return ($row['daily_post_ordered_units'] - $row['daily_baseline_ordered_units'] ) * 7 * $this->spinput->post_weeks;
                break;
            
            case 'post_incremental_pos_sales':
                return ($row['daily_post_pos_sales'] - $row['daily_baseline_pos_sales'] ) * 7 * $this->spinput->post_weeks;
                break;

            case 'post_incremental_pos_units':
                return ($row['daily_post_pos_units'] - $row['daily_baseline_pos_units'] ) * 7 * $this->spinput->post_weeks;
                break;

            // LIFT
            case 'during_lift_ordered_amount':
                return $this->merge->safe_division($row['daily_during_ordered_amount'], $row['daily_baseline_ordered_amount']) - 1;
                break;

            case 'during_lift_ordered_units':
                return $this->merge->safe_division($row['daily_during_ordered_units'], $row['daily_baseline_ordered_units'], true) - 1;
                break;
            
            case 'during_lift_pos_sales':
                return $this->merge->safe_division($row['daily_during_pos_sales'], $row['daily_baseline_pos_sales']) - 1;
                break;

            case 'during_lift_pos_units':
                return $this->merge->safe_division($row['daily_during_pos_units'], $row['daily_baseline_pos_units'], true) - 1;
                break;

            case 'post_lift_ordered_amount':
                return $this->merge->safe_division($row['daily_post_ordered_amount'], $row['daily_baseline_ordered_amount']) - 1;
                break;

            case 'post_lift_ordered_units':
                return $this->merge->safe_division($row['daily_post_ordered_units'], $row['daily_baseline_ordered_units'], true) - 1;
                break;
            
            case 'post_lift_pos_sales':
                return $this->merge->safe_division($row['daily_post_pos_sales'], $row['daily_baseline_pos_sales']) - 1;
                break;

            case 'post_lift_pos_units':
                return $this->merge->safe_division($row['daily_post_pos_units'], $row['daily_baseline_pos_units'], true) - 1;
                break;
            
        }
        return false;
    }

    function mark_promoted_items_ordered_amount($row) {

        echo "Marking the promoted items \n";

        $drop = $require = [];
        $swcalc = $this->swcalc->get_swcalc_week($this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']);
        foreach ($swcalc as $key => $value) {
            if ($value['ordered_amount'] < 0.9 * $row['daily_baseline_ordered_amount']) {
                $drop[] = $value->id;
            } else {
                $require[] = $value->id;
            }
        }


        $row['calculated_during_sales_price'] = $this->merge->safe_division($row['daily_during_ordered_amount'], $row['daily_during_ordered_units']);

        $row['calculated_baseline_ordered_amount'] = $this->swcalc->get_avg_column_id('ordered_amount', $require);
        $row['calculated_baseline_ordered_units'] = $this->swcalc->get_avg_column_id('ordered_units', $require);
        $row['calculated_baseline_sales_price'] = $this->merge->safe_division($row['calculated_baseline_ordered_amount'], $row['calculated_baseline_ordered_units']);
        $row['discount'] = 1 - ($row['calculated_during_sales_price'] / $row['calculated_baseline_sales_price']);

        if ($row['discount'] >= 0.03) {
            $row['promoted'] = true;
            echo "Promoted is : true \n";
        } else {
            $row['promoted'] = false;
            echo "Promoted is : false \n";
        }


        Item::where('id', $this->spinput->promo_child_id)->update(['promoted' => $row['promoted']]);
    }

    function mark_promoted_items_pos_sales($row) {

        echo "Marking the promoted items \n";

        $drop = $require = [];
        $swcalc = $this->swcalc->get_swcalc_week($this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']);
        foreach ($swcalc as $key => $value) {
            if ($value['pos_sales'] < 0.9 * $row['daily_baseline_pos_sales']) {
                $drop[] = $value->id;
            } else {
                $require[] = $value->id;
            }
        }

        $row['calculated_during_sales_price'] = $this->merge->safe_division($row['daily_during_pos_sales'], $row['daily_during_pos_units']);

        $row['calculated_baseline_pos_sales'] = $this->swcalc->get_avg_column_id('pos_sales', $require);
        $row['calculated_baseline_pos_units'] = $this->swcalc->get_avg_column_id('pos_units', $require);
        $row['calculated_baseline_sales_price'] = $this->merge->safe_division($row['calculated_baseline_pos_sales'], $row['calculated_baseline_pos_units']);
        $row['discount'] = 1 - ($row['calculated_during_sales_price'] / $row['calculated_baseline_sales_price']);

        if ($row['discount'] >= 0.03) {
            $row['promoted'] = true;
            echo "Promoted is : true \n";
        } else {
            $row['promoted'] = false;
            echo "Promoted is : false \n";
        }

        Item::where('id', $this->spinput->promo_child_id)->update(['promoted' => $row['promoted']]);
    }

}
