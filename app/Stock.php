<?php

namespace App;

use App\Redshift\Pgquery;

class Stock {

    public static function get_value($stock_key, $return_key) {
        $stock = self::get($stock_key);
        if (!is_array($stock)) {
            return false;
        }
        return isset($stock[$return_key]) ? $stock[$return_key] : false;
    }

    public static function get($key) {

        switch ($key) {
            case 'roles':

                $system_roles = self::get('system_roles');
                $result = array();
                foreach ($system_roles as $key => $value) {
                    // remove public from the list
                    if ($key == 'public') {
                        continue;
                    }
                    $result[$key] = $value['name'];
                }
                return $result;
                break;

            case 'system_roles':
                return array(
                    'admin' => array(
                        'name' => 'Administrator',
                        'capabilities' => array(
                            'sup_admin_cap', // general
                            'sub_admin_cap', // general
                            'view_user_lists',
                            'user_add',
                            'user_edit',
                            'user_edit_role',
                            'user_delete',
                            'case_add',
                            'case_edit',
                            'case_delete',
                            'view_category_list',
                            'category_add',
                            'category_edit',
                            'category_delete',
                        ),
                    ),
                    'employee' => array(
                        'name' => 'Employee',
                        'capabilities' => array(
                            'sub_admin_cap', // general
                        ),
                    ),
                    'public' => array(
                        'name' => 'Public',
                        'capabilities' => array(),
                    ),
                );
                break;

            case 'retailer':

                return Pgquery::get_distinct_column_values('retailer');
                break;


            case 'retailer_country':
                $country = ['US', 'Canada', 'UK', 'Spain', 'Germany', 'India', 'France'];
                return array_combine($country, $country);
                break;

            case 'newell_status':
                $return = [
                    'Approved',
                    'Rejected',
                    'Supressed',
                    'Cancelled',
                ];
                return array_combine($return, $return);
                break;

            case 'promotions_status':
                $return = [
                    'Not Started',
                    'In',
                    'Progress',
                    'Completed',
                ];
                return array_combine($return, $return);
                break;

            case 'promotions_type':
                $return = [
                    'DOTD',
                    'CSLD',
                    'Best Deals',
                    'VPC',
                    'Price Discount',
                    'Non Price Discount',
                    'Gift Card',
                    'Buy X and Get Y',
                    'Percent Discount OFF',
                    'Other'
                ];
                return array_combine($return, $return);
                break;
            case 'promotions_budget_type':
                $return = [
                    'Checkbook',
                    'ITPF',
                    'Other',
                ];
                return array_combine($return, $return);
                break;

            case 'level_of_promotions':
                $return = [
                    'Item Level',
                    'Brand',
                    'Category',
                ];
                return array_combine($return, $return);
                break;

            case 'marketing_type':
                $return = [
                    'Price Promotion',
                    'Marketing Promotion',
                ];
                return array_combine($return, $return);
                break;

            case 'brand':
                return Pgquery::get_distinct_column_values('brand');
                break;

            case 'category':
                return Pgquery::get_distinct_column_values('category');
                break;

            case 'product_family':
                return [];
                break;

            case 'product_line':
                return [];
                break;

            case 'division':
                return Pgquery::get_distinct_column_values('division');
                break;

            case 'status':
                $return = [
                    'sleep' => 'Not Processed', // Non active promotion. May be waiting for items of a category or brand
                    'active' => 'Not Processed', // On submit the promotion
                    'processing' => 'In-Progress', // Promotion go through the calculation (do not allow edit on this mode)
                    'completed' => 'Completed', // calculation completed 
                    'failed' => 'Needs Attention',  //calculation failed
                ];
                return $return;
                break;

            case 'material_id':
                return Pgquery::get_distinct_column_values('material_id');
                break;
            case 'product_name':
                return Pgquery::get_distinct_column_values('product_name');
                break;
            case 'asin':
                return Pgquery::get_distinct_column_values('asin');
                break;
            case 'rtl_id':
                return Pgquery::get_distinct_column_values('rtl_id');
                break;
        }

        return false;
    }

    public static function psql_weekly_pos() {

        $sql = "SELECT
m.item_id,
m.material_id,
ms.date_day,
SUM(ms.pos_sales),
SUM(ms.pos_units),
SUM(moc.ordered_amount),
SUM(moc.ordered_units),
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
AND ms.date_day BETWEEN '{$start_date}' AND '{$end_date}'";

        return $sql;
    }

    public static function psql_dayily_pos_bkp01($material_id, $start_date, $end_date) {

        $sql = "SELECT
m.item_id,
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
AND ms.date_day BETWEEN '{$start_date}' AND '{$end_date}'";

        return $sql;
    }

    public static function functionName() {
        $sql = "Select * FROM nwl_pos.dim_material WHERE material_id = '{$where_id}'";
    }

    public static function sample_input() {

        return [
            'promotions_name' => 'Prime Day, 7/12/16',
            'promotion_type' => 'Best Deal',
            'promotions_startdate' => '07/12/2016', // July 12
            'promotions_enddate' => '07/12/2016', // July 12
//            'promotions_startdate' => '2016-06-24',
//            'promotions_enddate' => '2016-07-02',
            'retailer_id' => 'B01ABQBYSO',
            'material_id' => '1954840',
            'promo_description' => '',
            'item_name' => '',
            'investment_d' => '$42.84',
            'forecasted_units' => '1800',
            'forecasted_d' => '',
            'customer_name' => 'Amazon',
            'level_of_promotion' => 'SKU Level',
            'discount_price_d' => '',
            'discount_p' => '',
            'comments' => '',
            'status' => 'Approved'
        ];
    }

}
