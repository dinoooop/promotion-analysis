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

    protected $table = 'promo_pod';
    public $timestamps = false;
    protected $guarded = array('id');
    protected $fillable = [
        'promo_id',
        'year',
        'ordered_amount_during',
        'wkly_baseline',
        'baseline',
        'incremental_d',
        'incremental_p',
        'wkly_avg_ordered_amount_post_2_wks',
        'wkly_pull_forward_halo_d',
        'pull_forward_halo_d',
        'pull_forward_halo_p',
        'pos_during',
        'cogs_during',
        'ppm_p_during',
        'pos_during_baseline_period',
        'cogs_during_baseline_period',
        'ppm_p_baseline',
        'ordered_qty_during',
        'investment_unit',
        'funding_source',
        'investment',
        'roi',
        'forecast_qty',
        'fcst_investment',
        'discount_amount',
    ];

    function set_vars($input) {
        $this->calendar = new Calendar;
        $this->merge = new Merge;

        $this->swcalc = $input;

        $this->promo_id = $this->swcalc->sdcalc->spinput->promo_id;
        $this->promo_start_date = $this->swcalc->sdcalc->spinput->data['start_date'];
        $this->promo_end_date = $this->swcalc->sdcalc->spinput->data['end_date'];
        $this->promo_start_week = $this->calendar->get_week_sat($this->promo_start_date);
        $this->promo_end_week = $this->calendar->get_week_sat($this->promo_end_date);

        $this->weekly_baseline_date = $this->swcalc->sdcalc->spinput->weekly_baseline_date;
        $this->weekly_baseline_start_week = $this->calendar->get_week_sat($this->weekly_baseline_date);
        $this->weekly_baseline_end_week = date('Y-m-d', strtotime($this->promo_start_week . '-1 weeks'));

        $this->post_weekly_baseline_date = $this->swcalc->sdcalc->spinput->post_weekly_baseline_date;
        $this->post_weekly_baseline_start_week = date('Y-m-d', strtotime($this->promo_start_week . '+1 weeks'));
        $this->post_weekly_baseline_end_week = $this->calendar->get_week_sat($this->post_weekly_baseline_date);
        
        // Setting DB values
        $this->ordered_amount_during = $this->get_sum_promo_period('ordered_amount');
        $this->wkly_baseline = $this->get_avg_prior_promo_period('normalized_ordered_amount');
        $this->baseline = $this->wkly_baseline / 7;
        $this->incremental_d = $this->ordered_amount_during - $this->baseline;
        $this->incremental_p = $this->merge->safe_division($this->ordered_amount_during - $this->baseline, $this->baseline);
        $this->wkly_avg_ordered_amount_post_2_wks = $this->get_avg_post_promo_period('normalized_ordered_amount');
        $this->wkly_pull_forward_halo_d = $this->wkly_avg_ordered_amount_post_2_wks - $this->wkly_baseline;
        $this->pull_forward_halo_d = $this->wkly_pull_forward_halo_d * 2;
        $this->pull_forward_halo_p = $this->merge->safe_division($this->wkly_avg_ordered_amount_post_2_wks - $this->wkly_baseline, $this->wkly_baseline) * 100;
        $this->pos_during = $this->get_sum_promo_period('pos_sales');
        $this->cogs_during = $this->get_sum_promo_period('pos_shipped_cog_sold');
        $this->ppm_p_during = $this->merge->safe_division($this->pos_during - $this->cogs_during, $this->pos_during) * 100;
        $this->pos_during_baseline_period = $this->get_avg_prior_promo_period('pos_sales') / 7;
        $this->cogs_during_baseline_period = $this->get_avg_prior_promo_period('pos_shipped_cog_sold') / 7;
        $this->ppm_p_baseline = $this->merge->safe_division($this->pos_during_baseline_period - $this->cogs_during_baseline_period, $this->pos_during_baseline_period) * 100;
        $this->ordered_qty_during = $this->get_sum_promo_period('ordered_units');
        $this->investment_unit = $this->swcalc->sdcalc->spinput->data['investment_d'];
        $this->funding_source = $this->swcalc->sdcalc->spinput->data['investment_d'];
        $this->investment = $this->investment_unit * $this->ordered_qty_during;
        $this->roi = $this->merge->safe_division($this->incremental_d + $this->pull_forward_halo_d, $this->investment);
        $this->forecast_qty = $this->swcalc->sdcalc->spinput->data['forecasted_units'];
        $this->fcst_investment = $this->investment_unit * $this->forecast_qty;
        $this->discount_amount = $this->swcalc->sdcalc->spinput->data['investment_d'];
    }

    function create_record() {

        $row = [
            'promo_id' => $this->promo_id,
            'year' => $this->swcalc->sdcalc->spinput->year,
            'ordered_amount_during' => $this->ordered_amount_during,
            'wkly_baseline' => $this->wkly_baseline,
            'baseline' => $this->baseline,
            'incremental_d' => $this->incremental_d,
            'incremental_p' => $this->incremental_p,
            'wkly_avg_ordered_amount_post_2_wks' => $this->wkly_avg_ordered_amount_post_2_wks,
            'wkly_pull_forward_halo_d' => $this->wkly_pull_forward_halo_d,
            'pull_forward_halo_d' => $this->pull_forward_halo_d,
            'pull_forward_halo_p' => $this->pull_forward_halo_p,
            'pos_during' => $this->pos_during,
            'cogs_during' => $this->cogs_during,
            'ppm_p_during' => $this->ppm_p_during,
            'pos_during_baseline_period' => $this->pos_during_baseline_period,
            'cogs_during_baseline_period' => $this->cogs_during_baseline_period,
            'ppm_p_baseline' => $this->ppm_p_baseline,
            'ordered_qty_during' => $this->ordered_qty_during,
            'investment_unit' => $this->investment_unit,
            'funding_source' => $this->funding_source,
            'investment' => $this->investment,
            'roi' => $this->roi,
            'forecast_qty' => $this->forecast_qty,
            'fcst_investment' => $this->fcst_investment,
            'discount_amount' => $this->discount_amount,
        ];

        self::create($row);
    }

    function get_sum_promo_period($column) {
        return Sdcalc::whereBetween('date_day', [$this->promo_start_date, $this->promo_end_date])
                        ->where('promo_id', $this->promo_id)
                        ->sum($column);
    }

    function get_avg_prior_promo_period($column) {
        return Swcalc::whereBetween('week', [$this->weekly_baseline_start_week, $this->weekly_baseline_end_week])
                        ->where('promo_id', $this->promo_id)
                        ->avg($column);
    }

    function get_avg_post_promo_period($column) {
        return Swcalc::whereBetween('week', [$this->post_weekly_baseline_start_week, $this->post_weekly_baseline_end_week])
                        ->where('promo_id', $this->promo_id)
                        ->avg($column);
    }

}
