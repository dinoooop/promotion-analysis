<?php

namespace App;

use App\Calendar;
use App\Block;
use Illuminate\Support\Facades\DB;



class Sdcalc {

    function input() {

        $input = [
            'start_date' => '2016-09-21',
            'end_date' => '2016-10-12',
            'material_id' => '1927322'
        ];

        $promo_start_date = $input['start_date'];
        $promo_end_date = $input['end_date'];
        $material_id = $input['material_id'];

        $calendar = new Calendar();
        $psql_date = $calendar->input($promo_start_date, $promo_end_date);
        //$sql = Block::prepare_psql($material_id, $psql_date);
        $sql = Block::sample_psql();
        DB::setFetchMode(\PDO::FETCH_ASSOC);
        $records = DB::connection('redshift')->select($sql);
        
        echo '<pre>', print_r($records), '</pre>';


        $this->csv_write($records);
    }

    function csv_write($records) {
        $header[] = Block::get_headers();
        $list = array_merge($header, $records);


        $csv = storage_path('app/sample_1.csv');

        $fp = fopen($csv, 'w');

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
    }

}
