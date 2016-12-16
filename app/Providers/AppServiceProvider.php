<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\promotions\Item;

class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Validator::extend('itemcomp', function ($attribute, $value, $parameters, $validator) {
            $promotions_id = $parameters[0];
            if($value == null){
                return true;
            }
            $query = Item::where('promotions_id', $promotions_id);
            $query->where($attribute, $value);
            $count = $query->count();

            // count zero means >>> no combination exist or validation success
            return ($count == 0) ? true : false;
        });
        Validator::extend('eaqualafter', function ($attribute, $value, $parameters, $validator) {
            $start_date = $parameters[0];
            $end_date = $value;
            return ($start_date > $end_date) ? false : true;
        });
        /**
         * 
         * material_id or asin shold be filled up
         * You must enter either material id or ASIN
         */
        Validator::extend('masin', function ($attribute, $value, $parameters, $validator) {
            
            $asin = $parameters[0];
            $material_id = $value;
            if ($material_id == null && $asin == null) {
                return false;
            }
            return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

}
