<?php

namespace App;

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

}
