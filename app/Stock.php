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
                    if($key == 'public'){
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

}
