<?php

namespace App;

class AppForms {

    public static function get_form($form_name, $param = null) {

        switch ($form_name) {



            case 'pv_form_post':
                return array(
                    'form_name' => 'pv_edit_post',
                    'modalObject' => 'form_data',
                    'fields' => array(
                        array(
                            'type' => 'text',
                            'name' => 'title',
                            'label' => 'Post title',
                        ),
                        array(
                            'type' => 'textarea',
                            'name' => 'description',
                            'label' => 'Desription',
                        ),
                    ),
                );
                break;


            case 'pv_form_user':
                return array(
                    'form_name' => 'pv_form_user',
                    'fields' => array(
                        array(
                            'type' => 'text',
                            'name' => 'name',
                            'label' => 'Full Name',
                        ),
                        array(
                            'type' => 'text',
                            'name' => 'username',
                            'label' => 'Username',
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'role',
                            'label' => 'Roles',
                            'options' => Stock::get('roles'),
                        ),
                        array(
                            'type' => 'email',
                            'name' => 'email',
                            'label' => 'Email',
                        ),
                        array(
                            'type' => 'password',
                            'name' => 'password',
                            'label' => 'Password',
                        ),
                        array(
                            'type' => 'password',
                            'name' => 'confirm_password',
                            'label' => 'Confirm Password',
                        ),
                    ),
                );
                break;
        }
    }

    public static function form_promotion() {
        return array(
            'form_name' => 'pv_form_promotion',
            'fields' => array(
                array(
                    'type' => 'text',
                    'name' => 'promotions_name',
                    'label' => 'Promotions Name',
                ),
                array(
                    'type' => 'textarea',
                    'name' => 'promotions_description',
                    'label' => 'Promotions Description',
                ),
                array(
                    'type' => 'date',
                    'name' => 'promotions_startdate',
                    'label' => 'Promotions start date',
                    'col' => 6,
                ),
                array(
                    'type' => 'date',
                    'name' => 'promotions_enddate',
                    'label' => 'Promotions end date',
                    'col' => 6,
                ),
                array(
                    'type' => 'auto_complete',
                    'name' => 'retailer',
                    'label' => 'Retailer',
                    'list' => Stock::get('retailer'),
                ),
                array(
                    'type' => 'text',
                    'name' => 'retailer_country_id',
                    'label' => 'Retailer Country Id',
                ),
                array(
                    'type' => 'select',
                    'name' => 'retailer_country',
                    'label' => 'Retailer Country',
                    'options' => Stock::get('retailer_country'),
                    'default' => 'US'
                ),
                array(
                    'type' => 'select',
                    'name' => 'newell_status',
                    'label' => 'Newell Status',
                    'options' => Stock::get('newell_status'),
                    'default' => 'approved'
                ),
                array(
                    'type' => 'select',
                    'name' => 'promotions_status',
                    'label' => 'Promotions Status',
                    'options' => Stock::get('promotions_status'),
                ),
                array(
                    'type' => 'select',
                    'name' => 'promotions_type',
                    'label' => 'Promotions Type',
                    'options' => Stock::get('promotions_type'),
                ),
                array(
                    'type' => 'select',
                    'name' => 'level_of_promotions',
                    'label' => 'Level of promotions',
                    'options' => Stock::get('level_of_promotions'),
                ),
                array(
                    'type' => 'select',
                    'name' => 'marketing_type',
                    'label' => 'Marketing Type',
                    'options' => Stock::get('marketing_type'),
                ),
                array(
                    'type' => 'boolean_checkbox',
                    'name' => 'annivarsaried',
                    'label' => 'Annivarsaried',
                ),
                array(
                    'type' => 'text',
                    'name' => 'promotions_projected_sales',
                    'label' => 'Promotions Projected Sales',
                ),
                array(
                    'type' => 'text',
                    'name' => 'promotions_expected_lift',
                    'label' => 'Promotions expected lift',
                ),
                array(
                    'type' => 'text',
                    'name' => 'promotions_budget_type',
                    'label' => 'Promotions Budget Type',
                ),
                array(
                    'type' => 'text',
                    'name' => 'brand_id',
                    'label' => 'Brand Id',
                ),
                array(
                    'type' => 'auto_complete',
                    'name' => 'brand',
                    'label' => 'Brand',
                    'list' => Stock::get('brand'),
                ),
                array(
                    'type' => 'auto_complete',
                    'name' => 'category',
                    'label' => 'Category',
                    'list' => Stock::get('category'),
                ),
//                        array(
//                            'type' => 'auto_complete',
//                            'name' => 'product_family',
//                            'label' => 'Product Family',
//                            'list' => Stock::get('product_family'),
//                        ),
//                        array(
//                            'type' => 'auto_complete',
//                            'name' => 'product_line',
//                            'label' => 'Product Line',
//                            'list' => Stock::get('product_line'),
//                        ),
                array(
                    'type' => 'auto_complete',
                    'name' => 'division',
                    'label' => 'Division',
                    'list' => Stock::get('division'),
                ),
//                        array(
//                            'type' => 'select',
//                            'name' => 'status',
//                            'label' => 'Status',
//                            'options' => Stock::get('status'),
//                        ),
            ),
        );
    }

    public static function form_item() {
        return array(
            'form_name' => 'pv_form_item',
            'fields' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'promotions_id',
                    'value' => isset($_GET['pid']) ? $_GET['pid'] : 1,
                ),
                array(
                    'type' => 'date',
                    'name' => 'promotions_startdate',
                    'label' => 'Promotions start date',
                    'col' => 6,
                ),
                array(
                    'type' => 'date',
                    'name' => 'promotions_enddate',
                    'label' => 'Promotions end date',
                    'col' => 6,
                ),
                
                array(
                    'type' => 'text',
                    'name' => 'material_id',
                    'label' => 'Material ID',
                    //'list' => Stock::get('material_id'),
                ),
                array(
                    'type' => 'text',
                    'name' => 'product_name',
                    'label' => 'Product Name',
                    //'list' => Stock::get('product_name'),
                ),
                array(
                    'type' => 'text',
                    'name' => 'asin',
                    'label' => 'ASIN',
                    //'list' => Stock::get('asin'),
                ),
                array(
                    'type' => 'text',
                    'name' => 'rtl_id',
                    'label' => 'Rtl ID',
                    //'list' => Stock::get('rtl_id'),
                ),
                array(
                    'type' => 'number',
                    'name' => 'promotions_budget',
                    'label' => 'Promotions Budget',
                    'step' => '0.001',
                ),
                array(
                    'type' => 'number',
                    'name' => 'promotions_projected_sales',
                    'label' => 'Promotions Projected Sales',
                    'step' => '0.001',
                ),
                array(
                    'type' => 'number',
                    'name' => 'promotions_expected_lift',
                    'label' => 'Promotions Expected Lift',
                    'step' => '0.001',
                ),
                array(
                    'type' => 'text',
                    'name' => 'x_plant_material_status',
                    'label' => 'X Plant material status',
                ),
                array(
                    'type' => 'date',
                    'name' => 'x_plant_status_date',
                    'label' => 'X Plant status date',
                ),
                array(
                    'type' => 'text',
                    'name' => 'promotions_budget_type',
                    'label' => 'Promotions Budget Type',
                ),
                array(
                    'type' => 'number',
                    'name' => 'funding_per_unit',
                    'label' => 'Funding per unit',
                    'step' => '0.001',
                ),
                array(
                    'type' => 'number',
                    'name' => 'forecaseted_qty',
                    'label' => 'Forecaseted qty',
                ),
                array(
                    'type' => 'number',
                    'name' => 'forecasted_unit_sales',
                    'label' => 'Forecasted Unit Sales',
                    'step' => '0.001',
                ),
                array(
                    'type' => 'boolean_checkbox',
                    'name' => 'user_input',
                    'label' => 'User Input',
                ),
                array(
                    'type' => 'boolean_checkbox',
                    'name' => 'validated',
                    'label' => 'Validated',
                ),
                array(
                    'type' => 'number',
                    'name' => 'percent_discount',
                    'label' => 'Percent Discount',
                    'step' => '0.001',
                ),
                array(
                    'type' => 'number',
                    'name' => 'price_discount',
                    'label' => 'Price Discount',
                    'step' => '0.001',
                ),
                array(
                    'type' => 'text',
                    'name' => 'reference',
                    'label' => 'Reference',
                ),
            ),
        );
    }

}
