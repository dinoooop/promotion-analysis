<?php

namespace App;

class Calendar {

    private $merge;

    public function __construct() {
        $this->merge = new Merge;
        $this->today = date("Y-m-d");
        //$this->today = '2016-09-17';
    }

    function input($start, $end) {
        $return['start_date'] = $this->get_the_weeks($start, 4, 'before');
        $return['end_date'] = $this->get_the_weeks($end, 2, 'after');
        return $return;
    }

    function is_week($date, $week_param = 'Sat') {
        $week = date('D', strtotime($date));
        if ($week == $week_param) {
            return true;
        }
        return false;
    }

    /**
     * 
     * @param string $date 
     * @param int $count number week to take after/before the date
     * @param string $time_machine after/before
     * @return string A date
     */
    function get_the_weeks($date, $count, $time_machine = 'after') {

        if ($time_machine == 'before') {
            $count = '-' . $count;
            if ($this->is_week($date, 'Sun')) {
                return date('Y-m-d', strtotime($date . "{$count} weeks"));
            } else {
                $sun = date('Y-m-d', strtotime($date . 'previous sun'));
                return date('Y-m-d', strtotime($sun . "{$count} weeks"));
            }
        } else {
            // For after (sat)
            $count = '+' . $count;
            if ($this->is_week($date, 'Sat')) {
                return date('Y-m-d', strtotime($date . "{$count} weeks"));
            } else {
                $sat = date('Y-m-d', strtotime($date . 'next sat'));
                return date('Y-m-d', strtotime($sat . "{$count} weeks"));
            }
        }
    }

    function get_week_sat($date) {
        if (!$this->is_week($date)) {
            $date = date('Y-m-d', strtotime($date . 'next sat'));
        }
        return $date;
    }

    function get_week_sun($date) {
        if (!$this->is_week($date, 'Sun')) {
            $date = date('Y-m-d', strtotime($date . 'previous sun'));
        }
        return $date;
    }

    function date_difference($start, $end) {

        $start = strtotime($start);
        $end = strtotime($end);
        $datediff = $end - $start;
        return floor($datediff / (60 * 60 * 24));
    }

    /**
     * 
     * Get the informations related to quarter for the given date
     * @param string $date a date
     * @return array
     */
    function get_quarter_info($date) {

        $Y = date('Y', strtotime($date));

        $quarters = $this->get_quarters_year($Y);
        
        $return = [];
        
        foreach ($quarters as $key => $quarter) {
            if ($date >= $quarter['start'] && $date <= $quarter['end']) {
                $return['quarter'] = $key;
                $return['id'] = $quarter['end'];
                $return['start_date'] = $this->get_week_start($quarter['start']);
                $return['end_date'] = $this->get_week_end($quarter['end']);
                $return['week_count'] = $this->week_count($return['start_date'], $return['end_date']);
            }
        }
        
        return $return;
    }

    /**
     * 
     * Get the quarter 
     * @param type $Y
     * @return array
     */
    function get_quarters_year($Y) {

        $quarter = [
            'Q1' => [
                'start' => "{$Y}-01-01",
                'end' => "{$Y}-03-31",
            ],
            'Q2' => [
                'start' => "{$Y}-04-01",
                'end' => "{$Y}-06-30",
            ],
            'Q3' => [
                'start' => "{$Y}-07-01",
                'end' => "{$Y}-09-30",
            ],
            'Q4' => [
                'start' => "{$Y}-10-01",
                'end' => "{$Y}-12-31",
            ],
        ];

        $quarter_refined = [
            'Q1' => [
                'start' => $quarter['Q1']['start'],
                'end' => $this->get_week_sat($quarter['Q1']['end']),
            ],
            'Q2' => [
                'start' => $this->get_week_sun(date('Y-m-d', strtotime($quarter['Q2']['start'] . ' next week'))),
                'end' => $this->get_week_sat($quarter['Q2']['end']),
            ],
            'Q3' => [
                'start' => $this->get_week_sun(date('Y-m-d', strtotime($quarter['Q3']['start'] . ' next week'))),
                'end' => $this->get_week_sat($quarter['Q3']['end']),
            ],
            'Q4' => [
                'start' => $this->get_week_sun(date('Y-m-d', strtotime($quarter['Q4']['start'] . ' next week'))),
                'end' => $quarter['Q4']['end'],
            ],
        ];

        return $quarter_refined;
    }

    /**
     * 
     * End date of a quarter will be treated as quarter id
     * @param string $date
     * @return string date
     */
    function get_quarter_id($date) {
        $return = $this->get_quarter_info($date);
        return $return['id'];
    }

    /**
     * 
     * Get total number weeks in a quarter
     */
    function week_count($start, $end) {
        $diff_dates = $this->date_difference($start, $end);
        return floor($diff_dates / 7) + 1;
    }

    /**
     * 
     * Get the end date of a week 
     * Exception if today 28-12-2016 return 2016-12-31 whether it is sat or not
     * The end date may greater than today such case turn the end date as today
     * @param string $date may be a quarter end_date
     */
    function get_week_end($date) {

        //$today = date('Y-m-d');
        $today = $this->today;

        // @testing
        // $today = '2015-12-28';

        $m = date('m', strtotime($date));

        if ($m == 12) {
            // A December
            $Y = date('Y', strtotime($date));
            $date = ($date > $today) ? $today : $date;
            $date_sat = $this->get_week_sat($date);
            $new_year_eve = "{$Y}-12-31";
            return ($date_sat > $new_year_eve) ? $new_year_eve : $date_sat;
        } else {
            $date = ($date > $today) ? $today : $date;
            return $this->get_week_sat($date);
        }
    }

    /**
     * 
     * 
     * Get the start date of this week 
     * Exception if today 02-01-2016 return 01-01-2016
     * @param string $date quarter end_date
     */
    function get_week_start($date = '2015-01-05') {

        $m = date('m', strtotime($date));

        if ($m == 01) {
            // January
            $Y = date('Y', strtotime($date));

            $week_start = $this->get_week_sun($date);
            $new_year = "{$Y}-01-01";
            return ($week_start < $new_year) ? $new_year : $week_start;
        } else {

            return $this->get_week_sun($date);
        }
    }

    function get_req_dates($start_date, $end_date) {

        $date = [];

        $date['start_date'] = $start_date;
        $date['end_date'] = $end_date;
        $date['year'] = date('Y', strtotime($date['start_date']));

        if ($date['start_date'] == $date['end_date']) {
            $date['start_week'] = $this->get_week_sat($date['start_date']);
            $date['end_week'] = $date['start_week'];
            $quarter[] = $this->get_quarter_id($date['start_date']);
            $date['quarters'] = $quarter;
        } else {
            $date['start_week'] = $this->get_week_sat($date['start_date']);
            $date['end_week'] = $this->get_week_sat($date['end_date']);

            $start_date_quarter = $this->get_quarter_id($date['start_date']);

            $end_date_quarter = $this->get_quarter_id($date['end_date']);


            while ($start_date_quarter <= $end_date_quarter) {
                $quarter[] = $start_date_quarter;
                $next_day = date('Y-m-d', strtotime($start_date_quarter . ' next day'));
                $start_date_quarter = $this->get_quarter_id($next_day);
            }

            $quarters = array_unique($quarter);
            sort($quarters);
            $date['quarters'] = $quarters;
        }

        return $date;
    }

    /**
     * 
     * @param type $date promotion start date
     */
    function get_number_weeks_baseline($date) {
        $week_number = $this->merge->admin_settings('number_weeks_baseline');
        return date('Y-m-d', strtotime($date . " -{$week_number} weeks"));
    }

    function get_number_weeks_post_promotion($date) {
        $week_number = $this->merge->admin_settings('number_weeks_post_promotion');
        return date('Y-m-d', strtotime($date . " +{$week_number} weeks"));
    }

    function init($start_date, $end_date) {
        $dates = [];
        $dates['during'] = $this->get_req_dates($start_date, $end_date);

   

        $baseline_start_date = $this->get_number_weeks_baseline($start_date);
        $baseline_end_date = date('Y-m-d', strtotime($start_date . " -1 days"));

        $dates['baseline'] = $this->get_req_dates($baseline_start_date, $baseline_end_date);

        $post_start_date = date('Y-m-d', strtotime($end_date . " +1 days"));
        $post_end_date = $this->get_number_weeks_post_promotion($end_date);

        $dates['post'] = $this->get_req_dates($post_start_date, $post_end_date);

        $all_quarters = array_merge($dates['during']['quarters'], $dates['baseline']['quarters'], $dates['post']['quarters']);
        $all_quarters = array_unique($all_quarters);
        sort($all_quarters);

        $dates['all_quarters'] = $all_quarters;
        
        return $dates;
    }
    
    /**
     * 
     * Execute only the promotion with end_date less than 3 weeks before
     * @param string end date
     * 
     */
    function is_avail_post_week($date) {
        
        $week_number = $this->merge->admin_settings('post_week_avail_week_count');
        $sun = $this->get_week_sun($this->today);
        $post_week = date('Y-m-d', strtotime($sun . " -{$week_number} weeks"));
        
        if($post_week > $date){
            return true;
        }
        
        return false;
    }

}
