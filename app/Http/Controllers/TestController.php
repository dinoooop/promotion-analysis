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
use Illuminate\Support\Facades\DB;

class TestController extends BaseController {

    public function __construct() {
        $this->merge = new Merge;
        $this->rawdata = new RawData;
        $this->calendar = new Calendar;
        $this->printm = new Printm;
        $this->dmaterial = new Dmaterial;
    }
    
    function test() {
        //$this->calendar->get_quarter('2017-10-07');
        //$this->dmaterial->generate();
        $this->printm->print_drop_table_psql();
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
