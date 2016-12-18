<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sdcalc;
use App\Merge;
use App\Calendar;
use Illuminate\Support\Facades\Log;

class Swcalc extends Model {

    protected $table = 'promotions.promo_week';
    public $timestamps = false;
    protected $guarded = array('id');
    protected $fillable = [
        'promo_child_id',
        'week',
        'quarter',
        'ordered_amount',
        'ordered_units',
        'pos_sales',
        'pos_units',
        'quarter_ordered_amount',
        'normalized_ordered_amount',
        'quarter_ordered_units',
        'normalized_ordered_units',
        'quarter_pos_sales',
        'normalized_pos_sales',
        'quarter_pos_units',
        'normalized_pos_units',
    ];
    private $merge;
    private $sdcalc;
    private $spinput;

    function inject($spinput, $sdcalc) {
        $this->merge = new Merge;
        $this->calendar = new Calendar;
        $this->spinput = $spinput;
        $this->sdcalc = $sdcalc;
        $this->save_records();
    }

    function basic_week_data() {
        // SELECT SUM(pos_sales) FROM promo_week WHERE promo_child_id = 3

        $raw = [
            'ordered_amount',
            'ordered_units',
            'pos_sales',
            'pos_units',
        ];

        $sum_raw_select = $this->merge->create_sum_select_raw($raw);

        $records = Sdcalc::selectRaw("week, $sum_raw_select")
                        ->where('promo_child_id', $this->spinput->promo_child_id)
                        ->groupBy('week')
                        ->get()->toArray();
        return $records;
    }

    function basic_quarter_data() {
        $raw = [
            'ordered_amount',
            'ordered_units',
            'pos_sales',
            'pos_units',
        ];
        $sum_raw_select = $this->merge->create_sum_select_raw($raw);
        $records = Sdcalc::selectRaw($sum_raw_select)
                ->where('promo_child_id', $this->spinput->promo_child_id)
                ->groupBy('quarter')
                ->first();
        return $records;
    }

    function save_records() {
        $records_week = $this->basic_week_data();


        foreach ($records_week as $key => $record) {

            $raw = [];

            $raw['promo_child_id'] = $this->spinput->promo_child_id;
            $raw['week'] = $record['week'];
            $raw['quarter'] = $this->calendar->get_quarter_id($record['week']);

            $raw['ordered_amount'] = $record['ordered_amount'];
            $raw['ordered_units'] = $record['ordered_units'];
            $raw['pos_sales'] = $record['pos_sales'];
            $raw['pos_units'] = $record['pos_units'];

            // Normalize the data
            $records_quarter = $this->basic_quarter_data();
            $quarter = $this->calendar->get_quarter_info($raw['quarter']);


            $raw['quarter_ordered_amount'] = $this->merge->safe_division($records_quarter['ordered_amount'], $quarter['week_count']);
            $raw['normalized_ordered_amount'] = $this->calc('normalized_ordered_amount', $raw);
            $raw['quarter_ordered_units'] = $this->merge->safe_division($records_quarter['ordered_units'], $quarter['week_count'], true);
            $raw['normalized_ordered_units'] = $this->calc('normalized_ordered_units', $raw);

            $raw['quarter_pos_sales'] = $this->merge->safe_division($records_quarter['pos_sales'], $quarter['week_count']);
            $raw['normalized_pos_sales'] = $this->calc('normalized_pos_sales', $raw);
            $raw['quarter_pos_units'] = $this->merge->safe_division($records_quarter['pos_units'], $quarter['week_count'], true);
            $raw['normalized_pos_units'] = $this->calc('normalized_pos_units', $raw);


            self::create($raw);
        }
    }

    function calc($find, $input) {
        switch ($find) {
            case 'normalized_ordered_amount':
                if ($input['ordered_amount'] > (1 + $this->spinput->baseline_threshold) * $input['quarter_ordered_amount']) {
                    return $input['quarter_ordered_amount'];
                } else {
                    return $input['ordered_amount'];
                }
                break;
            case 'normalized_ordered_units':
                if ($input['ordered_units'] > (1 + $this->spinput->baseline_threshold) * $input['quarter_ordered_units']) {
                    return $input['quarter_ordered_units'];
                } else {
                    return $input['ordered_units'];
                }
                break;

            case 'normalized_pos_sales':
                if ($input['pos_sales'] > (1 + $this->spinput->baseline_threshold) * $input['quarter_pos_sales']) {
                    return $input['quarter_pos_sales'];
                } else {
                    return $input['pos_sales'];
                }
                break;

            case 'normalized_pos_units':
                if ($input['pos_units'] > (1 + $this->spinput->baseline_threshold) * $input['quarter_pos_units']) {
                    return $input['quarter_pos_units'];
                } else {
                    return $input['pos_units'];
                }
                break;
        }

        return false;
    }

    /**
     * 
     * Get swcalc records by week (start week and end week)
     * @param string $start_date
     * @param string $end_date
     * @return obj
     */
    function get_swcalc_week($start_date, $end_date) {
        return self::whereBetween('week', [$start_date, $end_date])
                        ->where('promo_child_id', $this->spinput->promo_child_id)
                        ->get();
    }

    /**
     * 
     * Get avg for the column
     * @param type $column
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    function get_avg_column($column, $start_date, $end_date) {
        return self::whereBetween('week', [$start_date, $end_date])
                        ->where('promo_child_id', $this->spinput->promo_child_id)
                        ->avg($column);
    }

    /**
     * 
     * Get the avg based on the swcalc id
     * @param string $column column name to find avg
     * @return array
     *      */
    function get_avg_column_id($column, $ids) {
        if (empty($ids)) {
            return 0;
        }
        return self::whereIn('id', $ids)
                        ->avg($column);
    }

}
