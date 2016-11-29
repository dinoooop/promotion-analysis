<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
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
    
    
    public static function display_prepare($record) {
        $record->title = Temp::csv_session_title($record);
        $record->created_at = date('Y-m-d H:i:s', strtotime($record->created_at));
        return  $record;
    }

}
