<?php

namespace App\Redshift;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Pgquery {

    public static function get_distinct_column_values($column, $term = null) {

        if (is_null($term)) {
            return false;
        }

        switch ($column) {
            case 'retailer':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_retailer_channel')
                                ->where('retailer', 'ilike', '%' . $term . '%')
                                ->distinct()
                                ->limit(5)
                                ->pluck('retailer');
                break;
            case 'brand':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->where('brand', 'ilike', '%' . $term . '%')
                                ->distinct()
                                ->limit(5)
                                ->pluck('brand');
                break;

            case 'category':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->where('business_team', 'ilike', '%' . $term . '%')
                                ->distinct()
                                ->limit(5)
                                ->pluck('business_team');
                break;

            case 'division':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->where('division', 'ilike', '%' . $term . '%')
                                ->distinct()
                                ->limit(5)
                                ->pluck('division');
                break;
            case 'material_id':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->where('material_id', 'ilike', '%' . $term . '%')
                                ->distinct()
                                ->limit(5)
                                ->pluck('material_id');
                break;
            case 'product_name':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->where('material_description', 'ilike', '%' . $term . '%')
                                ->distinct()
                                ->limit(5)
                                ->pluck('material_description');
                break;
            case 'asin':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->where('retailer_sku', 'ilike', '%' . $term . '%')
                                ->distinct()
                                ->limit(5)
                                ->pluck('retailer_sku');
                break;
            case 'rtl_id':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->where('retailer_sku', 'ilike', '%' . $term . '%')
                                ->distinct()
                                ->limit(5)
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
