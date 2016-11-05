<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sdcalc;
use App\Redshift\Dsales;
use Illuminate\Support\Facades\Log;

class Spod extends Model {

    protected $table = 'promo_pod';
    protected $guarded = array('id');
    protected $fillable = [
        'material_id',
        'retailer_id',
        'item_id',
        
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
        $this->data = $input;
    }

    function create_record() {

        $row = [
            'material_id' => $this->data->material_id,
            'retailer_id' => $this->data->retailer_id,
            'item_id' => $this->data->item_id,
            'promo_id' => $this->data->item_id,
            'year' => $this->data->year,
            'ordered_amount_during' => $this->get_ordered_amount_during(),
            'wkly_baseline' => $this->get_wkly_baseline(),
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
    }
    
    function get_ordered_amount_during() {
        $ordered_amount = Sdcalc::whereBetween('date', [$this->data->promo_start_date, $this->data->promo_start_date])
                ->sum('ordered_amount');
        return $ordered_amount;
    }
    
    function get_wkly_baseline() {
        
    }

}
