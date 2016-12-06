<?php

namespace App;

use App\Merge;
use App\Spinput;
use App\Dot;
use App\promotions\Promotion;
use App\Redshift\Pgquery;

class Printm {

    public function __construct() {
        $this->calendar = new Calendar;
    }
    
    
    function get_hyphen_data() {

        $str = 'material 
 division 
    ean_upc     
 ean_upc_category 
       ean_upc_category_text       
 alt_uom 
 alt_uom_text 
 numerator 
 denominator 
 length 
 width  
 height 
 dim_unit 
  volume   
 vol_unit 
 gross_weight 
 weight_uom 
 metric_length 
 metric_width 
 metric_height 
 metric_dim_unit 
 metric_volume 
 metric_vol_unit 
 metric_gross_weight 
 metric_net_weight 
 metric_weight_uom 
      insert_ts      
      insert_pid      
      update_ts      
      update_pid';

        return $str;
    }

    /**
     * 
     * Print all saturday of an year
     */
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

    function print_drop_table_psql() {
        $table_using = [
            'migrations',
            'password_resets',
            'users',
            'promo_input',
            'promo_date',
            'promo_items',
            'promo_week',
            'promo_pod',
            'user_input',
                //'nwl_pos.metric_sales',
                //'nwl_pos.dim_material',
                //'nwl_pos.metric_online_channel',
        ];

        $psql = '';
        foreach ($table_using as $key => $value) {
            $psql .= "DROP TABLE IF EXISTS {$value};";
        }

        echo $psql;
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

    

  

    function refine_hyphen_array($str) {
        
        $str = str_replace("\n", '-', $str);
        $ex_str = explode('-', $str);
        $return = [];
        foreach ($ex_str as $key => $value) {
            $return[] = trim($value);
        }
        return $return;
    }

    function print_array_key_value() {
        $str = $this->get_hyphen_data();
        $refined = $this->refine_hyphen_array($str);
        $records[] = Dot::get_array_key_value($refined);
        
        foreach ($records as $value) {
            echo "[\n";
            foreach ($value as $key => $val) {
                echo "'{$key}' => '{$val}', \n";
            }
            echo "]\n";
        }
    }

    function print_array_table_schema_create() {
        $str = $this->get_hyphen_data();
        $ex_str = $this->refine_hyphen_array($str);
        
        echo "\n";
        foreach ($ex_str as $key => $value) {
            //echo "\$table->double('{$value}', 15, 8);\n";
            echo "\$table->string('{$value}')->nullable();\n";
            // echo "'{$value}' => \$this->promotion['{$value}'],\n";
            // echo "'{$value}' => \$this->get_from_item('{$value}'),\n";
            
        }
        
        echo "\n";
    }

    function print_array_simple() {
        
        $str = $this->get_hyphen_data();

        $records = $this->refine_hyphen_array($str);

        echo "\n";
        foreach ($records as $key => $value) {
            echo "'{$value}',\n";
        }
        echo "\n";
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

  

    function create_array_from_table_browser() {

        $records = Promotion::all()->toArray();

        echo '[<br/>';
        foreach ($records as $value) {
            echo '[<br/>';
            foreach ($value as $key => $val) {
                echo "'{$key}' => '{$val}', <br/>";
            }
            echo '],<br/>';
        }
        echo ']<br/>';
    }
    
    /**
     * 
     * php artisan raw_data sample_test
     */
    function sample_test() {
        Pgquery::get_distinct_column_values();
    }

}
