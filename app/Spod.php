<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sdcalc;
use App\Swcalc;
use App\Calendar;
use App\Merge;
use App\Redshift\Dsales;
use Illuminate\Support\Facades\Log;

class Spod extends Model {

    protected $table = 'promotions.promotions_results';
    public $timestamps = false;
    protected $guarded = array('id');
    protected $fillable = [
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
        'post_lift_ordered_amount',
        'post_lift_ordered_units',
        'calculated_investment_amount',
        'no_of_promotion_days',
    ];
    private $spinput;
    private $sdcalc;
    private $swcalc;
    private $calendar;
    private $merge;

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

        $this->weekly_baseline_date = $this->sdcalc->weekly_baseline_date;
        $this->weekly_baseline_start_week = $this->calendar->get_week_sat($this->weekly_baseline_date);
        $this->weekly_baseline_end_week = date('Y-m-d', strtotime($this->promo_start_week . '-1 weeks'));

        $this->post_weekly_baseline_date = $this->sdcalc->post_weekly_baseline_date;
        $this->post_weekly_baseline_start_week = date('Y-m-d', strtotime($this->promo_start_week . '+1 weeks'));
        $this->post_weekly_baseline_end_week = $this->calendar->get_week_sat($this->post_weekly_baseline_date);

        
    }

    function create_record() {
        $row = [];
        $row['promo_child_id'] = $this->spinput->promo_child_id;
        $row['material_id'] = $this->spinput->material_id;
        $row['rtl_id'] = $this->spinput->retailer_id;
        $row['asin'] = $this->spinput->asin;        
        $row['product_name'] = $this->spinput->data['product_name'];
        
        // Basilene days (Normalized)
        $row['daily_baseline_pos_sales'] = $this->swcalc->get_avg_column('normalized_pos_sales', $this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']);
        $row['daily_baseline_pos_units'] = round($this->swcalc->get_avg_column('normalized_pos_units', $this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']));
        $row['daily_baseline_ordered_amount'] = $this->swcalc->get_avg_column('normalized_ordered_amount', $this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']);
        $row['daily_baseline_ordered_units'] = round($this->swcalc->get_avg_column('normalized_ordered_units', $this->spinput->calendar_dates['baseline']['start_week'], $this->spinput->calendar_dates['baseline']['end_week']));
        // Promotion  days
        $row['daily_during_pos_sales'] = $this->swcalc->get_avg_column('pos_sales', $this->spinput->calendar_dates['during']['start_week'], $this->spinput->calendar_dates['during']['end_week']);
        $row['daily_during_pos_units'] = round($this->swcalc->get_avg_column('pos_units', $this->spinput->calendar_dates['during']['start_week'], $this->spinput->calendar_dates['during']['end_week']));
        $row['daily_during_ordered_amount'] = $this->swcalc->get_avg_column('ordered_amount', $this->spinput->calendar_dates['during']['start_week'], $this->spinput->calendar_dates['during']['end_week']);
        $row['daily_during_ordered_units'] = round($this->swcalc->get_avg_column('ordered_units', $this->spinput->calendar_dates['during']['start_week'], $this->spinput->calendar_dates['during']['end_week']));
        // Post days
        $row['daily_post_pos_sales'] = $this->swcalc->get_avg_column('pos_sales', $this->spinput->calendar_dates['post']['start_week'], $this->spinput->calendar_dates['post']['end_week']);
        $row['daily_post_pos_units'] = round($this->swcalc->get_avg_column('pos_units', $this->spinput->calendar_dates['post']['start_week'], $this->spinput->calendar_dates['post']['end_week']));
        $row['daily_post_ordered_amount'] = $this->swcalc->get_avg_column('ordered_amount', $this->spinput->calendar_dates['post']['start_week'], $this->spinput->calendar_dates['post']['end_week']);
        $row['daily_post_ordered_units'] = round($this->swcalc->get_avg_column('ordered_units', $this->spinput->calendar_dates['post']['start_week'], $this->spinput->calendar_dates['post']['end_week']));
        
        //Others
        $row['during_incremental_ordered_amount'] = $this->calc('during_incremental_ordered_amount', $row);
        $row['during_incremental_ordered_units'] = $this->calc('during_incremental_ordered_units', $row);
        $row['during_incremental_pos_sales'] = $this->calc('during_incremental_pos_sales', $row);
        $row['during_incremental_pos_units'] = $this->calc('during_incremental_pos_units', $row);
        $row['post_incremental_ordered_amount'] = $this->calc('post_incremental_ordered_amount', $row);
        $row['post_incremental_ordered_units'] = $this->calc('post_incremental_ordered_units', $row);
        $row['post_incremental_pos_sales'] = $this->calc('post_incremental_pos_sales', $row);
        $row['post_incremental_pos_units'] = $this->calc('post_incremental_pos_units', $row);
        $row['during_lift_ordered_amount'] = $this->calc('during_lift_ordered_amount', $row);
        $row['during_lift_ordered_units'] = $this->calc('during_lift_ordered_units', $row);
        $row['post_lift_ordered_amount'] = $this->calc('post_lift_ordered_amount', $row);
        $row['post_lift_ordered_units'] = $this->calc('post_lift_ordered_units', $row);
        $row['no_of_promotion_days'] = $this->number_of_promotion_days;
        echo "Inserting output for child item id {$this->spinput->promo_child_id} \n";
        self::create($row);
    }
    
    function calc($key, $row) {
        switch ($key){
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
            
            case 'during_lift_ordered_amount':
                return $this->merge->safe_division($row['daily_during_ordered_amount'], $row['daily_baseline_ordered_amount'] ) - 1;
                return $this->merge->safe_division($row['daily_during_ordered_amount'], $row['daily_baseline_ordered_amount'] ) - 1;
                break;
            
            case 'during_lift_ordered_units':
                return $this->merge->safe_division($row['daily_during_ordered_units'], $row['daily_baseline_ordered_units'], true) - 1;
                break;
            case 'post_lift_ordered_amount':
                return $this->merge->safe_division($row['daily_post_ordered_amount'], $row['daily_baseline_ordered_amount'] ) - 1;
                break;
            case 'post_lift_ordered_units':
                return $this->merge->safe_division($row['daily_post_ordered_units'], $row['daily_baseline_ordered_units'], true) - 1;
                break;
        }
        return false;
    }



}
