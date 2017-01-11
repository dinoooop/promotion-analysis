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
                    'type' => 'auto_complete',
                    'name' => 'retailer',
                    'label' => 'Retailer',
                    'default' => 'Amazon',
                ),
                array(
                    'type' => 'textarea',
                    'name' => 'promotions_description',
                    'label' => 'Promotions Description',
                    'col' => 12,
                ),
                array(
                    'type' => 'date',
                    'name' => 'promotions_startdate',
                    'label' => 'Promotions start date',
                ),
                array(
                    'type' => 'date',
                    'name' => 'promotions_enddate',
                    'label' => 'Promotions end date',
                    'col' => 6,
                ),
//                array(
//                    'type' => 'text',
//                    'name' => 'retailer_country_id',
//                    'label' => 'Retailer Country Id',
//                ),
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
                    'type' => 'number',
                    'name' => 'promotions_projected_sales',
                    'label' => 'Promotions Projected Sales ($)',
                    'step' => '0.01',
                    'placeholder' => '$',
                ),
                array(
                    'type' => 'number',
                    'name' => 'promotions_expected_lift',
                    'label' => 'Promotions Expected Lift (%)',
                    'step' => '0.01',
                    'placeholder' => '%',
                ),
                array(
                    'type' => 'select',
                    'name' => 'promotions_budget_type',
                    'label' => 'Promotions Budget Type',
                    'options' => Stock::get('promotions_budget_type'),
                ),
                array(
                    'type' => 'auto_complete',
                    'name' => 'brand',
                    'label' => 'Brand',
                ),
                array(
                    'type' => 'auto_complete_tags',
                    'name' => 'category',
                    'label' => 'Category',
                    'placeholder' => '',
                    'url' => url('admin/ajax?action=auto_complete_tag&col=category'),
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
                array(
                    'type' => 'boolean_checkbox',
                    'name' => 'annivarsaried',
                    'label_checkbox' => 'YES',
                    'label' => 'Is this promotion annivarsaried?',
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
    public static function form_multiple_promotion() {
        return array(
            'form_name' => 'form_multiple',
            'submit' => 'Start Import <i class="fa fa-upload" aria-hidden="true"></i>',
            'fields' => array(
                array(
                    'type' => 'file_upload',
                    'name' => 'multiple_promotions',
                    'label' => 'Upload promotion (xlsx)',
                    'description' => '<br /><strong><a href="' . asset('downloads/template-promotions-newell.xlsx') . '">Download promotions template</a></strong>',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'type',
                    'value' => 'Promotions',
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
    public static function form_multiple_items($param) {
        $form = array(
            'form_name' => 'form_multiple',
            'submit' => 'Start Import <i class="fa fa-upload" aria-hidden="true"></i>',
            'fields' => array(
                array(
                    'type' => 'file_upload',
                    'name' => 'multiple_promotions',
                    'label' => 'Upload items (xlsx)',
                    'disabled' => $param['disable_item_input'],
                    'description' => '<br><strong><a href="' . asset('downloads/template-items.csv') . '">Download item template</a></strong>'
                    . '<br />Note: You don\'t need to upload promoted items for promotions that are across brand or category',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'type',
                    'value' => 'Items',
                ),
            ),
        );

        if(isset($param['csvid'])) {
            $form['fields'][] = array(
                'type' => 'hidden',
                'name' => 'csvid',
                'value' => $param['csvid'],
            );
        }
        
        if(isset($param['pid'])) {
            $form['fields'][] = array(
                'type' => 'hidden',
                'name' => 'pid',
                'value' => $param['pid'],
            );
        }
        
        return $form;
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
