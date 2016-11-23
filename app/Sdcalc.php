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
    public $timestamps = false;
    protected $guarded = array('id');
    protected $fillable = [
        'item_id',
        'promo_id',
        'week',
        'quarter',
        'date_day',
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

        $this->calendar = new Calendar;

        $this->spinput = $input;
        
        echo "On Sdcalc, records {$this->record_count} \n";
        echo "Checking  for other quarters \n";

        $quarters = $this->spinput->calendar_dates['all_quarters'];
        echo "total quarters " . count($quarters) . "\n";
        foreach ($quarters as $key => $quarter_id) {
            
            $this->quarter_id = $quarter_id;
            
            echo "collecting daily_pos for the quarter id {$this->quarter_id} \n";

            $this->quarter = $this->calendar->get_quarter_info($quarter_id);
            
            $this->set_psql_where();
            $sql = Stock::psql_dayily_pos($this->where_id, $this->where_date);
            $records = DB::connection('redshift')->select($sql);

            $this->record_count = count($records);


            $this->save_records($records);

            if ($this->record_count) {
                $this->record_one = $records[0];
                // Set material id again
                $this->spinput->material_id = $this->record_one['material_id'];
            }
        }
    }

    function calc($find, $input) {

        switch ($find) {
            case 'ordered_cogs':
                if ($input['pos_qty'] == 0) {
                    return 0;
                }
                return ($input['pos_shipped_cog_sold'] / $input['pos_qty']) * $input['ordered_units'];
                break;
        }
    }

    function set_psql_where() {
        if ($this->spinput->material_id != '') {
            $this->where_id = " m.material_id = '{$this->spinput->material_id}' ";
        } elseif ($this->spinput->retailer_id != '') {
            $this->where_id = " m.retailer_sku = '{$this->spinput->retailer_id}' ";
        }

        $this->where_date = " BETWEEN '{$this->quarter['start_date']}' AND '{$this->quarter['end_date']}' ";
    }

    function create_record($record) {

        $row['promo_id'] = $this->spinput->promo_id;
        $row['week'] = $this->calendar->get_week_sat($record['date_day']);
        $row['quarter'] = $this->quarter_id;
        $row['date_day'] = date('Y-m-d', strtotime($record['date_day']));
        $row['pos_sales'] = $record['pos_sales'];
        $row['pos_qty'] = $record['pos_units']; // pos_qty
        $row['ordered_amount'] = $record['ordered_amount'];
        $row['ordered_units'] = $record['ordered_units'];
        $row['pos_shipped_cog_sold'] = $record['pos_shipped_cogs'];
        $row['ordered_cogs'] = $this->calc('ordered_cogs', $row);

        self::create($row);
    }

    function save_records($records) {
        foreach ($records as $key => $record) {
            $this->create_record($record);
        }
    }

}
