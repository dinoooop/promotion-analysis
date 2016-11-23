<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sdcalc;
use App\Merge;
use App\Calendar;
use Illuminate\Support\Facades\Log;

class Swcalc extends Model {

    protected $table = 'promo_week';
    public $timestamps = false;
    protected $guarded = array('id');
    protected $fillable = [
        'promo_id',
        'week',
        'quarter',
        'pos_sales',
        'pos_qty',
        'ordered_amount',
        'ordered_units',
        'pos_shipped_cog_sold',
        'ordered_cogs',
        'wkly_avg_oa_quarterly',
        'normalized_ordered_amount',
        'avg_weekly_ordered_units_quarterly',
        'normalized_ordered_units',
        'normalized_ordered_cogs',
    ];
    private $merge;

    function set_vars($spinput, $sdcalc) {
        $this->merge = new Merge;
        $this->calendar = new Calendar;
        $this->spinput = $spinput;
        $this->sdcalc = $sdcalc;
        $this->save_records();
    }

    function basic_week_data() {
        // SELECT SUM(pos_sales) FROM promo_week WHERE promo_id = 3
        $select = [
            'promo_id',
            'week',
            'quarter',
            'pos_sales',
            'pos_qty',
            'ordered_amount',
            'ordered_units',
            'pos_shipped_cog_sold',
            'ordered_cogs'
        ];

        $raw = [
            'pos_sales',
            'pos_qty',
            'ordered_amount',
            'ordered_units',
            'pos_shipped_cog_sold',
            'ordered_cogs'
        ];

        $sum_raw_select = $this->merge->create_sum_select_raw($raw);

        $records = Sdcalc::selectRaw("week, $sum_raw_select")
                        ->where('promo_id', $this->spinput->promo_id)
                        ->groupBy('week')
                        ->get()->toArray();
        return $records;
    }

    function basic_quarter_data() {
        $raw = [
            'ordered_amount',
            'ordered_units'
        ];
        $sum_raw_select = $this->merge->create_sum_select_raw($raw);
        $records = Sdcalc::selectRaw($sum_raw_select)
                ->where('promo_id', $this->spinput->promo_id)
                ->groupBy('quarter')
                ->first();
        return $records;
    }

    function save_records() {
        $records_week = $this->basic_week_data();


        foreach ($records_week as $key => $record) {
            $raw = array();


            $raw['promo_id'] = $this->spinput->promo_id;
            $raw['week'] = $record['week'];
            $raw['quarter'] = $this->calendar->get_quarter_id($record['week']);
            $raw['pos_sales'] = $record['pos_sales'];
            $raw['pos_qty'] = $record['pos_qty'];
            $raw['ordered_amount'] = $record['ordered_amount'];
            $raw['ordered_units'] = $record['ordered_units'];
            $raw['pos_shipped_cog_sold'] = $record['pos_shipped_cog_sold'];
            $raw['ordered_cogs'] = $record['ordered_cogs'];

            $records_quarter = $this->basic_quarter_data();
            $quarter = $this->calendar->get_quarter_info($raw['quarter']);

            $raw['wkly_avg_oa_quarterly'] = $records_quarter['ordered_amount'] / $quarter['week_count'];
            $raw['normalized_ordered_amount'] = $this->calc('normalized_ordered_amount', $raw);
            $raw['avg_weekly_ordered_units_quarterly'] = $records_quarter['ordered_units'] / $quarter['week_count'];
            $raw['normalized_ordered_units'] = $this->calc('normalized_ordered_units', $raw);
            $raw['normalized_ordered_cogs'] = $this->calc('normalized_ordered_cogs', $raw);
            self::create($raw);
        }
    }

    function calc($find, $input) {
        switch ($find) {
            case 'normalized_ordered_amount':
                if ($input['ordered_amount'] > (1 + $this->merge->admin_settings('baseline_normalization_thresholds')) * $input['wkly_avg_oa_quarterly']) {
                    return $input['wkly_avg_oa_quarterly'];
                } else {
                    return $input['ordered_amount'];
                }
                break;
            case 'normalized_ordered_units':
                if ($input['ordered_units'] > (1 + $this->merge->admin_settings('baseline_normalization_thresholds')) * $input['avg_weekly_ordered_units_quarterly']) {
                    return $input['avg_weekly_ordered_units_quarterly'];
                } else {
                    return $input['ordered_units'];
                }
                break;
            case 'normalized_ordered_cogs':
                return ($input['normalized_ordered_units'] * $input['pos_shipped_cog_sold']) / $input['pos_qty'];
                break;
        }

        return false;
    }

}
