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
            case 'weekly_baseline_number':
                return 4;
                break;
            case 'post_weekly_baseline_number':
                return 2;
                break;

            default:
                break;
        }
    }

    function safe_division($numerator, $denominator) {
        if ($denominator == 0) {
            return 0;
        }
        return $numerator / $denominator;
    }

}
