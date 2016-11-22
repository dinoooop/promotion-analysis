<?php

namespace App\promotions;

use Illuminate\Database\Eloquent\Model;
use App\Dot;
use App\Merge;
use App\Calendar;

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
        'forecaseted_qty',
        'forecasted_unit_sales',
        'promoted',
        'user_input',
        'validated',
        'percent_discount',
        'price_discount',
        'reference',
    ];
    public static $form_create_rules = [
        'material_id' => 'required',
    ];
    public static $form_edit_rules = [
        'material_id' => 'required',
    ];

    public static function sanitize($input) {
        $sanitize = [

            'promotions_budget' => Dot::have_value('promotions_budget', $input),
            'promotions_projected_sales' => Dot::have_value('promotions_projected_sales', $input),
            'promotions_expected_lift' => Dot::have_value('promotions_expected_lift', $input),
            'forecasted_unit_sales' => Dot::have_value('forecasted_unit_sales', $input),
            'funding_per_unit' => Dot::have_value('funding_per_unit', $input),
            'forecaseted_qty' => Dot::have_value('forecaseted_qty', $input),
            'percent_discount' => Dot::have_value('percent_discount', $input),
            'price_discount' => Dot::have_value('price_discount', $input),
            'user_input' => isset($input['user_input']) ? 1 : 0,
            'validated' => isset($input['validated']) ? 1 : 0,
        ];

        return array_merge($input, $sanitize);
    }

    public static function display_prepare($input) {

        $input->promotions_startdate = date('m/d/Y', strtotime($input->promotions_startdate));
        $input->promotions_enddate = date('m/d/Y', strtotime($input->promotions_enddate));
        return $input;
    }

}
