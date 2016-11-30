<?php

namespace App\Redshift;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Pgquery {

    public static function get_distinct_column_values($column) {

        switch ($column) {
            case 'retailer':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_retailer_channel')
                                ->distinct()
                                ->pluck('retailer');
                break;
            case 'brand':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->distinct()
                                ->pluck('brand');
                break;

            case 'category':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->distinct()
                                ->pluck('business_team');
                break;

            case 'division':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->distinct()
                                ->pluck('division');
                break;
            case 'material_id':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->distinct()
                                ->pluck('material_id');
                break;
            case 'product_name':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->distinct()
                                ->pluck('material_description');
                break;
            case 'asin':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->distinct()
                                ->pluck('retailer_sku');
                break;
            case 'rtl_id':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->distinct()
                                ->pluck('retailer_sku');
                break;

            default:
                return [];
                break;
        }
    }

    /**
     * 
     * Get items of category
     */
    public static function get_items_category($category) {
        return DB::connection('redshift')
                        ->table('nwl_pos.dim_material')
                        ->where('business_team', $category)
                        ->distinct('material_id')
                        ->get();
    }
    /**
     * 
     * Get items by material id
     */
    public static function get_items_material_id($material_id) {
        return DB::connection('redshift')
                        ->table('nwl_pos.dim_material')
                        ->where('material_id', $material_id)
                        ->first();
    }

}
