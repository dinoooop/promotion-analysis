<?php

namespace App;

class Smaterial extends Eloquent {

    protected $table = 'promo_items';
    protected $guarded = array('id');
    protected $fillable = [
        'item_id',
        'material_id',
        'retailer_id',
        'material_description',
        'x_plant_material_status',
        'segment',
        'brand',
        'prod_platform',
        'prod_category',
        'prod_fam',
        'prod_line',
        'retailer',
    ];

    

}
