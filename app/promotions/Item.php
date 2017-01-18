<?php

namespace App\promotions;

use Illuminate\Support\Facades\Log;
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
        'material_id',
        'asin',
        'rtl_id',
        'product_name',
        'promotions_startdate',
        'promotions_enddate',
        'promotions_budget',
        'promotions_projected_sales',
        'promotions_expected_lift',
        'x_plant_material_status',
        'x_plant_status_date',
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
    protected $csv = [
        'promotions_id',
        'material_id',
        'asin',
        'promotions_startdate',
        'promotions_enddate',
        'promotions_budget',
        'promotions_projected_sales',
        'promotions_expected_lift',
        'funding_per_unit',
        'forecasted_qty',
        'forecasted_unit_sales',
    ];
    public static $messages = [
        'itemcomp' => 'Given :attribute repeated for promotion',
        'eaqualafter' => 'Given :attribute must be eaqual or greater than promotions start date',
        'masin' => 'You must enter either material id or ASIN',
    ];

    public static function store_rules($param) {
        return [
            'promotions_id' => 'required',
            'material_id' => "masin:{$param['asin']}|itemcomp:{$param['promotions_id']}",
//            'asin' => '',
//            'rtl_id',
//            'product_name',
            'promotions_startdate' => 'required|date',
            'promotions_enddate' => "bail|required|date|eaqualafter:{$param['promotions_startdate']}",
            'promotions_budget' => 'numeric|nullable',
            'promotions_projected_sales' => 'numeric|nullable',
            'promotions_expected_lift' => 'numeric|nullable',
//            'x_plant_material_status',
            'x_plant_status_date' => 'date|nullable',
//            'promotions_budget_type',
            'funding_per_unit' => 'numeric|nullable',
            'forecasted_qty' => 'integer|nullable',
            'forecasted_unit_sales' => 'numeric|nullable',
            'promoted' => 'boolean',
            'user_input' => 'boolean',
            'validated' => 'boolean',
            'percent_discount' => 'numeric|nullable',
            'price_discount' => 'numeric|nullable',
//            'reference',
        ];
    }

    public static function store_rules_update($param) {
        return [
            'promotions_id' => 'required',
//            'asin' => '',
//            'rtl_id',
//            'product_name',
            'promotions_startdate' => 'required|date',
            'promotions_enddate' => "bail|required|date|eaqualafter:{$param['promotions_startdate']}",
            'promotions_budget' => 'numeric|nullable',
            'promotions_projected_sales' => 'numeric|nullable',
            'promotions_expected_lift' => 'numeric|nullable',
//            'x_plant_material_status',
            'x_plant_status_date' => 'date|nullable',
//            'promotions_budget_type',
            'funding_per_unit' => 'numeric|nullable',
            'forecasted_qty' => 'integer|nullable',
            'forecasted_unit_sales' => 'numeric|nullable',
            'promoted' => 'boolean',
            'user_input' => 'boolean',
            'validated' => 'boolean',
            'percent_discount' => 'numeric|nullable',
            'price_discount' => 'numeric|nullable',
//            'reference',
        ];
    }

    public static function status($input, $update = null) {
        $input = Dot::empty_strings2null($input);
        $input = self::sanitize($input);

        if (is_null($update)) {
            $validation = Validator::make($input, self::store_rules($input), self::$messages);
        } else {
            $validation = Validator::make($input, self::store_rules_update($input), self::$messages);
        }

        if ($validation->passes()) {
            return ['status' => true, 'input' => $input];
        } else {
            return [
                'status' => false,
                'validation' => $validation,
            ];
        }
    }

    public static function sanitize($input) {
        $sanitize = [
            'material_id' => Dot::sanitize_string('material_id', $input),
            'asin' => Dot::sanitize_string('asin', $input),
            'promotions_startdate' => date('Y-m-d', strtotime($input['promotions_startdate'])),
            'promotions_enddate' => date('Y-m-d', strtotime($input['promotions_enddate'])),
            'promotions_budget' => Dot::sanitize_numeric('promotions_budget', $input),
            'promotions_projected_sales' => Dot::sanitize_numeric('promotions_projected_sales', $input),
            'promotions_expected_lift' => Dot::sanitize_numeric('promotions_expected_lift', $input),
            'funding_per_unit' => Dot::sanitize_numeric('funding_per_unit', $input),
            'forecasted_qty' => Dot::sanitize_numeric('forecasted_qty', $input),
            'forecasted_unit_sales' => Dot::sanitize_numeric('forecasted_unit_sales', $input),
            'percent_discount' => Dot::sanitize_numeric('percent_discount', $input),
            'price_discount' => Dot::sanitize_numeric('price_discount', $input),
            'promoted' => Dot::sanitize_boolean('promoted', $input),
            'user_input' => Dot::sanitize_boolean('user_input', $input),
            'validated' => Dot::sanitize_boolean('validated', $input),
        ];

        return array_merge($input, $sanitize);
    }

    public static function display_prepare($input) {

        $input->promotions_startdate = date('m/d/Y', strtotime($input->promotions_startdate));
        $input->promotions_enddate = date('m/d/Y', strtotime($input->promotions_enddate));
        return $input;
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

//        $error = [];
//        foreach ($this->csv as $key => $value) {
//            if (in_array($value, $records)) {
//                $error[] = 1;
//            } else {
//                $error[] = 0;
//            }
//        }

        return $this->csv === $records;
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
            7 => 'funding_per_unit',
            8 => 'forecasted_qty',
            9 => 'forecasted_unit_sales',
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
    function insert_items_under_promotion($promotion) {

        if ($this->have_child_items($promotion)) {
            return true;
        }

        if ($promotion->level_of_promotions == 'Category') {

            $categories = explode(',', $promotion->category);
            foreach ($categories as $key => $category) {
                Dot::iecho("Going to find items for the category {$category}");
                $records = Pgquery::get_items_category($category, $promotion->retailer);
                $count = count($records);
                Dot::iecho("Total records for {$category} is {$count }");

                foreach ($records as $key => $record) {
                    $input = $this->prepare_redshift_item($promotion, $record);
                    $status = self::status($input);
                    if ($status['status']) {
                        self::create($input);
                    }
                }
            }
        } elseif ($promotion->level_of_promotions == 'Brand') {
            $records = Pgquery::get_items_brand($promotion->brand, $promotion->retailer);
            foreach ($records as $key => $record) {
                $input = $this->prepare_redshift_item($promotion, $record);
                $status = self::status($input);
                if ($status['status']) {
                    self::create($input);
                }
            }
        }

        $this->set_have_child_items($promotion);
        Promotion::update_promotion_status($promotion->id, 'active');
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

            if ($promotion->level_of_promotions == 'Category') {
                if (isset($option['category'])) {
                    $categories = explode(',', $promotion->category);
                    $option['category'] = explode(',', $option['category']);
                    if (Dot::is_array_eaqual($categories, $option['category'])) {
                        Dot::iecho("child item exist for the category, pid: {$promotion->id}");
                        return true;
                    } else {
                        Dot::iecho("Its look like new/changed category, pid: {$promotion->id}");
                        Promotion::update_promotion_status($promotion->id, 'sleep');
                        Item::where('promotions_id', $promotion->id)->delete();
                        return false;
                    }
                }
            }
            if ($promotion->level_of_promotions == 'Brand') {
                if (isset($option['brand']) && $option['brand'] == $promotion->brand) {
                    Dot::iecho("child item exist for the brand, pid: {$promotion->id}");
                    return true;
                } else {
                    Dot::iecho("Its look like new/changed brand, pid: {$promotion->id}");
                    Promotion::update_promotion_status($promotion->id, 'sleep');
                    Item::where('promotions_id', $promotion->id)->delete();
                    return false;
                }
            }
        }
        // child item doesn't exist
        return false;
    }

    /**
     * 
     * Set or change option have_child_items_{n} value
     */
    function set_have_child_items($promotion) {
        $mata_key = 'have_child_items_' . $promotion->id;
        if ($promotion->level_of_promotions == 'Category') {
            $value = [
                'category' => $promotion->category
            ];
        } elseif ($promotion->level_of_promotions == 'Brand') {
            $value = [
                'brand' => $promotion->brand
            ];
        }

        Option::add($mata_key, $value);
    }

    /**
     * 
     * Create an array for the item when a category/brand level mass insertion
     * Prepare records from dim_material for promotions_child_input table
     * @param array $record
     */
    function prepare_redshift_item($promotion, $record) {


        return [
            'promotions_id' => $promotion->id,
            'promotions_startdate' => $promotion->promotions_startdate,
            'promotions_enddate' => $promotion->promotions_enddate,
            'material_id' => $record->material_id,
            'product_name' => Dot::get_obj_array_val('material_description', $record),
            'asin' => Dot::get_obj_array_val('retailer_sku', $record),
            'rtl_id' => Dot::get_obj_array_val('retailer_sku', $record),
            'promotions_budget' => null,
            'promotions_projected_sales' => null,
            'promotions_expected_lift' => null,
            'x_plant_material_status' => Dot::get_obj_array_val('x_plant_matl_status', $record),
            'x_plant_status_date' => Dot::get_obj_array_val('x_plant_valid_from', $record),
            'funding_per_unit' => null,
            'forecasted_qty' => null,
            'forecasted_unit_sales' => null,
            'promoted' => 0,
            'user_input' => 0,
            'validated' => 1,
            'percent_discount' => null,
            'price_discount' => null,
            'reference' => null,
        ];
    }

    /**
     * 
     * Prepare items from user input (Form, CSV)
     * Create an array of items of the given material_id OR asin
     * @param array $input
     * @return array
     */
    function generate_item($input) {

        $promotion = Promotion::find($input['promotions_id']);
        if (!$promotion) {
            return [];
        }
        if (!empty($input['material_id'])) {
            $item = Pgquery::get_items_material_id($input['material_id']);
        } elseif (!empty($input['asin'])) {
            $item = Pgquery::get_items_retailer_sku($input['asin']);
        } else {
            return [];
        }

        if (!isset($item)) {
            return [];
        }

        return [
            'promotions_id' => $promotion->id,
            'promotions_startdate' => Dot::get_first_second('promotions_startdate', $input, $promotion),
            'promotions_enddate' => Dot::get_first_second('promotions_enddate', $input, $promotion),
            'material_id' => Dot::get_first_second('material_id', $input, $item),
            'product_name' => Dot::get_first_second('product_name', $input, $item, 'material_description'),
            'asin' => Dot::get_first_second('asin', $input, $item, 'retailer_sku'),
            'rtl_id' => Dot::get_first_second('rtl_id', $input, $item, 'retailer_sku'),
            'promotions_budget' => Dot::sanitize_numeric('promotions_budget', $input),
            'promotions_projected_sales' => Dot::sanitize_numeric('promotions_projected_sales', $input),
            'promotions_expected_lift' => Dot::sanitize_numeric('promotions_expected_lift', $input),
            'x_plant_material_status' => Dot::get_first_second('x_plant_material_status', $input, $item, 'x_plant_matl_status'),
            'x_plant_status_date' => Dot::get_first_second('x_plant_status_date', $input, $item, 'x_plant_valid_from'),
            'funding_per_unit' => Dot::sanitize_numeric('funding_per_unit', $input),
            'forecasted_qty' => Dot::sanitize_numeric('forecasted_qty', $input),
            'forecasted_unit_sales' => Dot::sanitize_numeric('forecasted_unit_sales', $input),
            'promoted' => Dot::sanitize_boolean('promoted', $input),
            'user_input' => Dot::sanitize_boolean('user_input', $input),
            'validated' => Dot::sanitize_boolean('validated', $input),
            'percent_discount' => Dot::sanitize_numeric('percent_discount', $input),
            'price_discount' => Dot::sanitize_numeric('price_discount', $input),
            'reference' => Dot::sanitize_string('reference', $input),
        ];
    }

    /**
     * 
     * Get the list of items based on CSV import
     * @param type $promotion_start_id
     * @param type $promotion_end_id
     */
    public static function get_items_range($promotion_start_id, $promotion_end_id) {
        return self::whereBetween('promotions_id', [$promotion_start_id, $promotion_end_id])
                        ->get();
    }

}
