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
    
    function print_drop_table_psql() {
        $table_using = [
            'migrations',
            'password_resets',
            'promo_date',
            'promo_input',
            'promo_items',
            'redshift_dim_material',
            'users',
            'nwl_pos.metric_sales',
            'nwl_pos.dim_material',
        ];
        
        $psql = '';
        foreach ($table_using as $key => $value) {
            $psql .=  "DROP TABLE IF EXISTS {$value};";
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

    function print_array() {

        $str = 'insert_pid
-	insert_ts
-	update_pid
-	update_ts
-	insert_key
-	item_id 
-	retailer_country_id 
-	date_day 
-	channel_attribute_id
-	unfilled_ordered_units
-	pre_order_sales_price
-	pre_order_amount
-	pre_order_sales_rank
-	pre_order_units
-	pre_order_quantity_rank
-	orders
-	ordered_sales_rank
-	ordered_units
-	ordered_amount
-	ordered_units_rank
-	shipped_sales_rank
-	shipped_units_rank
-	cat_average_selling_price
-	cat_perorder_sales_rank
-	cat_perorder_units_rank
-	cat_ordered_sales_rank
-	cat_ordered_units_rank
-	cat_shipped_sales_rank
-	cat_shipped_units_rank
-	subcat_perorder_sales_rank
-	subcat_perorder_units_rank
-	subcat_ordered_sales_rank
-	subcat_ordered_units_rank
-	subcat_shipped_sales_rank
-	subcat_shipped_units_rank
-	avg_customer_review
-	conversion_percentile
-	customer_reviews
-	page_view_index
-	page_view_rank
-	unique_visitor_index ';
        $ex_str = explode('-', $str);
        
        echo '[';
        foreach ($ex_str as $key => $value) {
            $value = trim($value);
            echo '$table->string(\'' . $value . '\');<br>';
            //echo '\'' . $value . '\',<br>';
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
