<?php

namespace App;

class Stock {

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

            case 'ad_location':
                $location = self::get('ad_location_size');
                $result = array();
                foreach ($location as $key => $value) {
                    $result[$key] = $key . ' (' . $value['width'] . ' x ' . $value['height'] . ')';
                }
                return $result;
                break;

            case 'ad_location_size':
                $placeholder = self::get('ad_placeholders');
                return array(
                    'A1' => array('width' => 600, 'height' => 100, 'placeholder' => $placeholder['600_100']),
                    'A2' => array('width' => 600, 'height' => 100, 'placeholder' => $placeholder['600_100']),
                    'B1' => array('width' => 450, 'height' => 240, 'placeholder' => $placeholder['450_240']),
                    'B2' => array('width' => 450, 'height' => 240, 'placeholder' => $placeholder['450_240']),
                    'C' => array('width' => 600, 'height' => 150, 'placeholder' => $placeholder['600_150']),
                );
                break;

            case 'ad_placeholders':
                return array(
                    '600_100' => url('shape/images/ad-1.gif'),
                    '450_240' => url('shape/images/ad-2.jpg'),
                    '600_150' => url('shape/images/ad-3.gif'),
                );
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

    public static function psql_dayily_pos($where_id, $where_date) {

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
            'start_date' => '07/12/2016',// July 12
            'end_date' => '07/12/2016',// July 12
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
