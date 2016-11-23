<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Dot;
use App\Sdcalc;
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
use Illuminate\Support\Facades\Config;

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
    }

    function test() {
        //$this->calendar->quarter_weeks();
        //$this->dmaterial->generate();
        //$this->dchannel->generate();
        //$this->rawdata->process();
        //echo date('Y-m-d', strtotime('2016-07-12' . "-2 weeks"));
        //echo '<pre>', print_r(range("Q1", "Q9")), '</pre>';
        echo date('d-m-Y', strtotime('16-01-17'));
        
        Config::set('database.fetch', \PDO::FETCH_ASSOC);
        
        $this->mockup = new Mockup;

        $this->mockup->promotion_chunk();
    }

    function local_test() {
        //$date = $this->calendar->init('2016-11-16', '2016-11-16');
        $date = $this->calendar->get_quarter_info('2016-12-31');
        echo '<pre>', print_r($date), '</pre>';
        exit();
        
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
