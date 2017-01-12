<?php

namespace App\promotions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Dot;
use App\Merge;
use App\Calendar;
use App\Temp;
use App\Stock;
use App\Spinput;

class Promotion extends Model {

    protected $table = 'promotions.promotions_master_input';
    protected $guarded = array('id');
    protected $fillable = [
        'promotions_name',
        'promotions_description',
        'promotions_startdate',
        'promotions_enddate',
        'retailer',
        'retailer_country_id',
        'retailer_country',
        'newell_status',
        'promotions_status',
        'promotions_type',
        'level_of_promotions',
        'marketing_type',
        'annivarsaried',
        'promotions_budget',
        'promotions_projected_sales',
        'promotions_expected_lift',
        'promotions_budget_type',
        'brand_id',
        'brand',
        'category',
        'division',
        'status'
    ];
    protected $csv = [
        'promotions_name',
        'promotions_description',
        'promotions_startdate',
        'promotions_enddate',
        'retailer',
        'retailer_country_id',
        'retailer_country',
        'newell_status',
        'promotions_status',
        'promotions_type',
        'level_of_promotions',
        'marketing_type',
        'annivarsaried',
        'promotions_budget',
        'promotions_projected_sales',
        'promotions_expected_lift',
        'promotions_budget_type',
        'brand_id',
        'brand',
        'category',
        'division',
    ];
    private $merge;
    private $calendar;
    public static $messages = [
        'itemcomp' => 'Given :attribute repeated for promotion',
        'eaqualafter' => 'Given :attribute must be eaqual or greater than promotions start date',
        'masin' => 'You must enter either material id or ASIN',
    ];

    public static function status($input) {

        $validation_primery = Validator::make($input, self::store_rules_primary($input), self::$messages);

        if (!$validation_primery->passes()) {
            return [
                'status' => false,
                'validation' => $validation_primery
            ];
        }

        $input = Dot::empty_strings2null($input);
        $input = self::sanitize($input);

        $validation = Validator::make($input, self::store_rules_secondary($input), self::$messages);

        if ($validation->passes()) {
            return ['status' => true, 'input' => $input];
        } else {
            return [
                'status' => false,
                'validation' => $validation
            ];
        }
    }

    public static function store_rules_primary($param) {
        return [
            'promotions_name' => 'required',
        ];
    }

    public static function store_rules_secondary($param) {
        return [
            'promotions_name' => 'required',
            //'promotions_description',
            'promotions_startdate' => 'required|date',
            'promotions_enddate' => "bail|required|date|eaqualafter:{$param['promotions_startdate']}",
            'retailer' => 'required',
            //'retailer_country_id',
            //'retailer_country',
            //'newell_status',
            //'promotions_status',
//            'promotions_type',
            'level_of_promotions' => 'required',
//            'marketing_type',
//            'annivarsaried',
            'promotions_budget' => 'numeric|nullable',
            'promotions_projected_sales' => 'numeric|nullable',
            'promotions_expected_lift' => 'numeric|nullable',
//            'promotions_budget_type',
//            'brand_id',
            'brand' => 'required_if:level_of_promotions,Brand',
            'category' => 'required_if:level_of_promotions,Category',
//            'division',
            'status' => 'required'
        ];
    }

    /**
     * 
     * Change not exist and unexpected values to default
     * @param type $input
     * @return type
     */
    public static function sanitize($input) {




        $default = [
            'retailer' => 'Amazon',
            'retailer_country' => 'US',
            'newell_status' => 'Approved',
            'promotions_status' => 'Not Started',
            'promotions_type' => 'Best Deals',
            'level_of_promotions' => 'Item Level',
            'marketing_type' => 'Price Promotion',
            'status' => 'active',
        ];

        $sanitize = [
            'promotions_name' => trim($input['promotions_name']),
            //'promotions_description' => $input['promotions_description'],
            //'promotions_startdate' => date('Y-m-d', strtotime($input['promotions_startdate'])),
            //'promotions_enddate' => date('Y-m-d', strtotime($input['promotions_enddate'])),
            //'retailer',
            //'retailer_country_id',
            //'retailer_country',
            //'newell_status',
            //'promotions_status',
            //'promotions_type',
            //'level_of_promotions',
            //'marketing_type',
            'annivarsaried' => Dot::sanitize_boolean('annivarsaried', $input),
            'promotions_budget' => Dot::sanitize_numeric('promotions_budget', $input),
            'promotions_projected_sales' => Dot::sanitize_numeric('promotions_projected_sales', $input),
            'promotions_expected_lift' => Dot::sanitize_numeric('promotions_expected_lift', $input),
                //'promotions_budget_type',
                //'brand_id',
                //'brand',
                //'category',
                //'product_family',
                //'product_line',
                //'division',
        ];

        foreach ($default as $key => $value) {
            $sanitize[$key] = Dot::get_first_second($key, $input, $default);
        }

        return array_merge($input, $sanitize);
    }

    public static function display_prepare($input) {
        $input->promotions_startdate = date('m/d/Y', strtotime($input->promotions_startdate));
        $input->promotions_enddate = date('m/d/Y', strtotime($input->promotions_enddate));

        $input->button_result = Temp::button_result($input);
        $input->status = Stock::get_value('status', $input->status);

        $url_param = ['pid' => $input->promotions_id, 'pci' => $input->promo_child_id, 'type' => 'day'];
        $input->href_preperation_table = route('preparation_table', $url_param);

        $url_param = ['pid' => $input->promotions_id, 'pci' => $input->promo_child_id, 'type' => 'week'];
        $input->href_week_preperation_table = route('preparation_table', $url_param);
        return $input;
    }

    /**
     * 
     * @param type $input
     * @return type
     * Display the final result
     */
    public static function display_prepare_output($input) {
        $merge = new Merge();
        $promotion = Promotion::find($input->promotions_id)->toArray();
        $settings = $merge->admin_settings($promotion);
        $post_weeks = $settings['post_weeks'];


        if ($promotion['promotions_startdate'] == $promotion['promotions_enddate']) {
            // SINGLE DAY PROMOTION
            // 
            // Baseline (daily) 
            $input->daily_baseline_ordered_amount = $input->baseline_ordered_amount;
            $input->daily_baseline_ordered_units = round($input->baseline_ordered_units);

            // Baseline (wkly) 
            $input->wkly_baseline_ordered_amount = $input->baseline_ordered_amount * 7;
            $input->wkly_baseline_ordered_units = round($input->baseline_ordered_units * 7);

            // During (daily)
            $input->daily_during_ordered_amount = $input->during_ordered_amount;
            $input->daily_during_ordered_units = $input->during_ordered_units;

            //Post (wkly)
            $input->wkly_post_ordered_amount = $input->post_ordered_amount * 7;
            $input->wkly_post_ordered_units = round($input->post_ordered_units * 7);

            //DURING_INCREMENTAL
            $input->daily_during_incremental_ordered_amount = $input->daily_during_ordered_amount - $input->daily_baseline_ordered_amount;
            $input->daily_during_incremental_ordered_units = $input->daily_during_ordered_units - $input->daily_baseline_ordered_units;


            //POST INCREMENTAL 
            $input->wkly_post_incremental_ordered_amount = $input->wkly_post_ordered_amount - $input->wkly_baseline_ordered_amount;
            $input->wkly_post_incremental_ordered_units = $input->wkly_post_ordered_units - $input->wkly_baseline_ordered_units;

            $input->total_during_incremental_ordered_amount = $input->daily_during_incremental_ordered_amount;
            $input->total_during_incremental_ordered_units = $input->daily_during_incremental_ordered_units;

            $input->total_post_incremental_ordered_amount = ($input->wkly_post_ordered_amount - $input->wkly_baseline_ordered_amount) * $post_weeks;
            $input->total_post_incremental_ordered_units = ($input->wkly_post_ordered_units - $input->wkly_baseline_ordered_units) * $post_weeks;

            $input->during_lift_ordered_amount = $merge->safe_division($input->daily_during_ordered_amount, $input->daily_baseline_ordered_amount) - 1;
            $input->during_lift_ordered_amount = round($input->during_lift_ordered_amount, 2);

            $input->during_lift_ordered_units = $merge->safe_division($input->during_ordered_units, $input->baseline_ordered_units) - 1;
            $input->during_lift_ordered_units = round($input->during_lift_ordered_units);

            $input->post_lift_ordered_amount = $merge->safe_division($input->wkly_post_ordered_amount, $input->wkly_baseline_ordered_amount) - 1;
            $input->post_lift_ordered_amount = round($input->post_lift_ordered_amount, 2);

            $input->post_lift_ordered_units = $merge->safe_division($input->wkly_post_ordered_units, $input->wkly_baseline_ordered_units) - 1;
            $input->post_lift_ordered_units = round($input->post_lift_ordered_units, 2);
        } else {
            // MULTIPLE DAY PROMOTIONS ---------------------------------------------
            // Baseline (daily) 
            $input->daily_baseline_ordered_amount = $input->baseline_ordered_amount;
            $input->daily_baseline_ordered_units = round($input->baseline_ordered_units);

            // Baseline (wkly) 
            $input->wkly_baseline_ordered_amount = $input->baseline_ordered_amount * 7;
            $input->wkly_baseline_ordered_units = round($input->baseline_ordered_units * 7);



            // During (wkly)
            $input->wkly_during_ordered_amount = $input->during_ordered_amount * 7;
            $input->wkly_during_ordered_units = round($input->during_ordered_units * 7);

            //Post (wkly)
            $input->wkly_post_ordered_amount = $input->post_ordered_amount * 7;
            $input->wkly_post_ordered_units = round($input->post_ordered_units * 7);

            //DURING_INCREMENTAL
            $input->wkly_during_incremental_ordered_amount = round($input->wkly_during_ordered_amount - $input->wkly_baseline_ordered_amount, 2);
            $input->wkly_during_incremental_ordered_units = $input->wkly_during_ordered_units - $input->wkly_baseline_ordered_units;

            //POST INCREMENTAL 
            $input->wkly_post_incremental_ordered_amount = $input->wkly_post_ordered_amount - $input->wkly_baseline_ordered_amount;
            $input->wkly_post_incremental_ordered_units = $input->wkly_post_ordered_units - $input->wkly_baseline_ordered_units;

            $input->total_during_incremental_ordered_amount = round(($input->wkly_during_ordered_amount - $input->wkly_baseline_ordered_amount) * ($input->no_of_promotion_days / 7), 2);
            $input->total_during_incremental_ordered_units = round(($input->wkly_during_ordered_units - $input->wkly_baseline_ordered_units) * ($input->no_of_promotion_days / 7));

            $input->total_post_incremental_ordered_amount = round(($input->wkly_post_ordered_amount - $input->wkly_baseline_ordered_amount) * $post_weeks, 2);
            $input->total_post_incremental_ordered_units = round(($input->wkly_post_ordered_units - $input->wkly_baseline_ordered_units) * $post_weeks);

            $input->during_lift_ordered_amount = $merge->safe_division($input->wkly_during_ordered_amount, $input->wkly_baseline_ordered_amount) - 1;
            $input->during_lift_ordered_amount = round($input->during_lift_ordered_amount, 2);

            $input->during_lift_ordered_units = $merge->safe_division($input->wkly_during_ordered_units, $input->wkly_baseline_ordered_units) - 1;
            $input->during_lift_ordered_units = round($input->during_lift_ordered_units, 2);

            $input->post_lift_ordered_amount = $merge->safe_division($input->wkly_post_ordered_amount, $input->wkly_baseline_ordered_amount) - 1;
            $input->post_lift_ordered_amount = round($input->post_lift_ordered_amount, 2);

            $input->post_lift_ordered_units = $merge->safe_division($input->wkly_post_ordered_units, $input->wkly_baseline_ordered_units) - 1;
            $input->post_lift_ordered_units = round($input->post_lift_ordered_units, 2);
        }
        $input->promotions_startdate = date('m/d/Y', strtotime($input->promotions_startdate));
        $input->promotions_enddate = date('m/d/Y', strtotime($input->promotions_enddate));

        $input->button_result = Temp::button_result($input);
        $input->status = Stock::get_value('status', $input->status);

        $url_param = ['pid' => $input->promotions_id, 'pci' => $input->promo_child_id, 'type' => 'day'];
        $input->href_preperation_table = route('preparation_table', $url_param);

        $url_param = ['pid' => $input->promotions_id, 'pci' => $input->promo_child_id, 'type' => 'week'];
        $input->href_week_preperation_table = route('preparation_table', $url_param);
        return $input;
    }

    public static function update_promotion_status($promotion_id, $status) {
        self::where('id', $promotion_id)
                ->update(['status' => $status]);
    }

    function csv_match_data($record) {
        $row = [];
        foreach ($this->csv as $key => $value) {
            if (!isset($record[$key])) {
                continue;
            }
            $row[$value] = $record[$key];
        }
        return $row;
    }

    function csv_validate_file($records) {
        return $this->csv === $records;
    }

}
