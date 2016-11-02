<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Merge;

class Spinput extends Model {

    protected $table = 'promo_input';
    protected $guarded = array('id');
    protected $fillable = [
        'material_id',
        'retailer_id',
        'promotions_name',
        'promotion_type',
        'start_date',
        'end_date',
        'promo_description',
        'item_name',
        'investment_d',
        'forecasted_units',
        'forecasted_d',
        'customer_name',
        'level_of_promotion',
        'discount_price_d',
        'discount_p',
        'comments',
        'status'
    ];

    private $merge;
    private $calendar;

    function set_input($input) {

        $this->merge = new Merge;
        $this->calendar = new Calendar;
        $this->input = $this->sanitize($input);


        $this->validate = $this->validate();

        if (!$this->validate) {
            return false;
        }

        //self::create($this->input);

        $this->start_date = $this->input['start_date'];
        $this->end_date = $this->input['end_date'];
        
        $this->set_psql_where_id();
        $this->set_psql_where_date();
        $this->psql_daily = Stock::psql_dayily_pos($this->where_id, $this->where_date);
    }

    function set_psql_where_id() {
        if (isset($this->input['material_id']) && $this->input['material_id'] != '') {
            $this->material_id = $this->input['material_id'];
            $this->where_id = "m.material_id = '{$this->material_id}'";
        } elseif (isset($this->input['retailer_id']) && $this->input['retailer_id'] != '') {
            $this->retailer_id = $this->input['retailer_id'];
            $this->where_id = " m.retailer_id = '{$this->retailer_id}' ";
        }
    }

    function set_psql_where_date() {
        $this->quarter = $this->calendar->get_quarter($this->start_date);
        $this->where_date =  " BETWEEN '{$this->quarter['start']}' AND '{$this->quarter['end']}' ";
    }

    function validate() {

        if (!Dot::validate_date($this->input['start_date']) || !Dot::validate_date($this->input['end_date'])) {
            return false;
        }

        if ($this->input['start_date'] > $this->input['end_date']) {
            return false;
        }
        return true;
    }

    function sanitize($input) {
        return [
            'promotions_name' => $input['promotions_name'],
            'promotion_type' => $input['promotion_type'],
            'start_date' => date('Y-m-d', strtotime($input['start_date'])),
            'end_date' => date('Y-m-d', strtotime($input['end_date'])),
            'retailer_id' => $input['retailer_id'],
            'material_id' => $input['material_id'],
            'promo_description' => $input['promo_description'],
            'item_name' => $input['item_name'],
            'investment_d' => $this->merge->refine_dollar($input['investment_d']),
            'forecasted_units' => $input['forecasted_units'],
            'forecasted_d' => $this->merge->refine_dollar($input['forecasted_d']),
            'customer_name' => $input['customer_name'],
            'level_of_promotion' => $input['level_of_promotion'],
            'discount_price_d' => $this->merge->refine_dollar($input['discount_price_d']),
            'discount_p' => $this->merge->refine_dollar($input['discount_p']),
            'comments' => $input['comments'],
            'status' => $input['status'],
        ];
    }

}
