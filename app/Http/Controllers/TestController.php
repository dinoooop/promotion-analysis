<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Dot;
use App\Sdcalc;
use Illuminate\Support\Facades\DB;

class TestController extends BaseController {

    public function __construct() {
        
    }
    
    function test() {
       $obj =  new Sdcalc;
       $obj->input();
    }
    

    function test_02() {
        $str = 'ASIN
-	Avg. Weekly Ordered Units (Quarterly)
-	Baseline
-	Brand
-	COGs During
-	COGs during Baseline period
-	Comments
-	Customer Name
-	Date
-	Discount %
-	Discount Price $
-	Discount-Amount 
-	End Date
-	Fcst Investment
-	Forecast Qty
-	Forecasted $
-	Forecasted Units
-	Funding Source 
-	Incremental %
-	Incremental $
-	Investment  / Unit
-	Investment $
-	Investment $
-	Item id
-	Item Name
-	Level of Promotion
-	Material Description
-	Material ID
-	Normalized Ordered Amount
-	Normalized Ordered COGs
-	Normalized Ordered Units
-	Ordered Amount
-	Ordered Amount During
-	Ordered COGs
-	Ordered Qty During
-	Ordered Units
-	POS During
-	POS during Baseline period
-	POS Qty
-	POS Sales
-	POS Shipped COG Sold
-	PPM % Baseline
-	PPM % During
-	Prod Category
-	Prod Fam
-	Prod Line
-	Prod Platform
-	Promo Description
-	Promo Id
-	Promotion Type
-	Promotions Name
-	Pull Forward / Halo %
-	Pull Forward / Halo $
-	Retailer
-	Retailer ID
-	ROI
-	Segment
-	Start Date
-	Status
-	Wkly Avg. OA Quarterly
-	Wkly Baseline
-	Wkly. Avg. Ordered Amount Post 2 wks
-	Wkly. Pull Forward / Halo $
-	X Plant Material Status
-	Year';
        
        $ex_str = explode('-', $str);
        foreach ($ex_str as $key => $value){
            $value = trim($value);
            $dup = $value;
            $value = strtolower($value);
            $value = str_replace(' ', '_', $value);
            $value = str_replace('%', 'p', $value);
            $value = str_replace('$', 'd', $value);
            $value = str_replace('_/_', '_', $value);
            $value = str_replace('._', '_', $value);
            $value = str_replace('__', '_', $value);
            echo '\''.$value.'\' => [\'label\' => \''.$dup.'\'],<br>';
            //echo '\''.$value.'\',<br>';
        }
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
