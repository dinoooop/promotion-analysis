<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Smaterial extends Model {

    protected $table = 'promo_items';
    public $timestamps = false;
    protected $guarded = array('id');
    protected $fillable = [
        'promo_id',
        'material_description',
        'x_plant_material_status',
        'segment',
        'brand',
        'prod_platform',
        'prod_category',
        'prod_fam',
        'prod_line',
    ];
    
    function set_vars($input) {
        
        echo "Setting table promo_items \n";
        
        $this->sdcalc = $input;
        $this->create_record($this->sdcalc->record_one);
        
    }
    
    function create_record($record) {
        
        $row = [
            'promo_id' => $this->sdcalc->spinput->promo_id,
            'material_description' => $record['material_description'],
            'x_plant_material_status' => $record['x_plant_matl_status'],
            'segment' => $record['sub_segment'],
            'brand' => $record['brand'],
            'prod_platform' => $record['product_platform'],
            'prod_category' => $record['business_team'],
            'prod_fam' => $record['product_family'],
            'prod_line' => $record['product_line'],
        ];
        
        self::create($row);
    }

}
