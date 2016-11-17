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
            case 'pv_form_promotion':
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
                            'type' => 'select',
                            'name' => 'category',
                            'label' => 'Category',
                            'options' => Stock::get('category'),
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'product_family',
                            'label' => 'Product Family',
                            'options' => Stock::get('product_family'),
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'product_line',
                            'label' => 'Product Line',
                            'options' => Stock::get('product_line'),
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'division',
                            'label' => 'Division',
                            'options' => Stock::get('division'),
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'status',
                            'label' => 'Status',
                            'options' => Stock::get('status'),
                        ),
                    ),
                );

                break;


            case 'pv_form_videos':
                return array(
                    'form_name' => 'pv_form_videos',
                    'fields' => array(
                        array(
                            'type' => 'text',
                            'name' => 'title',
                            'label' => 'Video title',
                        ),
                        array(
                            'type' => 'textarea',
                            'name' => 'description',
                            'label' => 'Desription',
                        ),
                        array(
                            'type' => 'file',
                            'name' => 'file',
                            'label' => 'Upload Video',
                            'placeholder' => 'Upload',
                            'textarea' => false,
                            'heading' => 'Upload video',
                            'allow_type' => 'mp4,flv',
                            'allow_size' => 5,
                            'crop' => false,
                            'description' => 'Upload a video file with type mp4, flv and maximum size should not be exceed above 5MB',
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

            case 'pv_form_ads':
                return array(
                    'form_name' => 'pv_form_ads',
                    'fields' => array(
                        array(
                            'type' => 'text',
                            'name' => 'title',
                            'label' => 'Ad title',
                        ),
                        array(
                            'type' => 'url',
                            'name' => 'url',
                            'label' => 'Ad URL',
                        ),
                        array(
                            'type' => 'textarea',
                            'name' => 'description',
                            'label' => 'Desription',
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'ad_location',
                            'label' => 'Ad Position',
                            'options' => Stock::get('ad_location'),
                            'description' => 'By changing ad location you have to re-upload ad file. For more details check Ad position map.',
                        ),
                        array(
                            'type' => 'file',
                            'name' => 'ad_file',
                            'label' => 'Upload Ad image',
                            'placeholder' => 'Upload',
                            'textarea' => false,
                            'heading' => 'Upload Ad',
                            'allow_type' => 'jpg,png,gif,jpeg',
                            'allow_size' => 2,
                            'crop' => true,
                            'description' => 'Upload an image file with type jpg, png, gif or jpeg and maximum size should not be exceed above 2MB.',
                        ),
                    ),
                );
                break;
        }
    }

}
