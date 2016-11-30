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
    public static $form_create_rules = [];

    public static function validate($input) {
        $error = [];

        if (!$input->hasFile('multiple_promotion_csv')) {
            $error['message'][] = 'CSV not attached';
            $error['status'] = false;
            return $error;
        }

        $allow_extension = ['csv', 'txt'];
        $extention = $input->multiple_promotion_csv->extension();
        if (!in_array($extention, $allow_extension)) {
            $error['message'][] = 'Please upload valid file with data';
            $error['status'] = false;
            return $error;
        }


        $error['status'] = true;
        return $error;
    }

    public static function sanitize($input) {
        $title = pathinfo($input->file('multiple_promotion_csv')->getClientOriginalName())['filename'];
        $file_name = date('Y-m-d-h-i-s') . rand(1000, 9999) . '.csv';
        $path = $input->multiple_promotion_csv->storeAs('csv', $file_name);
        $path = storage_path('app/' . $path);


        $sanitize = [
            'title' => $title,
            'description' => Dot::sanitize_string('description', $input),
            'file' => $file_name,
            'type' => $input->type,
            'start_id' => '',
            'end_id' => '',
        ];

        return $sanitize;
    }

    public static function status($input) {
        //$validation = Validator::make($input, self::$form_create_rules);
        $custom_validation = self::validate($input);
        if ($custom_validation['status']) {
            $input = self::sanitize($input);
            return ['status' => true, 'input' => $input];
        } else {
            return [
                'status' => false,
                'validation' => $validation,
                'custom_validation' => $custom_validation
            ];
        }
    }

    public static function display_prepare($record) {
        $record->title = Temp::csv_session_title($record);
        $record->created_at = date('Y-m-d H:i:s', strtotime($record->created_at));
        return $record;
    }

}
