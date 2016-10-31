<?php

namespace App;

class Calendar {

    function input($start, $end) {
        $return['start_date'] = $this->get_4week_sun($start);
        $return['end_date'] = $this->get_2week_sat($end);
        return $return;
        
    }

    function is_sunday($date) {
        $week = date('D', strtotime($date));
        if ($week == 'Sun') {
            return true;
        }
        return false;
    }
    
    function is_saturday($date) {
        $week = date('D', strtotime($date));
        if ($week == 'Sat') {
            return true;
        }
        return false;
    }
    
    function get_4week_sun($date) {
        if($this->is_sunday($date)){
            return date('Y-m-d', strtotime($date . '-4 weeks'));
        }
        $sunday = date('Y-m-d', strtotime($date . ' previous sun'));
        return date('Y-m-d', strtotime($sunday . '-4 weeks'));
    }
    function get_2week_sat($date) {
        if($this->is_saturday($date)){
            return date('Y-m-d', strtotime($date . '+2 weeks'));
        }
        $sat = date('Y-m-d', strtotime($date . ' next sat'));
        return date('Y-m-d', strtotime($sat . '+2 weeks'));
    }

}
