<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Sdcalc;
use App\Merge;
use App\Calendar;

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
        'quarter_ordered_amount',
        'normalized_ordered_amount',
        'quarter_ordered_units',
        'normalized_ordered_units',
    ];
    private $merge;
    private $sdcalc;
    private $spinput;
    public static $messages = [];

    function inject($spinput, $sdcalc) {
        $this->merge = new Merge;
        $this->calendar = new Calendar;
        $this->spinput = $spinput;
        $this->sdcalc = $sdcalc;
        $this->save_records();
    }

    public static function status($input, $type = 'create') {

        $input = Dot::empty_strings2null($input);
        

        if ($type == 'create') {
            // Create rule
            $input = self::sanitize($input);
            $validation = Validator::make($input, self::store_rules($input), self::$messages);
        } else {
            // Update rule
            $input = self::sanitize($input, 'update');
            $validation = Validator::make($input, self::store_rules_update($input), self::$messages);
        }
        if ($validation->passes()) {
            return ['status' => true, 'input' => $input];
        } else {
            return [
                'status' => false,
                'validation' => $validation
            ];
        }
    }

    public static function store_rules($param) {
        return [
            'promo_child_id' => 'required',
            'week' => 'required',
        ];
    }

    public static function store_rules_update($param) {
        return [];
    }

    public static function sanitize($input, $type = 'create') {

        $sanitize = [
//            'promo_child_id',
//            'week',
//            'quarter',
            'ordered_amount' => Dot::sanitize_numeric('ordered_amount', $input),
            'ordered_units' => Dot::sanitize_numeric('ordered_units', $input, 0),
            'quarter_ordered_amount' => Dot::sanitize_numeric('quarter_ordered_amount', $input),
            'quarter_ordered_units' => Dot::sanitize_numeric('quarter_ordered_units', $input, 0),
            'normalized_ordered_amount' => Dot::sanitize_numeric('normalized_ordered_amount', $input),
            'normalized_ordered_units' => Dot::sanitize_numeric('normalized_ordered_units', $input, 0),
        ];

        if ($type = 'update') {
            foreach ($input as $key => $value) {
                if (isset($sanitize[$key])) {
                    $input[$key] = $sanitize[$key];
                }
            }
            
            return $input;
        }


        return array_merge($input, $sanitize);
    }

    function basic_week_data() {
        // SELECT SUM(ordered_amount) FROM promo_week WHERE promo_child_id = 3

        $raw = [
            'ordered_amount',
            'ordered_units',
        ];

        $sum_raw_select = $this->merge->create_sum_select_raw($raw);

        $records = Sdcalc::selectRaw("week, $sum_raw_select")
                        ->where('promo_child_id', $this->spinput->promo_child_id)
                        ->where('ordered_amount', '>', 0)
                        ->where('ordered_units', '>', 0)
                        ->groupBy('week')
                        ->get()->toArray();
        return $records;
    }

    function basic_quarter_data($start_week, $end_week) {
        $raw = [
            'ordered_amount',
            'ordered_units',
        ];

        $sum_raw_select = $this->merge->create_avg_select_raw($raw);
        $records = self::selectRaw($sum_raw_select)
                ->where('promo_child_id', $this->spinput->promo_child_id)
                ->where('ordered_amount', '>', 0)
                ->where('ordered_units', '>', 0)
                ->whereBetween('week', [$start_week, $end_week])
                ->first();
        return $records;
    }

    function save_records() {
        
        $records_week = $this->basic_week_data();
        
        foreach ($records_week as $key => $record) {

            $raw = [];

            $raw['promo_child_id'] = $this->spinput->promo_child_id;
            $raw['week'] = $record['week'];
//            $raw['quarter'] = $this->calendar->get_quarter_id($record['week']);
            $raw['ordered_amount'] = $record['ordered_amount'];
            $raw['ordered_units'] = $record['ordered_units'];

            $validation = self::status($raw);
            if ($validation['status']) {
                self::create($validation['input']);
            }
        }

        $raw = [];
        foreach ($records_week as $key => $record) {
            // Normalize the data

            if (in_array($record['week'], $this->spinput->calendar_dates['baseline']['weeks'])) {

                $swcalc = self::where('week', $record['week'])
                        ->where('promo_child_id', $this->spinput->promo_child_id)
                        ->first();

                $range = $this->spinput->calendar_dates['baseline']['range'][$record['week']];
                $records_quarter = $this->basic_quarter_data($range['start_week'], $range['end_week']);

                $raw['quarter_ordered_amount'] = $records_quarter['ordered_amount'];
                $swcalc['quarter_ordered_amount'] = $raw['quarter_ordered_amount'];
                $raw['normalized_ordered_amount'] = $this->calc('normalized_ordered_amount', $swcalc);

                $raw['quarter_ordered_units'] = $records_quarter['ordered_units'];
                $swcalc['quarter_ordered_units'] = $raw['quarter_ordered_units'];
                $raw['normalized_ordered_units'] = $this->calc('normalized_ordered_units', $swcalc);

                $validation_u = self::status($raw, 'update');
                if ($validation_u['status']) {
                    $swcalc->update($validation_u['input']);
                }
            }
        }
    }

    function calc($find, $input) {
        
        switch ($find) {
            case 'normalized_ordered_amount':
                if (abs($input['ordered_amount']) > abs($this->spinput->baseline_threshold * $input['quarter_ordered_amount'])) {
                    return $input['quarter_ordered_amount'];
                } else {
                    return $input['ordered_amount'];
                }
                break;
                
            case 'normalized_ordered_units':
                if (abs($input['ordered_units']) > abs($this->spinput->baseline_threshold * $input['quarter_ordered_units'])) {
                    return $input['quarter_ordered_units'];
                } else {
                    return $input['ordered_units'];
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
        $return = self::whereBetween('week', [$start_date, $end_date])
                ->where('promo_child_id', $this->spinput->promo_child_id)
                ->avg($column);
        return Dot::sanitize_numeric($return);
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
        $return = self::whereIn('id', $ids)
                ->avg($column);
        return Dot::sanitize_numeric($return);
    }

}
