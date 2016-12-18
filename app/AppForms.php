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
            'submit' => 'Item Level Details <i class="fa fa-angle-double-right" aria-hidden="true"></i>',
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
                    'default' => 'Amazon',
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
                    'type' => 'number',
                    'name' => 'promotions_projected_sales',
                    'label' => 'Promotions Projected Sales',
                    'step' => '0.01',
                ),
                array(
                    'type' => 'number',
                    'name' => 'promotions_expected_lift',
                    'label' => 'Promotions Expected Lift',
                    'step' => '0.01',
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
                ),
                array(
                    'type' => 'auto_complete',
                    'name' => 'category',
                    'label' => 'Category',
                ),
//                        array(
//                            'type' => 'auto_complete',
//                            'name' => 'product_family',
//                            'label' => 'Product Family',
//                        ),
//                        array(
//                            'type' => 'auto_complete',
//                            'name' => 'product_line',
//                            'label' => 'Product Line',
//                        ),
                array(
                    'type' => 'auto_complete',
                    'name' => 'division',
                    'label' => 'Division',
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
                    'type' => 'auto_complete',
                    'name' => 'material_id',
                    'label' => 'Material ID',
                ),
                array(
                    'type' => 'auto_complete',
                    'name' => 'product_name',
                    'label' => 'Product Name',
                ),
                array(
                    'type' => 'auto_complete',
                    'name' => 'asin',
                    'label' => 'ASIN',
                ),
                array(
                    'type' => 'auto_complete',
                    'name' => 'rtl_id',
                    'label' => 'Rtl ID',
                ),
                array(
                    'type' => 'number',
                    'name' => 'promotions_budget',
                    'label' => 'Promotions Budget',
                    'step' => '0.01',
                ),
                array(
                    'type' => 'number',
                    'name' => 'promotions_projected_sales',
                    'label' => 'Promotions Projected Sales',
                    'step' => '0.01',
                ),
                array(
                    'type' => 'number',
                    'name' => 'promotions_expected_lift',
                    'label' => 'Promotions Expected Lift',
                    'step' => '0.01',
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
                    'type' => 'number',
                    'name' => 'promotions_budget_type',
                    'label' => 'Promotions Budget Type',
                    'step' => '0.01',
                ),
                array(
                    'type' => 'number',
                    'name' => 'funding_per_unit',
                    'label' => 'Funding per unit',
                    'step' => '0.01',
                ),
                array(
                    'type' => 'number',
                    'name' => 'forecasted_qty',
                    'label' => 'Forecasted qty',
                ),
                array(
                    'type' => 'number',
                    'name' => 'forecasted_unit_sales',
                    'label' => 'Forecasted Unit Sales',
                    'step' => '0.01',
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
                    'step' => '0.01',
                ),
                array(
                    'type' => 'number',
                    'name' => 'price_discount',
                    'label' => 'Price Discount',
                    'step' => '0.01',
                ),
                array(
                    'type' => 'text',
                    'name' => 'reference',
                    'label' => 'Reference',
                ),
            ),
        );
    }

    /**
     * 
     * 
     * Create a form to import csv file (promotions)
     * @return array
     */
    public static function form_multiple() {
        return array(
            'form_name' => 'form_multiple',
            'submit' => 'Import',
            'fields' => array(
                array(
                    'type' => 'file_upload',
                    'name' => 'multiple_promotion_csv',
                    'label' => 'Upload File',
                    'description' => 'Upload your csv file here. '
                    . '<br /> Download csv template for promotions - <a href="' . asset('downloads/template-promotions.csv') . '">CSV Template (Promotions)</a>'
                    . '<br /> Download csv template for items - <a href="' . asset('downloads/template-items.csv') . '">CSV Template (Items)</a>',
                ),
                array(
                    'type' => 'select',
                    'name' => 'type',
                    'label' => 'File content type',
                    'options' => [
                        'promotions' => 'Promotions',
                        'items' => 'Items'
                    ],
                ),
            ),
        );
    }

    public static function form_configuration() {
        return array(
            'form_name' => 'form_configuration',
            'fields' => array(
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
                    'type' => 'auto_complete',
                    'name' => 'retailer',
                    'label' => 'Retailer',
                ),
                array(
                    'type' => 'auto_complete',
                    'name' => 'brand',
                    'label' => 'Brand',
                ),
                array(
                    'type' => 'auto_complete',
                    'name' => 'division',
                    'label' => 'Division',
                ),
                array(
                    'type' => 'auto_complete',
                    'name' => 'category',
                    'label' => 'Category',
                ),
                array(
                    'type' => 'text',
                    'name' => 'sub_category',
                    'label' => 'Sub Category',
                ),
                array(
                    'type' => 'number',
                    'name' => 'baseline_weeks',
                    'label' => 'Baseline weeks',
                ),
                array(
                    'type' => 'number',
                    'name' => 'post_weeks',
                    'label' => 'Post weeks',
                ),
                array(
                    'type' => 'number',
                    'name' => 'baseline_threshold',
                    'label' => 'Baseline threshold',
                    'step' => '0.01',
                ),
            ),
        );
    }

}
