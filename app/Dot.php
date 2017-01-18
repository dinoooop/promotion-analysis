<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

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

    /**
     * 
     * Round the integer value for the database
     * @param array $input
     */
    public static function general_round($input, $precision = 2) {
        foreach ($input as $key => $value) {
            if (is_numeric($value)) {
                $input[$key] = round($value, $precision);
            }
        }
        return $input;
    }

    public static function sanitize_numeric($key, $input = null, $round = 2) {

        // If input not exist key is an integer given to round
        if (is_null($input)) {
            if (is_numeric($key)) {
                return round($key, $round);
            }
        }

        if (isset($input[$key]) && is_numeric($input[$key])) {
            return round($input[$key], $round);
        }

        return null;
    }

    public static function sanitize_string($key, $input) {

        if (!isset($input[$key])) {
            return NULL;
        }

        $trim = trim($input[$key]);
        if ($trim == '') {
            return NULL;
        }

        return $trim;
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

    public static function R404() {
        return Response::make(View::make('errors.404', ['page_404' => true]), 404);
    }

    /**
     * 
     * Do not insert insert empty string to table set it to null
     * @param type $input
     * @return type
     */
    public static function empty_strings2null($input) {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                continue;
            }

            $input[$key] = (trim($value) == '') ? NULL : $value;
        }

        return $input;
    }

    /**
     * 
     * Check in $first for $key if not exist go for $second
     * 
     * @param string $key
     * @param array $first
     * @param array $second
     * @param string $second_key
     * @return string
     */
    public static function get_first_second($key, $first, $second, $second_key = null) {

        if (isset($first[$key]) && $first[$key] != '') {
            return $first[$key];
        }

        if (!is_null($second_key)) {
            if (isset($second[$second_key]) && $second[$second_key] != '') {
                return $second[$second_key];
            }
        } elseif (isset($second[$key]) && $second[$key] != '') {
            return $second[$key];
        }
        return NULL;
    }

    public static function save_attachment($input, $field) {

        if (!$input->hasFile($field)) {
            $error['message'][] = 'File not attached';
            $error['status'] = false;
            return $error;
        }


        $allow_extension = ['xls', 'xlsx', 'txt', 'bin'];
        $extention = $input->file($field)->extension();

        if (!in_array($extention, $allow_extension)) {
            $error['message'][] = 'Please upload valid file with data';
            $error['status'] = false;
            return $error;
        }


        $pathinfo = pathinfo($input->file($field)->getClientOriginalName());

        $title = $pathinfo['filename'];
        $file_name = date('Y-m-d-h-i-s') . rand(1000, 9999) . '.' . $pathinfo['extension'];
        $path = $input->file($field)->storeAs('csv', $file_name);
        $path = storage_path('app/' . $path);

        $output = shell_exec("chmod 777 {$path}");
        Log::info("Changing the permision of uploading file");
        Log::info($output);

        $store = [
            'title' => $title,
            'file_name' => $file_name,
            'file_path' => $path,
            'status' => true,
        ];

        return $store;
    }

    /**
     * 
     * Check weather the array are equal
     * @param type $array1
     * @param type $array2
     * @return boolean
     */
    public static function is_array_eaqual($array1, $array2) {
        $in = [];
        foreach ($array1 as $key => $value) {
            if (in_array($value, $array2)) {
                $in[] = $value;
            }
        }

        return (count($in) == count($array1) && count($in) == count($array2));
    }

    public static function is_amazon($promotion) {

        if (isset($promotion->retailer) && (strtolower($promotion->retailer) == 'amazon' || strtolower($promotion->retailer) == 'amz')) {
            return true;
        } elseif (isset($promotion['retailer']) && (strtolower($promotion['retailer']) == 'amazon' || strtolower($promotion['retailer']) == 'amz')) {
            return true;
        }

        return false;
    }

    public static function iecho($string, $force = null) {
        $app_env = env('IECHO');
        if ($app_env == 'on') {
            echo $string . "\n";
        } elseif (!is_null($force)) {
            echo $string . "\n";
        }
    }

    public static function get_obj_array_val($key, $stack) {

        if (is_array($stack)) {
            return isset($stack[$key]) ? $stack[$key] : null;
        } else {
            return isset($stack->$key) ? $stack->$key : null;
        }
    }

}
