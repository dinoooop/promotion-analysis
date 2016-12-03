<?php

namespace App\promotions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Dot;
use App\Merge;
use App\Calendar;
use App\Redshift\Pgquery;
use App\Option;

class Item extends Model {

    protected $table = 'promotions.promotions_child_input';
    protected $guarded = array('id');
    public $timestamps = false;
    protected $fillable = [
        'promotions_id',
        'promotions_startdate',
        'promotions_enddate',
        'material_id',
        'product_name',
        'asin',
        'rtl_id',
        'promotions_budget',
        'promotions_projected_sales',
        'promotions_expected_lift',
        'x_plant_material_status',
        'x_plant_status_date',
        'promotions_budget_type',
        'funding_per_unit',
        'forecasted_qty',
        'forecasted_unit_sales',
        'promoted',
        'user_input',
        'validated',
        'percent_discount',
        'price_discount',
        'reference',
    ];
    public static $form_create_rules = [
        'promotions_id' => 'required|integer',
        'forecasted_qty' => 'integer'
    ];
    public static $form_edit_rules = [
        'promotions_id' => 'required|integer',
        'forecasted_qty' => 'integer'
    ];

    /**
     * 
     * Custom validate user input to DB (form, CSV)
     * @param array $input
     * @return boolean
     */
    public static function validate($input) {

        // validate only if date is set
        if (isset($input['promotions_startdate']) && isset($input['promotions_enddate'])) {

            if (!Dot::validate_date($input['promotions_startdate']) || !Dot::validate_date($input['promotions_enddate'])) {
                $error['message'][] = 'Please enter a valid date';
                $error['status'] = false;
                return $error;
            }

            if ($input['promotions_startdate'] > $input['promotions_enddate']) {
                $error['message'][] = 'Start date is greater than end date';
                $error['status'] = false;
                return $error;
            }
        }

        if (isset($input['x_plant_status_date'])) {
            if (!Dot::validate_date($input['x_plant_status_date'])) {
                $error['message'][] = 'Please enter a valid date';
                $error['status'] = false;
                return $error;
            }
        }

        if (!Dot::validate_true('material_id', $input)) {
            if (!Dot::validate_true('rtl_id', $input)) {
                $error['message'][] = 'There are no material id or retailer id';
                $error['status'] = false;
                return $error;
            }
        }


        $error['status'] = true;
        return $error;
    }

    public static function sanitize($input) {
        $sanitize = [
            'promotions_budget' => Dot::sanitize_numeric('promotions_budget', $input),
            'promotions_projected_sales' => Dot::sanitize_numeric('promotions_projected_sales', $input),
            'promotions_expected_lift' => Dot::sanitize_numeric('promotions_expected_lift', $input),
            'forecasted_unit_sales' => Dot::sanitize_numeric('forecasted_unit_sales', $input),
            'funding_per_unit' => Dot::sanitize_numeric('funding_per_unit', $input),
            'forecasted_qty' => Dot::sanitize_numeric('forecasted_qty', $input),
            'percent_discount' => Dot::sanitize_numeric('percent_discount', $input),
            'price_discount' => Dot::sanitize_numeric('price_discount', $input),
            'promoted' => Dot::sanitize_boolean('promoted', $input),
            'user_input' => Dot::sanitize_boolean('user_input', $input),
            'validated' => Dot::sanitize_boolean('validated', $input),
        ];

        return array_merge($input, $sanitize);
    }

    public static function status($input) {
        $validation = Validator::make($input, self::$form_create_rules);
        $custom_validation = self::validate($input);
        if ($validation->passes() && $custom_validation['status']) {
            $input = self::sanitize($input);
            return ['status' => true, 'input' => $input];
        } else {
            return [
                'status' => false,
                'validation' => $validation,
                'custom_validation' => $custom_validation
            ];
        }
    }

    public static function display_prepare($input) {

        $input->promotions_startdate = date('m/d/Y', strtotime($input->promotions_startdate));
        $input->promotions_enddate = date('m/d/Y', strtotime($input->promotions_enddate));
        return $input;
    }

    function csv_match_data($record) {
        $row = [];
        foreach ($this->fillable as $key => $value) {
            if (!isset($record[$key])) {
                continue;
            }
            $row[$value] = $record[$key];
        }
        return $row;
    }

    function csv_validate_file($records) {

        return $this->fillable === $records;
    }

    function tabular_form_interpreter($input) {


        $expected = [
            0 => 'material_id', // column one
            1 => 'asin', // column one
            2 => 'promotions_startdate', // column one
            3 => 'promotions_enddate', // column one
            4 => 'promotions_budget',
            5 => 'promotions_projected_sales',
            6 => 'promotions_expected_lift',
            7 => 'promotions_budget_type',
            8 => 'funding_per_unit',
            9 => 'forecasted_qty',
            10 => 'forecasted_unit_sales',
        ];

        //$key is the row
        //$j is the column

        if (empty($input)) {
            return false;
        }

        $record = [];

        foreach ($input as $key => $value) {
            $row = [];
            for ($j = 0; $j < count($expected); $j++) {
                if (isset($input[$key][$j]) && isset($expected[$j])) {
                    $row[$expected[$j]] = $input[$key][$j];
                }
            }
            $record[$key] = $row;
        }


        return $record;
    }

    /**
     * 
     * Category level promotion may not contain items, create items
     */
    function insert_items_under_promotion($promotion, $param, $type) {

        if ($this->have_child_items($promotion)) {
            return true;
        }

        if ($type == 'category') {
            $records = Pgquery::get_items_category($param);
        }
        
        if ($type == 'brand') {
            $records = Pgquery::get_items_brand($param);
        }
        
        foreach ($records as $key => $record) {
            $input = $this->prepare_redshift_item($promotion, $record);
            $status = self::status($input);
            if ($status['status']) {
                self::create($input);
            }
        }

        $this->set_have_child_items($promotion, $type);
    }

    /**
     * 
     * Check wheather the child are availbale for a category level of promotion
     * @return boolean
     */
    function have_child_items($promotion) {
        $mata_key = 'have_child_items_' . $promotion->id;
        $option = Option::get($mata_key);

        if ($option) {

            if (isset($option['category']) && $option['category'] == $promotion->category) {
                // child item exist for the category
                return true;
            }

            if (isset($option['brand']) && $option['brand'] == $promotion->brand) {
                // child item exist for the brand
                return true;
            }
        }
        // child item doesn't exist
        return false;
    }

    /**
     * 
     * Set or change option have_child_items_{n} value
     */
    function set_have_child_items($promotion, $type) {
        $mata_key = 'have_child_items_' . $promotion->id;
        if ($type == 'category') {
            $value = [
                'category' => $promotion->category
            ];
        }
        if ($type == 'brand') {
            $value = [
                'brand' => $promotion->brand
            ];
        }

        Option::add($mata_key, $value);
    }

    /**
     * 
     * 
     * Prepare records from dim_material for promotions_child_input table
     * @param array $record
     */
    function prepare_redshift_item($promotion, $record) {

        return [
            'promotions_id' => $promotion->id,
            'promotions_startdate' => $promotion->promotions_startdate,
            'promotions_enddate' => $promotion->promotions_enddate,
            'material_id' => $record->material_id,
            'product_name' => $record->material_description,
            'asin' => $record->retailer_sku,
            'rtl_id' => $record->retailer_upc,
            'promotions_budget', // inheritted
            'promotions_projected_sales', // inheritted
            'promotions_expected_lift', // inheritted
            'x_plant_material_status' => $record->x_plant_matl_status,
            'x_plant_status_date' => $record->x_plant_valid_from,
            'promotions_budget_type', // inheritted
            'funding_per_unit', // @todo
            'forecasted_qty', // @todo
            'forecasted_unit_sales', // @todo
            'promoted' => 1, // @todo
            'user_input' => 0, // @todo
            'validated' => 1, // @todo
            'percent_discount', // @todo
            'price_discount', // @todo
            'reference', // @todo
        ];
    }

}
