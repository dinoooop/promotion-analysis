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
use Illuminate\Support\Facades\Validator;

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
        $this->promotion = new Promotion;
    }

    function test() {

        $records = [[
        'promotions_name',
        'promotions_description',
        'promotions_startdate',
        'promotions_enddate',
        'retailer',
        'retailer_country_id',
        'retailer_country',
        'newell_status',
        'promotions_status',
        'promotions_type',
        'level_of_promotions',
        'marketing_type',
        'annivarsaried',
        'promotions_budget',
        'promotions_projected_sales',
        'promotions_expected_lift',
        'promotions_budget_type',
        'brand_id',
        'brand',
        'category',
        'division',
        ]];
        $this->promotion->csv_validate_file($records);
    }

    function local_test() {
        
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
