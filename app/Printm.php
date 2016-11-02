<?php

namespace App;

use App\Merge;
use App\Spinput;

class Printm {

    public function __construct() {
        $this->calendar = new Calendar;
    }

    function print_sat() {
        $Y = 2017;
        $dup = $Y;

        $jan_first = $Y . '-01-01';
        $sat = $this->calendar->get_week_sat($jan_first);
        echo $sat . '<br>';
        while ($Y == $dup) {
            $sat = date('Y-m-d', strtotime($sat . ' next sat'));
            $Y = date('Y', strtotime($sat));
            echo $sat . '<br>';
        }
    }

    /**
     * Create sample redshift dim_metric_sale
     * @param type $param
     */
    function create_sample_red($param) {


        $row = [
            'item_id' => $record['item_id'],
            'material_id' => $record['material_id'],
            'retailer_id' => $record['retailer_id'],
            'material_description' => $record['material_description'],
            'x_plant_material_status' => $record['x_plant_material_status'],
            'segment' => $record['segment'],
            'brand' => $record['brand'],
            'prod_platform' => $record['prod_platform'],
            'prod_category' => $record['prod_category'],
            'prod_fam' => $record['prod_fam'],
            'prod_line' => $record['prod_line'],
            'retailer' => $record['retailer'],
        ];

        Dmaterial::create($row);

        $row = [
            'item_id' => $record['item_id'],
            'date' => $record['date'],
            'pos_sales' => $record['pos_sales'],
            'pos_qty' => $record['pos_qty'],
            'ordered_amount' => $record['ordered_amount'],
            'ordered_units' => $record['ordered_units'],
            'pos_shipped_cog_sold' => $record['pos_shipped_cog_sold'],
        ];

        Dsales::create($row);
    }

    function print_array() {

        $str = 'insert_pid
-	insert_ts
-	update_pid
-	update_ts
-	item_id
-	retailer_country_id
-	material_id     
-	material_description        
-	description1       
-	brand   
-	sub_brand 
-	division  
-	segment   
-	sub_segment   
-	business_team
-	product_family  
-	product_line
-	product_platform
-	retailer_sku
-	retailer_upc
-	x_plant_matl_status
-	x_plant_valid_from';
        $ex_str = explode('-', $str);
        
        echo '[';
        foreach ($ex_str as $key => $value) {
            $value = trim($value);
            echo '$table->string(\'' . $value . '\');<br>';
        }
        echo ']';
    }

    /**
     * 
     * Pu the string separated by hyphen and get refined key name
     */
    function create_array_key() {
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
        foreach ($ex_str as $key => $value) {
            $value = trim($value);
            $dup = $value;
            $value = strtolower($value);
            $value = str_replace(' ', '_', $value);
            $value = str_replace('%', 'p', $value);
            $value = str_replace('$', 'd', $value);
            $value = str_replace('_/_', '_', $value);
            $value = str_replace('._', '_', $value);
            $value = str_replace('__', '_', $value);
            echo '\'' . $value . '\' => [\'label\' => \'' . $dup . '\'],<br>';
            //echo '\''.$value.'\',<br>';
        }
    }

}
