<?php

namespace App;

class Merge {

    function __construct() {
        
    }

    function refine_dollar($dollar) {
        $dollar = str_replace('$', '', $dollar);
        $dollar = str_replace('%', '', $dollar);
        $dollar = trim($dollar);
        return floatval($dollar);
    }

    function create_sum_select_raw($array) {
        $str = [];
        foreach ($array as $key => $value) {
            $str[] = "sum({$value}) as {$value}";
        }

        return implode(', ', $str);
    }

    function admin_settings($param) {
        //@testing
        switch ($param) {
            case 'number_weeks_baseline':
                return 4;
                break;
            case 'number_weeks_post_promotion':
                return 2;
                break;
            case 'baseline_normalization_thresholds':
                return 0.25;
                break;
            case 'post_week_avail_week_count':
                // Execute only the promotion with end_date less than 3 weeks before
                return 3;
                break;

            default:
                return 0;
                break;
        }
    }

    function safe_division($numerator, $denominator) {
        if ($denominator == 0) {
            return 0;
        }
        return $numerator / $denominator;
    }
    
    function url($key, $param) {
        switch ($key) {
            case 'items_index':
                if(isset($param['pid'])){
                    return url('admin/items?pid=' . $param['pid']);
                }
                break;
            case 'items_create':
                if(isset($param['pid'])){
                    return url('admin/items/create?pid=' . $param['pid']);
                }
                break;

            default:
                break;
        }
        
        return false;
    }

}
