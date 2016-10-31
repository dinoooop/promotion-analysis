<?php

namespace App;

class Block {

    function __construct() {
        
    }

    function materials() {

        $this->mat = [
            'asin' => ['label' => 'ASIN'],
            'avg_weekly_ordered_units_(quarterly)' => ['label' => 'Avg. Weekly Ordered Units (Quarterly)'],
            'baseline' => ['label' => 'Baseline'],
            'brand' => ['label' => 'Brand'],
            'cogs_during' => ['label' => 'COGs During'],
            'cogs_during_baseline_period' => ['label' => 'COGs during Baseline period'],
            'comments' => ['label' => 'Comments'],
            'customer_name' => ['label' => 'Customer Name'],
            'date' => ['label' => 'Date'],
            'discount_p' => ['label' => 'Discount %'],
            'discount_price_d' => ['label' => 'Discount Price $'],
            'discount' => ['label' => 'Discount'],
            'amount' => ['label' => 'Amount'],
            'end_date' => ['label' => 'End Date'],
            'fcst_investment' => ['label' => 'Fcst Investment'],
            'forecast_qty' => ['label' => 'Forecast Qty'],
            'forecasted_d' => ['label' => 'Forecasted $'],
            'forecasted_units' => ['label' => 'Forecasted Units'],
            'funding_source' => ['label' => 'Funding Source'],
            'incremental_p' => ['label' => 'Incremental %'],
            'incremental_d' => ['label' => 'Incremental $'],
            'investment_unit' => ['label' => 'Investment / Unit'],
            'investment_d' => ['label' => 'Investment $'],
            'investment_d' => ['label' => 'Investment $'],
            'item_id' => ['label' => 'Item id'],
            'item_name' => ['label' => 'Item Name'],
            'level_of_promotion' => ['label' => 'Level of Promotion'],
            'material_description' => ['label' => 'Material Description'],
            'material_id' => ['label' => 'Material ID'],
            'normalized_ordered_amount' => ['label' => 'Normalized Ordered Amount'],
            'normalized_ordered_cogs' => ['label' => 'Normalized Ordered COGs'],
            'normalized_ordered_units' => ['label' => 'Normalized Ordered Units'],
            'ordered_amount' => ['label' => 'Ordered Amount'],
            'ordered_amount_during' => ['label' => 'Ordered Amount During'],
            'ordered_cogs' => ['label' => 'Ordered COGs'],
            'ordered_qty_during' => ['label' => 'Ordered Qty During'],
            'ordered_units' => ['label' => 'Ordered Units'],
            'pos_during' => ['label' => 'POS During'],
            'pos_during_baseline_period' => ['label' => 'POS during Baseline period'],
            'pos_qty' => ['label' => 'POS Qty'],
            'pos_sales' => ['label' => 'POS Sales'],
            'pos_shipped_cog_sold' => ['label' => 'POS Shipped COG Sold'],
            'ppm_p_baseline' => ['label' => 'PPM % Baseline'],
            'ppm_p_during' => ['label' => 'PPM % During'],
            'prod_category' => ['label' => 'Prod Category'],
            'prod_fam' => ['label' => 'Prod Fam'],
            'prod_line' => ['label' => 'Prod Line'],
            'prod_platform' => ['label' => 'Prod Platform'],
            'promo_description' => ['label' => 'Promo Description'],
            'promo_id' => ['label' => 'Promo Id'],
            'promotion_type' => ['label' => 'Promotion Type'],
            'promotions_name' => ['label' => 'Promotions Name'],
            'pull_forward_halo_p' => ['label' => 'Pull Forward / Halo %'],
            'pull_forward_halo_d' => ['label' => 'Pull Forward / Halo $'],
            'retailer' => ['label' => 'Retailer'],
            'retailer_id' => ['label' => 'Retailer ID'],
            'roi' => ['label' => 'ROI'],
            'segment' => ['label' => 'Segment'],
            'start_date' => ['label' => 'Start Date'],
            'status' => ['label' => 'Status'],
            'wkly_avg_oa_quarterly' => ['label' => 'Wkly Avg. OA Quarterly'],
            'wkly_baseline' => ['label' => 'Wkly Baseline'],
            'wkly_avg_ordered_amount_post_2_wks' => ['label' => 'Wkly. Avg. Ordered Amount Post 2 wks'],
            'wkly_pull_forward_halo_d' => ['label' => 'Wkly. Pull Forward / Halo $'],
            'x_plant_material_status' => ['label' => 'X Plant Material Status'],
            'year' => ['label' => 'Year'],
        ];
    }

    function headers() {


        $this->promo_input = [
            'promo_id',
            'item_id',
            'material_id',
            'asin',
            'promotions_name',
            'promotion_type',
            'start_date',
            'end_date',
            'retailer_id',
            'material_id',
            'promo_description',
            'item_name',
            'investment_d',
            'forecasted_units',
            'forecasted_p',
            'customer_name',
            'level_of_promotion',
            'discount_price_d',
            'discount_p',
            'comments',
            'status'
        ];


        $this->promo_item = [
            'item_id',
            'material_id',
            'asin',
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

        $this->promo_date = [
            'item_id',
            'date',
            'pos_sales',
            'pos_qty',
            'ordered_amount',
            'ordered_units',
            'pos_shipped_cog_sold',
            'ordered_cogs',
        ];

        $this->promo_week = [
            'item_id',
            'pos_sales',
            'pos_qty',
            'ordered_amount',
            'ordered_units',
            'pos_shipped_cog_sold',
            'ordered_cogs',
            'wkly_avg_oa_quarterly',
            'normalized_ordered_amount',
            'avg_weekly_ordered_units_quarterly',
            'normalized_ordered_units',
            'normalized_ordered_cogs',
        ];


        $this->POD = [
            'promo_id',
            'item_id',
            'year',
            'ordered_amount_during',
            'wkly_baseline',
            'baseline',
            'incremental_d',
            'incremental_p',
            'wkly_avg_ordered_amount_post_2_wks',
            'wkly_pull_forward_halo_d',
            'pull_forward_halo_d',
            'pull_forward_halo_p',
            'pos_during',
            'cogs_during',
            'ppm_p_during',
            'pos_during_baseline_period',
            'cogs_during_baseline_period',
            'ppm_p_baseline',
            'ordered_qty_during',
            'investment_unit',
            'funding_source',
            'investment',
            'roi',
            'forecast_qty',
            'fcst_investment',
            'discount',
            'amount',
        ];
    }

    public static function get_headers() {
        return [
            'material_id',
            'material_description',
            'x_plant_matl_status',
            'sub_segment',
            'brand',
            'product_platform',
            'business_team',
            'product_family',
            'product_line',
            'date_day',
            'pos_sales',
            'pos_units',
            'ordered_amount',
            'ordered_units',
            'pos_shipped_cogs'
        ];
    }

    public static function prepare_psql($material_id, $psql_date) {
        $sql = "SELECT
m.material_id,
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
AND ms.retailer_country_id = m.retailer_country_id
INNER JOIN nwl_pos.metric_online_channel AS moc 
ON ms.item_id = moc.item_id 
AND ms.retailer_country_id = moc.retailer_country_id
AND ms.date_day = moc.date_day
WHERE
m.material_id = '{$material_id}'
AND ms.date_day BETWEEN '{$psql_date['start_date']}' AND '{$psql_date['end_date']}'";

        return $sql;
    }
    
    
    public static function sample_psql() {
        $sql = 'SELECT * FROM nwl_pcm.sap_material_additional LIMIT 2';
        //$sql = 'SELECT * FROM users LIMIT 2';
        return $sql;
        
    }

}
