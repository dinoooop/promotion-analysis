<?php

namespace App;

use App\Calendar;
use App\Block;
use App\Stock;
use App\Sdcalc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class RawData {
    
    private $calendar;
    private $sdcalc;


    public function __construct() {
        $this->calendar = new Calendar;
        $this->sdcalc = new Sdcalc;
        
        
    }

    function input_test() {
        //$rec = $this->read_csv();
        $input = Stock::sample_input();
        
        $this->input = new Spinput;
        $this->input->set_input($input);
        
        
        if (!$this->input->validate) {
            return false;
        }
        
        
        
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        $record = DB::connection('redshift')->select($this->input->psql_daily);
        
        // CREATE MATERIAL
        $row = [    
            'item_id' => $record['item_id'],
            'material_id' => $record['material_id'],
            'retailer_id' => $record['retailer_id'],
            'material_description' => $record['material_description'],
            'x_plant_material_status' => $record['x_plant_material_status'],
            'segment' => $record['segment'],
            'brand' => $record['brand'],
            'prod_platform' => $record['prod_platform'],
            'prod_category' => $record['prod_category'],
            'prod_fam' => $record['prod_fam'],
            'prod_line' => $record['prod_line'],
            'retailer' => $record['retailer'],
        ];

        Smaterial::create($row);
        
        // CREAT DAILY POS
        $row = [    
            'item_id' => $record['item_id'],
            'week' => $this->calendar->get_week_sat($record['date']),
            'quarter' => $this->input->quarter['quarter'],
            'date' => date('Y-m-d', strtotime($record['date'])),
            'pos_sales' => $record['pos_sales'],
            'pos_qty' => $record['pos_qty'],
            'ordered_amount' => $record['ordered_amount'],
            'ordered_units' => $record['ordered_units'],
            'pos_shipped_cog_sold' => $record['pos_shipped_cog_sold'],
            'ordered_cogs' => $this->sdcalc->calc('ordered_cogs', $record),
        ];
        
        Sdcalc::create($row);
        
        
        //$this->csv_write($records);
    }

    function csv_write($list) {
        //$header[] = Block::get_headers();
        //$list = array_merge($header, $records);

        $csv = storage_path('app/sample_02.csv');

        $fp = fopen($csv, 'a+');

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
    }

    function read_csv() {

        $csv = storage_path('app/input_01.csv');

        if (($handle = fopen($csv, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row[] = $data;
            }
            fclose($handle);
        }

        return $row;
    }

    function loop($input) {


        
    }
    
    
    
    

}
