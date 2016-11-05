<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Dot;
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
    public $data;

    function set_vars($input) {

        $this->merge = new Merge;

        // Record to be inserted to promo_input
        $this->data = $this->sanitize($input);


        $this->validate = $this->validate();

        if (!$this->validate) {
            return false;
        }

        $this->material_id = isset($this->data['material_id']) ? $this->data['material_id'] : '';
        $this->retailer_id = isset($this->data['retailer_id']) ? $this->data['retailer_id'] : '';
        
        $this->retailer_sku = isset($this->data['retailer_sku']) ? $this->data['retailer_sku'] : '';

        $this->is_single_day_promo = ($this->data['start_date'] == $this->data['end_date']);

        $spinput = self::create($this->data);
        $this->promo_id = $spinput->id;
        echo "Inputs Created \n";
    }

    function validate() {


        if ((!isset($this->data['material_id']) || $this->data['material_id'] == '')) {
            
            if (!isset($this->data['retailer_id']) || $this->data['retailer_id'] == '') {
                return false;
            }
        }


        if (!Dot::validate_date($this->data['start_date']) || !Dot::validate_date($this->data['end_date'])) {
            return false;
        }

        if ($this->data['start_date'] > $this->data['end_date']) {
            return false;
        }
        return true;
    }

    function sanitize($input) {
        return [
            'material_id' => $input['material_id'],
            'retailer_id' => $input['retailer_id'],
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
