<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Merge;
use App\Stock;
use App\Calendar;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Redshift\Pgquery;
use App\TimeMachine;

class Sdcalc extends Model {

    protected $table = 'promotions.promotions_preperation';
    public $timestamps = false;
    protected $guarded = array('id');
    protected $fillable = [
        'promo_child_id',
        'material_id',
        'asin',
        'rtl_id',
        'product_name',
        'week',
        'quarter',
        'date_day',
        'ordered_amount',
        'ordered_units',
        'pos_sales',
        'pos_units',
        'calculated_daily_pos_sales',
        'calculated_daliy_pos_units',
        'invoice_amounts',
        'invoice_units',
        'invoice_cost',
    ];
    private $calendar;
    private $merge;
    public $records;

    function inject($input) {

        $this->calendar = new Calendar;
        $this->merge = new Merge;
        $this->time_machine = new TimeMachine;
        $this->record_count = 0;

        $this->spinput = $input;


        echo "On Sdcalc, records {$this->record_count} \n";

        $this->set_psql_where();
        $sql = Pgquery::psql_dayily_pos($this->where_id, $this->where_date);
        $records = DB::connection('redshift')->select($sql);

        $this->record_count = count($records);

        echo "Total number of records in preperation {$this->record_count} \n";


        $this->save_records($records);

        if ($this->record_count) {
            $this->record_one = $records[0];
            // Set material id again
            $this->spinput->material_id = $this->record_one['material_id'];
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

        $this->where_date = " BETWEEN '{$this->spinput->calendar_dates['get']['start_date']}' AND '{$this->spinput->calendar_dates['get']['end_date']}' ";
    }

    function prepare($record) {
        $row['promo_child_id'] = $this->spinput->promo_child_id;
        $row['material_id'] = $this->spinput->material_id;
        $row['asin'] = $this->spinput->asin;
        $row['rtl_id'] = $this->spinput->retailer_id;
        $row['product_name'] = $this->spinput->data['product_name'];
        $row['week'] = $this->time_machine->get_week_sat($record['date_day']);
//        $row['quarter'] = $this->calendar->get_quarter_id($record['date_day']);
        $row['date_day'] = date('Y-m-d', strtotime($record['date_day']));
        $row['ordered_amount'] = Dot::sanitize_numeric($record['ordered_amount'], null, 0);
        $row['ordered_units'] = $record['ordered_units'];
        $row['pos_sales'] = Dot::sanitize_numeric($record['pos_sales'], null, 0);
        $row['pos_units'] = $record['pos_units'];
//        $row['invoice_amounts'] = Dot::sanitize_numeric($record['invoice_sales']);
//        $row['invoice_units'] = Dot::sanitize_numeric($record['invoice_units']);
//        $row['invoice_cost'] = $this->merge->safe_division($row['invoice_amounts'], $row['invoice_units']) * Dot::sanitize_numeric($record['invoice_numerator']);
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
                        ->where('promo_child_id', $this->spinput->promo_child_id)
                        ->avg($column);
    }
    function get_avg_column_week($column, $start_date, $end_date) {
        return self::whereBetween('week', [$start_date, $end_date])
                        ->where('promo_child_id', $this->spinput->promo_child_id)
                        ->avg($column);
    }
    function get_sum_column($column, $start_date, $end_date) {
        return self::whereBetween('date_day', [$start_date, $end_date])
                        ->where('promo_child_id', $this->spinput->promo_child_id)
                        ->sum($column);
    }

    function set_invoice_price() {
        echo "Setting invoice price \n";
        self::where('promo_child_id', $this->spinput->promo_child_id)->orderBy('id')->chunk(100, function ($items) {
            foreach ($items as $item) {
                $invoice = Pgquery::get_invoice($this->spinput->material_id, $item->date_day);

                if (empty($invoice)) {
                    continue;
                }

                $row = [];
                $row['invoice_amounts'] = Dot::sanitize_numeric('invoice_sales', $invoice);
                $row['invoice_units'] = Dot::sanitize_numeric('invoice_units', $invoice);
                $row['invoice_cost'] = $this->merge->safe_division($row['invoice_amounts'], $row['invoice_units']) * Dot::sanitize_numeric('invoice_numerator', $invoice);
                self::where('id', $item->id)->update($row);
            }
        });
    }

    /**
     * 
     * 
     * @param string $where_column
     * @param string $where_value
     */
    function get_column_val($column, $where_column, $where_value) {
        return self::where('promo_child_id', $this->spinput->promo_child_id)
                        ->where($where_column, $where_value)
                        ->value($column);
    }

    function get_preparation_table($promo_child_id) {
        
    }

}
