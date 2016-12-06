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
                                ->where('material_id', 'ilike', $term . '%')
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
                                ->where('retailer_sku', 'ilike', $term . '%')
                                ->distinct()
                                ->limit(5)
                                ->pluck('retailer_sku');
                break;
            case 'rtl_id':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->where('retailer_sku', 'ilike', $term . '%')
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
     * Get items of brand
     */
    public static function get_items_brand($brand) {
        return DB::connection('redshift')
                        ->table('nwl_pos.dim_material')
                        ->where('brand', $brand)
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

    public static function get_invoice($material_id, $date) {
        $sql = "SELECT
sum(mis.dollars_usd) invoice_sales,
sum(mis.units) invoice_units,
dm.material,
date(mis.invoice_date),
avg(sma.numerator) invoice_numerator
FROM nwl_sap_sales.metric_invoice_sales mis
INNER JOIN nwl_sap_sales.dim_material dm
ON (dm.material = mis.material_number)
INNER JOIN nwl_pcm.sap_material_additional sma
ON (sma.material = mis.material_number) OR (sma.ean_upc = dm.upc_number)
WHERE mis.material_number = '1954840'
AND mis.invoice_date = '2016-07-12'
GROUP BY dm.material, mis.invoice_date";
        
        $return = DB::connection('redshift')->select($sql);
        return $return[0];
    }

}
