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
        'baseline_pos_sales',
        'baseline_pos_units',
        'baseline_ordered_amount',
        'baseline_ordered_units',
        'during_pos_sales',
        'during_pos_units',
        'during_ordered_amount',
        'during_ordered_units',
        'post_pos_sales',
        'post_pos_units',
        'post_ordered_amount',
        'post_ordered_units',
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
        $row['baseline_ordered_amount'] = $this->merge->safe_division($this->swcalc->get_avg_column('normalized_ordered_amount', $this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']), 7);
        $row['baseline_ordered_units'] = $this->merge->safe_division($this->swcalc->get_avg_column('normalized_ordered_units', $this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']), 7);

        $row['baseline_pos_sales'] = $this->swcalc->get_avg_column('normalized_pos_sales', $this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']);
        $row['baseline_pos_units'] = round($this->swcalc->get_avg_column('normalized_pos_units', $this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']));

        // Promotion  days
        // For single day promotion order amount is not avg
        if ($this->spinput->is_single_day) {
            $row['during_ordered_amount'] = $this->sdcalc->get_column_val('ordered_amount', 'date_day', $this->spinput->promotions_startdate);
            $row['during_ordered_units'] = $this->sdcalc->get_column_val('ordered_units', 'date_day', $this->spinput->promotions_startdate);

            $row['during_pos_sales'] = $this->sdcalc->get_column_val('pos_sales', 'date_day', $this->spinput->promotions_startdate);
            $row['during_pos_units'] = $this->sdcalc->get_column_val('pos_units', 'date_day', $this->spinput->promotions_startdate);
        } else {

            $row['during_ordered_amount'] = $this->sdcalc->get_avg_column_week('ordered_amount', $this->spinput->calendar_dates['during']['start_week'], $this->spinput->calendar_dates['during']['end_week']);
            $row['during_ordered_units'] = $this->sdcalc->get_avg_column_week('ordered_units', $this->spinput->calendar_dates['during']['start_week'], $this->spinput->calendar_dates['during']['end_week']);

            $row['during_pos_sales'] = $this->sdcalc->get_avg_column_week('pos_sales', $this->spinput->calendar_dates['during']['start_date'], $this->spinput->calendar_dates['during']['end_date']);
            $row['during_pos_units'] = round($this->sdcalc->get_avg_column_week('pos_units', $this->spinput->calendar_dates['during']['start_date'], $this->spinput->calendar_dates['during']['end_date']));
        }

        // Post days
        $row['post_ordered_amount'] = $this->sdcalc->get_avg_column_week('ordered_amount', $this->spinput->calendar_dates['post']['start_week'], $this->spinput->calendar_dates['post']['end_week']);
        $row['post_ordered_units'] = $this->sdcalc->get_avg_column_week('ordered_units', $this->spinput->calendar_dates['post']['start_week'], $this->spinput->calendar_dates['post']['end_week']);

        $row['post_pos_sales'] = $this->swcalc->get_avg_column('pos_sales', $this->spinput->calendar_dates['post']['start_week'], $this->spinput->calendar_dates['post']['end_week']);
        $row['post_pos_units'] = round($this->swcalc->get_avg_column('pos_units', $this->spinput->calendar_dates['post']['start_week'], $this->spinput->calendar_dates['post']['end_week']));
        
        $row['no_of_promotion_days'] = $this->number_of_promotion_days;
        echo "Inserting output for child item id {$this->spinput->promo_child_id} \n";

//        if ($this->spinput->retailer == 'Walmart') {
//            $this->mark_promoted_items_pos_sales($row);
//        } else {
//            $this->mark_promoted_items_ordered_amount($row);
//        }

        Spod::create($row);
    }


    function mark_promoted_items_ordered_amount($row) {

        echo "Marking the promoted items \n";

        $drop = $require = [];
        $swcalc = $this->swcalc->get_swcalc_week($this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']);
        foreach ($swcalc as $key => $value) {
            if ($value['ordered_amount'] < 0.9 * $row['baseline_ordered_amount']) {
                $drop[] = $value->id;
            } else {
                $require[] = $value->id;
            }
        }


        $row['calculated_during_sales_price'] = $this->merge->safe_division($row['during_ordered_amount'], $row['during_ordered_units']);

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
            if ($value['pos_sales'] < 0.9 * $row['baseline_pos_sales']) {
                $drop[] = $value->id;
            } else {
                $require[] = $value->id;
            }
        }

        $row['calculated_during_sales_price'] = $this->merge->safe_division($row['during_pos_sales'], $row['during_pos_units']);

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
