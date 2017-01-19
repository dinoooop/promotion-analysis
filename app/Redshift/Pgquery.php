<?php

namespace App\Redshift;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Dot;

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
    public static function get_items_category($category, $retailer = null) {
        $query = DB::connection('redshift')->table('nwl_pos.dim_material as m');

        if (!is_null($retailer)) {
            $query->join('nwl_pos.dim_retailer_channel as rc', 'rc.retailer_country_id', 'm.retailer_country_id');
            $query->where('rc.retailer', $retailer);
        }

        $query->where('m.business_team', $category);
        $query->distinct('m.material_id');
        return $query->get();
    }

    /**
     * 
     * Get items of brand
     */
    public static function get_items_brand($brand, $retailer = null) {
        $query = DB::connection('redshift')->table('nwl_pos.dim_material as m');

        if (!is_null($retailer)) {
            $query->join('nwl_pos.dim_retailer_channel as rc', 'm.retailer_country_id', '=', 'rc.retailer_country_id');
            $query->where('rc.retailer', $retailer);
        }
        
        $query->where('m.brand', $brand);
        $query->distinct('m.material_id');
        return $query->get();
    }

    /**
     * 
     * Get items by material id
     */
    public static function get_items_material_id($material_id) {
        return (array) DB::connection('redshift')
                        ->table('nwl_pos.dim_material')
                        ->where('material_id', $material_id)
                        ->first();
    }

    /**
     * 
     * Get items by retailer_sku (rtl_id, asin)
     */
    public static function get_items_retailer_sku($retailer_sku) {
        return (array) DB::connection('redshift')
                        ->table('nwl_pos.dim_material')
                        ->where('retailer_sku', $retailer_sku)
                        ->first();
    }

    public static function laravel_preparation_data_amazon($data) {
        DB::connection('redshift')->enableQueryLog();
        extract($data);

        // m   : dim_material
        // ms  : metric_sales
        // rc  : dim_retailer_channel
        // moc : metric_online_channel
        $select = [
            'm.material_id',
            'm.retailer_sku as retailer_id',
            'm.material_description as material_description',
            'm.x_plant_matl_status',
            'm.sub_segment',
            'm.brand',
            'm.product_platform',
            'm.business_team',
            'm.product_family',
            'm.product_line',
            'ms.date_day',
        ];

        if (Dot::is_amazon($promotion)) {
            $select[] = 'moc.ordered_amount';
            $select[] = 'moc.ordered_units';
        } else {
            $select[] = 'ms.pos_sales as ordered_amount';
            $select[] = 'ms.pos_units as ordered_units';
        }

        // $select_str = implode(', ', $select);
        // QUERY BUILDER 

        $query = DB::connection('redshift')->table('nwl_pos.metric_sales as ms');

        $query->join('nwl_pos.dim_material as m', 'ms.item_id', 'm.item_id');

        if (Dot::is_amazon($promotion)) {
            $query->join('nwl_pos.metric_online_channel as moc', function ($join) {
                $join->on('ms.item_id', 'moc.item_id');
                $join->on('ms.retailer_country_id', 'moc.retailer_country_id');
                $join->on('ms.date_day', 'moc.date_day');
            });
        }

        if ($promotion->category != '') {

            $query->where('m.business_team', $promotion->category);
        }

        if ($promotion->brand != '') {

            $query->where('brand', $promotion->brand);
        }

        if ($promotion->retailer != '') {

            $query->join('nwl_pos.dim_retailer_channel as rc', 'ms.retailer_country_id', 'rc.retailer_country_id');
            $query->where('rc.retailer', $promotion->retailer);
        }

        // Store/Ecommerce
        $where_store = "(rc.retail_ecommerce ilike '%store%' or rc.retail_ecommerce ilike '%retail%')";
        $where_ecommerce = "(rc.retail_ecommerce ilike '%e-commerce%' or rc.retail_ecommerce ilike '%ecommerce%')";
        
        if ($promotion->retail_ecommerce == 'store') {
            Dot::iecho("For Stire/Retailers");
            $query->whereRaw($where_store);
        } elseif ($promotion->retail_ecommerce == 'all') {
            Dot::iecho("retail_ecommerce  for all");
            $query->whereRaw("(" . $where_store . " or " . $where_ecommerce . ") AND rc.retail_ecommerce <> 'Unknown'");
        } else {
            Dot::iecho("For E-commerce");
            $query->whereRaw($where_ecommerce);
        }

        // WHERE ID
        if ($item->material_id == '' || $item->material_id == 'BPEUNKNOWN') {

            if ($item->asin == '' || $item->asin == 0) {
                return false;
            } else {
                $query->where('m.retailer_sku', $item->asin);
            }
        } else {

            $query->where('m.material_id', $item->material_id);
        }

        // WHERE DATE
        $query->whereBetween('ms.date_day', [$start_date, $end_date]);

        $query->select($select);
        $query->distinct('ms.date_day');


        return $query->get();
    }

    public static function sample_retail() {
        $query = DB::connection('redshift')->table('nwl_pos.dim_retailer_channel as rc');

        $where_ecommerce = "(rc.retail_ecommerce ilike '%e-commerce%' or rc.retail_ecommerce ilike '%ecommerce%') AND rc.retail_ecommerce <> 'Unknown'";
        $query->whereRaw($where_ecommerce);
        $query->take(30);
        $REC = $query->get();
        echo '<pre>', print_r($REC), '</pre>';
    }

    public static function psql_preparation_data_amazon($where_id, $where_date) {

        $sql = "SELECT
m.material_id,
m.retailer_sku AS retailer_id,
m.material_description,
m.x_plant_matl_status,
m.sub_segment,
m.brand,
m.product_platform,
m.business_team,
m.product_family,
m.product_line,
ms.date_day,
ms.pos_sales,
ms.pos_units,
moc.ordered_amount,
moc.ordered_units,
ms.pos_shipped_cogs
FROM nwl_pos.metric_sales AS ms
INNER JOIN nwl_pos.dim_material AS m 
ON ms.item_id = m.item_id 
INNER JOIN nwl_pos.metric_online_channel AS moc 
ON ms.item_id = moc.item_id 
AND ms.retailer_country_id = moc.retailer_country_id
AND ms.date_day = moc.date_day
WHERE {$where_id} AND ms.date_day {$where_date}";

        return $sql;
    }

    public static function psql_preparation_data_nonamazon($where_id, $where_date) {

        $sql = "SELECT
m.material_id,
m.retailer_sku AS retailer_id,
m.material_description,
m.x_plant_matl_status,
m.sub_segment,
m.brand,
m.product_platform,
m.business_team,
m.product_family,
m.product_line,
ms.date_day,
ms.pos_sales AS ordered_amount,
ms.pos_units AS ordered_units
FROM nwl_pos.metric_sales AS ms
INNER JOIN nwl_pos.dim_material AS m 
ON ms.item_id = m.item_id
WHERE {$where_id} AND ms.date_day {$where_date}";

        return $sql;
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
WHERE mis.material_number = '{$material_id}'
AND mis.invoice_date = '{$date}'
GROUP BY dm.material, mis.invoice_date";

        $return = DB::connection('redshift')->select($sql);
        return (isset($return[0])) ? $return[0] : [];
    }

    /**
     * 
     * Promotion data fetching query for preperation table that have invoice sales value
     */
    public static function new_promotion_query($where_id, $where_date) {
        $sql = "SELECT
m.item_id,
m.material_id,
m.retailer_sku AS retailer_id,
m.material_description,
m.x_plant_matl_status,
m.sub_segment,
m.brand,
m.product_platform,
m.business_team,
m.product_family,
m.product_line,
ms.date_day,
ms.pos_sales,
ms.pos_units,
moc.ordered_amount,
moc.ordered_units,
ms.pos_shipped_cogs,
invoice.invoice_sales,
invoice.invoice_units,
invoice.invoice_numerator
FROM nwl_pos.metric_sales AS ms
INNER JOIN nwl_pos.dim_material AS m 
ON ms.item_id = m.item_id
AND ms.retailer_country_id = m.retailer_country_id
INNER JOIN nwl_pos.metric_online_channel AS moc 
ON ms.item_id = moc.item_id 
AND ms.retailer_country_id = moc.retailer_country_id
AND ms.date_day = moc.date_day
INNER JOIN (
	SELECT
	sum(mis.dollars_usd) invoice_sales,
	sum(mis.units) invoice_units,
	dm.material AS material,
	date(mis.invoice_date) AS invoice_date,
	avg(sma.numerator) invoice_numerator
	FROM nwl_sap_sales.metric_invoice_sales mis
	INNER JOIN nwl_sap_sales.dim_material dm
	ON (dm.material = mis.material_number)
	INNER JOIN nwl_pcm.sap_material_additional sma
	ON (sma.material = mis.material_number) OR (sma.ean_upc = dm.upc_number)
	GROUP BY dm.material, mis.invoice_date
) AS invoice ON m.material_id = invoice.material AND ms.date_day = invoice.invoice_date
WHERE {$where_id}
AND ms.date_day {$where_date}";
        return $sql;
    }

}
