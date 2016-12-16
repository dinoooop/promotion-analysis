<?php

namespace App\Retailers;

use App\Sdcalc;
use App\Swcalc;
use App\Spod;
use App\Calendar;
use App\Merge;
use App\Redshift\Dsales;
use App\promotions\Item;
use Illuminate\Support\Facades\Log;

class Amazon {

    function inject($spinput, $sdcalc, $swcalc) {
        
        $this->calendar = new Calendar;
        $this->merge = new Merge;

        $this->spinput = $spinput;
        $this->sdcalc = $sdcalc;
        $this->swcalc = $swcalc;


        if ($this->spinput->is_single_day) {
            // Number of days based denominator for single day it is 7
            echo "Single day promotion (POD) \n";
            $this->nod_based_denominator = 7;
            $this->number_of_promotion_days = 1;
        } else {
            echo "Multiple day promotion (POD) \n";
            $this->date_difference = $this->calendar->date_difference($this->spinput->data['promotions_startdate'], $this->spinput->data['promotions_enddate']);
            $this->nod_based_denominator = $this->date_difference;
            $this->number_of_promotion_days = $this->date_difference;
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

        $row['daily_baseline_ordered_amount'] = $this->swcalc->get_avg_column('normalized_ordered_amount', $this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']);
        $row['daily_baseline_ordered_units'] = round($this->swcalc->get_avg_column('normalized_ordered_units', $this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']));
        // Promotion  days

        $row['daily_during_ordered_amount'] = $this->swcalc->get_avg_column('ordered_amount', $this->spinput->calendar_dates['during']['start_week'], $this->spinput->calendar_dates['during']['end_week']);
        $row['daily_during_ordered_units'] = round($this->swcalc->get_avg_column('ordered_units', $this->spinput->calendar_dates['during']['start_week'], $this->spinput->calendar_dates['during']['end_week']));
        // Post days

        $row['daily_post_ordered_amount'] = $this->swcalc->get_avg_column('ordered_amount', $this->spinput->calendar_dates['post']['start_week'], $this->spinput->calendar_dates['post']['end_week']);
        $row['daily_post_ordered_units'] = round($this->swcalc->get_avg_column('ordered_units', $this->spinput->calendar_dates['post']['start_week'], $this->spinput->calendar_dates['post']['end_week']));

        //Others
        $row['during_incremental_ordered_amount'] = $this->calc('during_incremental_ordered_amount', $row);
        $row['during_incremental_ordered_units'] = $this->calc('during_incremental_ordered_units', $row);

        $row['post_incremental_ordered_amount'] = $this->calc('post_incremental_ordered_amount', $row);
        $row['post_incremental_ordered_units'] = $this->calc('post_incremental_ordered_units', $row);

        $row['during_lift_ordered_amount'] = $this->calc('during_lift_ordered_amount', $row);
        $row['during_lift_ordered_units'] = $this->calc('during_lift_ordered_units', $row);
        $row['post_lift_ordered_amount'] = $this->calc('post_lift_ordered_amount', $row);
        $row['post_lift_ordered_units'] = $this->calc('post_lift_ordered_units', $row);
        $row['no_of_promotion_days'] = $this->number_of_promotion_days;
        echo "Inserting output for child item id {$this->spinput->promo_child_id} \n";
        $this->mark_promoted_items($row);
        Spod::create($row);
    }

    function calc($key, $row) {
        switch ($key) {
            case 'during_incremental_ordered_amount':
                return ($row['daily_during_ordered_amount'] - $row['daily_baseline_ordered_amount'] ) * $this->number_of_promotion_days;
                break;
            case 'during_incremental_ordered_units':
                return ($row['daily_during_ordered_units'] - $row['daily_baseline_ordered_units'] ) * $this->number_of_promotion_days;
                break;
            
            case 'post_incremental_ordered_amount':
                return ($row['daily_post_ordered_amount'] - $row['daily_baseline_ordered_amount'] ) * 7 * $this->spinput->post_weeks;
                break;

            case 'post_incremental_ordered_units':
                return ($row['daily_post_ordered_units'] - $row['daily_baseline_ordered_units'] ) * 7 * $this->spinput->post_weeks;
                break;


            case 'during_lift_ordered_amount':
                return $this->merge->safe_division($row['daily_during_ordered_amount'], $row['daily_baseline_ordered_amount']) - 1;
                return $this->merge->safe_division($row['daily_during_ordered_amount'], $row['daily_baseline_ordered_amount']) - 1;
                break;

            case 'during_lift_ordered_units':
                return $this->merge->safe_division($row['daily_during_ordered_units'], $row['daily_baseline_ordered_units'], true) - 1;
                break;
            case 'post_lift_ordered_amount':
                return $this->merge->safe_division($row['daily_post_ordered_amount'], $row['daily_baseline_ordered_amount']) - 1;
                break;
            case 'post_lift_ordered_units':
                return $this->merge->safe_division($row['daily_post_ordered_units'], $row['daily_baseline_ordered_units'], true) - 1;
                break;
        }
        return false;
    }

    function mark_promoted_items($row) {

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

}
