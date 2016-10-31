<?php

namespace App;

use App\Calendar;
use App\Block;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Sdcalc {

    function input() {
        $rec = $this->read_csv();
        foreach ($rec as $key => $value) {
            $this->loop($value);
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
//                $num = count($data);
                $row[] = $data;
            }
            fclose($handle);
        }
        
        return $row;
    }
    
    function loop($input) {
        $promo_start_date = $input[0];
        $promo_end_date = $input[1];
        $material_id = $input[2];

        $calendar = new Calendar();
        $psql_date = $calendar->input($promo_start_date, $promo_end_date);
        $sql = Block::prepare_psql($material_id, $psql_date);
        //$sql = Block::sample_psql();
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        $records = DB::connection('redshift')->select($sql);

        $this->csv_write($records);
    }

}
