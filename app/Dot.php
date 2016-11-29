<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Dot {

    function __construct() {
        
    }

    public static function get_slug($table, $column, $title, $id = null) {
        $slug = self::create_slug($title);

        $query = DB::table($table);
        $query->whereRaw("$column LIKE '$slug%'");
        if (!is_null($id)) {
            $query->where('id', '!=', $id);
        }
        $query->select('id', $column);
        $query->orderBy('id', 'desc');
        $record = $query->first();

        if (isset($record->id)) {
            $exist = $record->$column;
            $count = str_replace($slug, '', $exist);
            $count = str_replace('-', '', $count);
            $count = ($count == '') ? 0 : $count;
            return $slug . '-' . ++$count;
        }

        return $slug;
    }

    public static function create_slug($title) {
        $title = trim($title);
        $title = preg_replace('/[^a-zA-Z0-9- ]/', '', $title);
        $title = str_replace("'", '', $title);
        $title = str_replace(' ', '-', $title);
        $title = strtolower($title);
        return $title;
    }

    public static function create_array_key($key) {
        $key = trim($key);
        $key = preg_replace('/[^a-zA-Z0-9- ]/', '', $key);
        $key = str_replace("'", '', $key);
        $key = str_replace(' ', '_', $key);
        $key = strtolower($key);
        return $key;
    }

    public static function get_array_key_value($array) {
        $return = [];
        foreach ($array as $key => $value) {
            $refined_key = self::create_array_key($value);
            $return[$refined_key] = $value;
        }
        return $return;
    }

    public static function get_model_by_slug($table) {

        $slug = basename(Request::url());
        $record = DB::table($table)
                ->where('slug', $slug)
                ->first();
        if (isset($record->id)) {
            $result = array();
            foreach ($record as $key => $value) {
                $result[$key] = $value;
            }
            return $result;
        }

        return false;
    }

    public static function json_boolean_response($result) {
        if (!empty($result)) {
            $response = array('status' => true, 'result' => $result);
        } else {
            $response = array('status' => false);
        }

        return json_encode($response);
    }

    public static function create_name_value_array($array) {

        $new_array = array();

        foreach ($array as $key => $value) {
            $row = array();
            $row['key'] = $key;
            $row['value'] = $value;
            $new_array[] = $row;
        }

        return json_encode($new_array);
    }

    /**
     * 
     * Convert a single class object into array
     * @param object $record
     * @return array
     */
    public static function convert_array($record) {
        $result = array();
        foreach ($record as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }

    public static function enable() {
        echo '<pre>', print_r(Stock::get('roles')), '</pre>';
    }

    public static function validate_date($date) {

        $wrong = '1970-01-01';
        $date = date('Y-m-d', strtotime($date));

        if ($date == $wrong) {
            return false;
        }

        return true;
    }

    public static function have_value($key, $input) {
        return (isset($input[$key]) && $input[$key] != '') ? $input[$key] : null;
    }

    /**
     * 
     * Print a custom log
     * @param array/string $data
     */
    static function print_log($data) {

        ob_start();

        echo PHP_EOL . '------------------------------------------------' . PHP_EOL;
        print_r($data);

        $contents = ob_get_contents();
        ob_end_clean();

        Log::info($contents);
    }

    public static function sanitize_numeric($key, $input) {
        if (isset($input[$key]) && is_numeric($input[$key])) {
            return $input[$key];
        }

        return 0;
    }

    public static function sanitize_boolean($key, $input) {
        if (!isset($input[$key])) {
            return 0;
        }

        if (is_bool($input[$key])) {
            return $input[$key];
        }

        $value = trim($input[$key]);

        if (strtolower($value) == 'true' || $value == '1' || $value == 1) {
            return 1;
        }

        return 0;
    }

    public static function validate_true($key, $input) {

        if (!isset($input[$key])) {
            return false;
        }


        if (is_array($input[$key])) {
            if (empty($input[$key])) {
                return false;
            }
        } else {
            if (trim($input[$key]) == '') {
                return false;
            }
        }


        return true;
    }

}
