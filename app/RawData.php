<?php

namespace App;

use App\Calendar;
use App\Block;
use App\Stock;
use App\Smaterial;
use App\Spinput;
use App\Sdcalc;
use App\Swcalc;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class RawData {
    
    private $calendar;
    private $sdcalc;
    private $smaterial;


    public function __construct() {
        $this->calendar = new Calendar;
        
        
        
    }

    function process() {
        
        echo "\n";
        
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        //$rec = $this->read_csv();
        $input = Stock::sample_input();
        
        $this->spinput = new Spinput;
        $this->sdcalc = new Sdcalc;
        $this->swcalc = new Swcalc;
        $this->smaterial = new Smaterial;
        
        
        $this->spinput->set_vars($input);
        
        if (!$this->spinput->validate) {
            echo "The given input is not valid \n";
            return false;
        }
        
        echo "The given input is valid \n";
        
        $this->sdcalc->set_vars($this->spinput);
        
        $this->smaterial->set_vars($this->sdcalc);
        echo "material created\n";
        
        if($this->sdcalc->record_count){
            $this->swcalc->set_vars($this->sdcalc);
            echo "Var set for swcalc \n";
        }
        
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
