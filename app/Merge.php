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
    
    
    function create_sum_select_raw($array){
        $str = [];
        foreach ($array as $key => $value) {
            $str[]= "sum({$value}) as {$value}";
        }
        
        return implode(', ', $str);
    }
    

}
