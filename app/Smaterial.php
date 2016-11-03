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


    
    function create_record($record) {

        $row = [
            'item_id' => $record['item_id'],
            'material_id' => $record['material_id'],
            'retailer_id' => $record['retailer_id'],
            'material_description' => $record['material_description'],
            'x_plant_material_status' => $record['x_plant_material_status'],
            'segment' => $record['segment'],
            'brand' => $record['brand'],
            'prod_platform' => $record['prod_platform'],
            'prod_category' => $record['prod_category'],
            'prod_fam' => $record['prod_fam'],
            'prod_line' => $record['prod_line'],
            'retailer' => $record['retailer'],
        ];
        
        self::create($row);
    }

}
