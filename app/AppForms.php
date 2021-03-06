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
