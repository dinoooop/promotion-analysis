<?php

namespace App;

use App\Calendar;
use App\Block;
use App\Stock;
use App\Smaterial;
use App\Spinput;
use App\Sdcalc;
use App\Swcalc;
use App\Spod;
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

    function init() {

        Config::set('database.fetch', \PDO::FETCH_ASSOC);

        $mode = 'table_user_input';

        switch ($mode) {

            case 'sample':
                $input = Stock::sample_input();
                $this->process($input);
                break;

            case 'table_user_input':
                $records = $this->read_table_user_input();
                foreach ($records as $key => $input) {
                    $this->process($input);
                }
                break;

            case 'csv':
                $records = $this->read_csv();
                foreach ($records as $key => $input) {
                    $this->process($input);
                }
                break;
        }
    }

    function process($input) {

        // @testing 
//        Sdcalc::truncate();
//        Swcalc::truncate();
//        Spod::truncate();
//        Spinput::truncate();

        $this->spinput = new Spinput;
        $this->sdcalc = new Sdcalc;
        $this->swcalc = new Swcalc;
        $this->smaterial = new Smaterial;
        $this->spod = new Spod;


        $this->spinput->set_vars($input);

        if (!$this->spinput->validate) {
            echo "The given input is not valid \n";
            return false;
        }

        echo "The given input is valid \n";
        $this->spinput->promo_id = $this->spinput->create_record($this->spinput->data);

        $this->sdcalc->set_vars($this->spinput);


        $this->smaterial->set_vars($this->sdcalc);
        echo "material created\n";

        if ($this->sdcalc->record_count) {
            $this->swcalc->set_vars($this->sdcalc);
            echo "Var set for swcalc \n";
        }

        echo "checking for neighbourhood quarter \n";
        $this->spinput->set_vars_nh();
        if ($this->spinput->is_require_nhqs) {
            echo "Neighbourhood quarter required (start) \n";
            $this->nh_spinput = new Spinput;
            $this->nh_sdcalc = new Sdcalc;
            $this->nh_swcalc = new Swcalc;
            $input['start_date'] = $this->spinput->weekly_baseline_date;
            $input['end_date'] = $this->spinput->weekly_baseline_date;

            $this->nh_spinput->set_vars($input);
            $this->nh_spinput->promo_id = $this->spinput->promo_id;
            $this->nh_sdcalc->set_vars($this->nh_spinput);

            if ($this->nh_sdcalc->record_count) {
                $this->nh_swcalc->set_vars($this->nh_sdcalc);
                echo "Var set for swcalc \n";
            }
        }

        if ($this->spinput->is_require_nhqe) {
            echo "Neighbourhood quarter required (end) \n";
            $this->nh_spinput = new Spinput;
            $this->nh_sdcalc = new Sdcalc;
            $this->nh_swcalc = new Swcalc;
            $input['start_date'] = $this->spinput->post_weekly_baseline_date;
            $input['end_date'] = $this->spinput->post_weekly_baseline_date;

            $this->nh_spinput->set_vars($input);
            $this->nh_spinput->promo_id = $this->spinput->promo_id;
            $this->nh_sdcalc->set_vars($this->nh_spinput);

            if ($this->nh_sdcalc->record_count) {
                $this->nh_swcalc->set_vars($this->nh_sdcalc);
                echo "Var set for swcalc \n";
            }
        }

        if ($this->sdcalc->record_count) {
            echo "Createing POD \n";
            $this->spod->set_vars($this->swcalc);
            $this->spod->create_record();
        }
        
        
        echo "One promotion completed ------------------------------------------ \n";
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

    function read_table_user_input() {
        return DB::table("user_input")->get();
    }

    

}
