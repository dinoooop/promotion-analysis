<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Merge;
use App\Stock;
use App\Calendar;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Sdcalc extends Model {

    protected $table = 'promotions.promotions_preperation';
    public $timestamps = false;
    protected $guarded = array('id');
    protected $fillable = [
        'promo_child_id',
        'week',
        'quarter',
        'date_day',
        'ordered_amount',
        'ordered_units',
        'pos_sales',
        'pos_units',
    ];
    private $calendar;
    public $records;

    function inject($input) {

        $this->calendar = new Calendar;
        $this->record_count = 0;

        $this->spinput = $input;

        echo "On Sdcalc, records {$this->record_count} \n";
        echo "Checking  for other quarters \n";

        $quarters = $this->spinput->calendar_dates['all_quarters'];

        echo "total quarters " . count($quarters) . "\n";

        foreach ($quarters as $key => $quarter_id) {

            echo "collecting daily_pos for the quarter id {$quarter_id} \n";

            $this->quarter_id = $quarter_id;
            $this->quarter = $this->calendar->get_quarter_info($quarter_id);

            $this->set_psql_where();
            $sql = Stock::psql_dayily_pos($this->where_id, $this->where_date);
            $records = DB::connection('redshift')->select($sql);

            $this->record_count = count($records) + $this->record_count;

            echo "Total number of records for the condition {$this->where_id} {$this->record_count} \n";


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
                if ($input['pos_units'] == 0) {
                    return 0;
                }
                return ($input['pos_shipped_cog_sold'] / $input['pos_units']) * $input['ordered_units'];
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

    function prepare($record) {
        $row['promo_child_id'] = $this->spinput->promo_child_id;
        $row['week'] = $this->calendar->get_week_sat($record['date_day']);
        $row['quarter'] = $this->calendar->get_quarter_id($record['date_day']);
        $row['date_day'] = date('Y-m-d', strtotime($record['date_day']));
        $row['ordered_amount'] = $record['ordered_amount'];
        $row['ordered_units'] = $record['ordered_units'];
        $row['pos_sales'] = $record['pos_sales'];
        $row['pos_units'] = $record['pos_units'];
        return $row;
    }

    function save_records($records) {
        foreach ($records as $key => $record) {
            $row = $this->prepare($record);
            self::create($row);
        }
    }

    function get_avg_column($column, $start_date, $end_date) {
        return self::whereBetween('date_day', [$start_date, $end_date])
                        ->where('promo_child_id', $this->promo_child_id)
                        ->avg($column);
    }

}
