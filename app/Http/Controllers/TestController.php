<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Dot;
use App\Sdcalc;
use App\Merge;
use App\RawData;
use App\Calendar;
use App\Printm;
use App\Redshift\Dmaterial;
use App\Redshift\Dsales;
use Illuminate\Support\Facades\DB;

class TestController extends BaseController {

    public function __construct() {
        $this->merge = new Merge;
        $this->rawdata = new RawData;
        $this->calendar = new Calendar;
        $this->printm = new Printm;
        $this->dmaterial = new Dmaterial;
        $this->dsales = new Dsales;
    }
    
    function test() {
        //$this->calendar->get_quarter('2017-10-07');
        //$this->dmaterial->generate();
        $this->dsales->generate();
        exit();
    }
    
    function local_test() {
        $this->printm->print_array();
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
