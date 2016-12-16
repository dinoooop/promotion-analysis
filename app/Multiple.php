<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Temp;

class Multiple extends Model {

    protected $table = 'multiples_csv';
    protected $guarded = array('id');
    protected $fillable = [
        'title',
        'description',
        'file',
        'type',
        'start_id',
        'end_id',
    ];
    public static $messages = [
        'start_id.required' => 'Import failed, file not matching or no valid records',
        'end_id.required' => '',
    ];

    public static function store_rules($param) {
        return [
            'title' => 'required',
//        'description' => '',
//        'type' => '',
            'start_id' => 'required|integer',
            'end_id' => 'required|integer',
        ];
    }

    public static function status($input) {

        $input = Dot::empty_strings2null($input);

        $validation = Validator::make($input, self::store_rules($input), self::$messages);
        if ($validation->passes()) {

            return ['status' => true, 'input' => $input];
        } else {
            return [
                'status' => false,
                'validation' => $validation,
            ];
        }
    }

    public static function display_prepare($record) {
        $record->title = Temp::csv_session_title($record);
        $record->created_at = date('Y-m-d H:i:s', strtotime($record->created_at));
        return $record;
    }

}
