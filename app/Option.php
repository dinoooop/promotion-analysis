<?php

namespace App;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;


class Option extends Model {

    protected $table = 'options';
    protected $guarded = array('id');
    protected $fillable = array('option_name', 'option_value');

    /**
     * 
     * Add new option, if already exist update it
     * @param string $option_name
     * @param mixed $option_value
     * @return boolean id or false
     */
    static function add($option_name, $option_value) {

        if (self::is_option_exist($option_name)) {
            return self::update_option($option_name, $option_value);
        }

        return self::add_option($option_name, $option_value);
    }

    /**
     * 
     * Get the value of given option name
     * @param string $option_name
     * @param mixed $default - if the option name doesn't exist return this default
     * @return mixed option value
     */
    static function get($option_name, $default = null) {

        $option = self::where('option_name', $option_name)->first();

        if (isset($option->option_value)) {

            if (self::is_serialized($option->option_value)) {
                return unserialize($option->option_value);
            }

            return $option->option_value;
        }

        if (!is_null($default)) {
            return $default;
        }

        return false;
    }

    /**
     * 
     * Expected option value is array, so get the specified value of the key
     * @param type $option_name
     * @param type $key
     * @return type
     */
    static function get_option_array($option_name, $key) {
        $options = self::get($option_name);
        return isset($options[$key]) ? $options[$key] : false;
    }

    static function is_option_exist($option_name) {
        $option = self::where('option_name', $option_name)->first();
        if (isset($option->id)) {
            return $option->id;
        }
        return false;
    }

    static function remove($option_name) {

        $option = self::where('option_name', $option_name)->first();

        if (isset($option->id)) {
            $option->delete();
            return $option->id;
        }

        return false;
    }

    // Internal

    /**
     * 
     * Add a new option, whether already exist
     * @param string $option_name
     * @param mixed $option_value
     * @return type id or false
     */
    private static function add_option($option_name, $option_value) {

        if (is_array($option_value)) {
            $option_value = serialize($option_value);
        }

        $option = new self;
        $option->option_name = $option_name;
        $option->option_value = $option_value;
        $option->save();
        return ($option->id) ? $option->id : false;
    }

    private static function update_option($option_name, $option_value) {

        if (is_array($option_value)) {
            $option_value = serialize($option_value);
        }

        $option = self::where('option_name', $option_name)->first();
        $option->option_value = $option_value;
        $option->save();
        return ($option->id) ? $option->id : false;
    }

    public static function is_serialized($value, &$result = null) {

        if (!is_string($value)) {
            return false;
        }

        if ($value === 'b:0;') {
            $result = false;
            return true;
        }

        $length = strlen($value);
        $end = '';

        switch ($value[0]) {
            case 's':
                if ($value[$length - 2] !== '"') {
                    return false;
                }
            case 'b':
            case 'i':
            case 'd':

                $end .= ';';
            case 'a':
            case 'O':
                $end .= '}';
                if ($value[1] !== ':') {
                    return false;
                }
                switch ($value[2]) {
                    case 0:
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        break;
                    default:
                        return false;
                }
            case 'N':
                $end .= ';';
                if ($value[$length - 1] !== $end[0]) {
                    return false;
                }
                break;

            default:
                return false;
        }

        if (($result = @unserialize($value)) === false) {
            $result = null;
            return false;
        }


        return true;
    }
    
    /**
     * 
     * Create table before using the model class Option
     */
    static function create_table() {
        $table_name = 'options';
        Schema::dropIfExists($table_name);
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('option_name');
            $table->text('option_value')->nullable();
            $table->timestamps();
        });
    }

}
