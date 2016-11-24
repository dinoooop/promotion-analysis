// Sample Input
return [
            'item_id',
            'promotions_name' => 'Prime Day, 7/12/16',
            'promotion_type' => 'Best Deal',
            'start_date' => '12/07/2016',
            'end_date'=> '12/07/2016',
            'retailer_id' => 'B01ABQBYSO',
            'material_id' => '1954840',
            'promo_description',
            'item_name',
            'investment_d' => '$42.84',
            'forecasted_units' => '1800',
            'forecasted_d' => '',
            'customer_name' => 'Amazon',
            'level_of_promotion' => 'SKU Level',
            'discount_price_d',
            'discount_p',
            'comments',
            'status' => 'Approved'
        ];

Eloquent Model
-------------------------------------
Sdcalc: Store daily POS  information
Swcalc: Store weekly POS  information
Spod: Store result data 
promotions/Item : Store child input data
promotions/promotion : Store master input data
Option: Create meta key value pairs
User: Eloquent Model for user table


Mockup: Prepare the input and manage the process
Spinput : Validate input and set variables for calculation

Form
-------------------------------------------
AppForms : store app html forms
FormHtmlJq: Html form generator
Gform: Prepare form inputs

Helpers
---------------------------------------
Calendar: Custom date related scripts for the app
Stock: Store bulk data
Dot: Common static functions
Merge : Custom object functions

Builders
------------------------------------
Printm: Print codes
RawData: Custom console actions