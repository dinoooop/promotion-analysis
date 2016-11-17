<?php

namespace App\Redshift;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Pgquery {

    public static function get_distinct_column_values($column) {

        switch ($column) {
            case 'retailer':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_retailer_channel')
                                ->distinct()
                                ->pluck('retailer');
                break;
            case 'brand':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->distinct()
                                ->pluck('brand');
                break;
            
            case 'category':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->distinct()
                                ->pluck('business_team');
                break;
            
            case 'division':
                return DB::connection('redshift')
                                ->table('nwl_pos.dim_material')
                                ->distinct()
                                ->pluck('division');
                break;

            default:
                return [];
                break;
        }
    }

}
