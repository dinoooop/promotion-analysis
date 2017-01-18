<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Dot;
use App\Sdcalc;
use App\Sample;
use App\Swcalc;
use App\Merge;
use App\RawData;
use App\Calendar;
use App\Printm;
use App\Mockup;
use App\Redshift\Dmaterial;
use App\Redshift\Dsales;
use App\Redshift\Dchannel;
use Illuminate\Support\Facades\DB;
use App\promotions\Promotion;
use App\promotions\Item;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Import;
use App\TimeMachine;
use App\Redshift\Pgquery;

class TestController extends BaseController {

    public function __construct() {
        $this->merge = new Merge;
        $this->rawdata = new RawData;
        $this->calendar = new Calendar;
        $this->printm = new Printm;
        $this->dmaterial = new Dmaterial;
        $this->dsales = new Dsales;
        $this->dchannel = new Dchannel;
        $this->swcalc = new Swcalc;
        $this->sdcalc = new Sdcalc;
        $this->promotion = new Promotion;
        $this->item = new Item;
        $this->sample = new Sample;
        $this->import = new Import;
        $this->time_machine = new TimeMachine;
    }

    function test() {
        
        
        $this->test_date();
        
    }
    
    function test_date() {
        $start_date = '2016-03-20';
        $end_date = '2016-03-26';
        $baseline_weeks_count = 4;
        $post_weeks_count = 2;
        $normalize_weeks_count = 13;
        $dates = $this->time_machine->init($start_date, $end_date, $baseline_weeks_count, $post_weeks_count, $normalize_weeks_count);
        
        echo '<pre>', print_r($dates), '</pre>';
    }

    function test_child_items_input() {

        $input = [
            'promotions_id' => 1,
            'promotions_startdate' => '08/12/2016',
            'promotions_enddate' => '07/12/2016',
            'material_id' => '',
            'product_name' => '',
            'asin' => '1954840',
            'rtl_id' => 'B01ABQBYSO',
            'promotions_budget',
            'promotions_projected_sales',
            'promotions_expected_lift',
            'x_plant_material_status',
            'x_plant_status_date',
            'funding_per_unit' => '42.84',
            'forecasted_qty' => 1800,
            'forecasted_unit_sales',
            'promoted',
            'user_input',
            'validated',
            'percent_discount',
            'price_discount',
            'reference',
        ];
        $status = Item::status($input);
        if ($status['status']) {
            echo "inserted";
            Item::create($status['input']);
        } else {
            echo '<pre>', print_r($status['validation']->errors()), '</pre>';
        }
    }

    function test_sdample_input() {
        //Promotion::update_promotion_status(3, 'sleep');

        $input['promotions_id'] = 7;
        $input['material_id'] = NULL;
        $input['promotions_startdate'] = '2016-06-11';
        $input['promotions_enddate'] = '2016-06-11';
        $input['daily_baseline_pos_sales'] = '';
        $input['asin'] = NULL;
        $input['product_name'] = 'Test product';
        $input['status'] = 'sleep';
        $input['daily_baseline_pos_sales'] = NULL;

        //$item = $this->item->generate_item($input);

        $status = Sample::status($input);
        if ($status['status']) {
            echo "inserted";
            Sample::create($status['input']);
        } else {
            echo '<pre>', print_r($status['validation']->errors()), '</pre>';
        }
    }

    function local_test() {
        $array = Sample::all();
        foreach ($array as $key => $value) {
            if (is_null($value->daily_baseline_pos_sales)) {
                echo 'date null';
            }
        }
    }

    function test_02() {
        
    }

    function test_01() {
        //$users = DB::table('users')->select('name', 'email as user_email')->get();
        $records = DB::connection('redshift')
                ->table('nwl_pcm.sap_material_additional')
                ->first();
//        $records = DB::connection('pgsql')
//                ->table('users')
//                ->first();
        echo '<pre>', print_r($records), '</pre>';
        exit();
    }

}
