<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Merge;
use App\Stock;
use App\Calendar;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Sdcalc extends Model {

    protected $table = 'promo_date';
    protected $guarded = array('id');
    protected $fillable = [
        'item_id',
        'week',
        'quarter',
        'date',
        'pos_sales',
        'pos_qty',
        'ordered_amount',
        'ordered_units',
        'pos_shipped_cog_sold',
        'ordered_cogs',
    ];
    private $calendar;
    public $records;

    function set_vars($input) {
        
        Log::info('Setting the sdcalc vars');

        $this->calendar = new Calendar;

        $this->data = $input;

        $this->quarter = $this->calendar->get_quarter($this->data['start_date']);

        $this->set_psql_where();
        
        
        Log::info("Sql condtion for daily pos are : {$this->where_id}, $this->where_date");


        $sql = Stock::psql_dayily_pos($this->where_id, $this->where_date);
        $this->records = DB::connection('redshift')->select($sql);

        $this->record_count = count($this->records);
        
        Log::info("Total number of records for the quarter {$this->quarter['quarter']} is {$this->record_count}");
        
        if ($this->record_count) {
            $this->record_one = $this->records[0];
            $this->material_id = $this->record_one['material_id'];
        }
    }

    function calc($find, $input) {

        switch ($find) {
            case 'ordered_cogs':
                return ($input['pos_shipped_cog_sold'] / $input['pos_qty']) * $input['ordered_units'];
                break;
        }
    }

    function set_psql_where() {
        if (isset($this->data['material_id']) && $this->data['material_id'] != '') {
            $this->material_id = $this->data['material_id'];
            $this->where_id = "m.material_id = '{$this->material_id}'";
        } elseif (isset($this->data['retailer_id']) && $this->data['retailer_id'] != '') {
            $this->retailer_id = $this->data['retailer_id'];
            $this->where_id = " m.retailer_id = '{$this->retailer_id}' ";
        }
        
        $this->where_date = " BETWEEN '{$this->quarter['start']}' AND '{$this->quarter['end']}' ";
    }

    

    function create_record($record) {

        $row = [
            'item_id' => $record['item_id'],
            'week' => $this->calendar->get_week_sat($record['date']),
            'quarter' => $this->quarter['quarter'],
            'date' => date('Y-m-d', strtotime($record['date'])),
            'pos_sales' => $record['pos_sales'],
            'pos_qty' => $record['pos_qty'],
            'ordered_amount' => $record['ordered_amount'],
            'ordered_units' => $record['ordered_units'],
            'pos_shipped_cog_sold' => $record['pos_shipped_cog_sold'],
            'ordered_cogs' => $this->calc('ordered_cogs', $record),
        ];

        self::create($row);
    }

    function save_records($records) {

        foreach ($records as $key => $record) {
            $this->create_record($record);
        }
        
        Log::info("Required daily pos records are saved in promo_date table successfully");
    }

}
