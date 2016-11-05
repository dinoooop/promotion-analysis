<?php

namespace App;

class Calendar {

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

    function get_quarter($date) {


        $Y = date('Y', strtotime($date));

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
                'start' => $this->get_week_sun($quarter['Q1']['start']),
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
                'end' => $this->get_week_sat($quarter['Q4']['end']),
            ],
        ];


        $return = [];
        foreach ($quarter_refined as $key => $value) {
            if ($date >= $value['start'] && $date <= $value['end']) {
                $return['quarter'] = $key;
                $return['start'] = $value['start'];
                $return['end'] = $this->get_last_week($value['end']);
                $return['week_count'] = $this->week_count($return['start'], $return['end']);
            }
        }

        return $return;
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
     * The end date may greater than today 
     * such case turn the end date as today
     * @param string $end_date end_date
     */
    function get_last_week($end_date) {

        //$today = new date('Y-m-d') ;
        //@testing
        $today = '2016-09-16';
        $end_date = ($end_date > $today) ? $today : $end_date;
        return $this->get_week_sat($end_date);
    }

}
