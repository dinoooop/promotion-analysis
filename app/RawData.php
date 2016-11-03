<?php

namespace App;

use App\Calendar;
use App\Block;
use App\Stock;
use App\Sdcalc;
use App\Smaterial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class RawData {
    
    private $calendar;
    private $sdcalc;
    private $smaterial;


    public function __construct() {
        $this->calendar = new Calendar;
        
        
        
    }

    function process() {
        
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        //$rec = $this->read_csv();
        $input = Stock::sample_input();
        
        $this->input = new Spinput;
        $this->sdcalc = new Sdcalc;
        $this->smaterial = new Smaterial;
        
        
        $this->input->set_vars($input);
        
        if (!$this->input->validate) {
            Log::info('The given input is not valid');
            return false;
        }
        
        $this->sdcalc->set_vars($this->input->data);
        
        $this->smaterial->create_record($this->sdcalc->record_one);
        $this->sdcalc->save_records($this->sdcalc->records);
        
//        $this->swcalc->set_vars($this->sdcalc->data);
//        $this->swcalc->save_records();
        
        
        
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

}
