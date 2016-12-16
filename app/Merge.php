<?php

namespace App;

use Illuminate\Support\Facades\Validator;
use App\promotions\Promotion;
use App\promotions\Item;
use Illuminate\Support\Facades\Log;
use App\promotions\Configuration;
use App\Sdcalc;
use App\Swcalc;
use App\Spod;

class Merge {

    function __construct() {
        $this->promotion = new Promotion;
        $this->item = new Item;
    }

    function refine_dollar($dollar) {
        $dollar = str_replace('$', '', $dollar);
        $dollar = str_replace('%', '', $dollar);
        $dollar = trim($dollar);
        return floatval($dollar);
    }

    function create_sum_select_raw($array) {
        $str = [];
        foreach ($array as $key => $value) {
            $str[] = "sum({$value}) as {$value}";
        }

        return implode(', ', $str);
    }

    function admin_settings($promotion) {

        $default = [
            'baseline_weeks' => 4,
            'post_weeks' => 2,
            'baseline_threshold' => 0.25,
        ];

        $query = Configuration::where('promotions_type', $promotion['promotions_type']);
        $query->where('level_of_promotions', $promotion['level_of_promotions']);

        if ($promotion['retailer'] != '') {
            $query->where('retailer', $promotion['retailer']);
        }
        if ($promotion['brand'] != '') {
            $query->where('brand', $promotion['brand']);
        }

        if ($promotion['category'] != '') {
            $query->where('category', $promotion['category']);
        }

        $settings = $query->first();
        if ($query->count()) {
            $settings = $settings->toArray();
            return array_merge($default, $settings);
        } else {
            return $default;
        }
    }

    function safe_division($numerator, $denominator, $round = false) {
        if ($denominator == 0) {
            return 0;
        }
        if ($round) {
            return round($numerator / $denominator);
        }
        return $numerator / $denominator;
    }

    function url($key, $param) {
        switch ($key) {
            case 'items_index':
                if (isset($param['pid'])) {
                    return url('admin/items?pid=' . $param['pid']);
                }
                break;
            case 'items_create':
                if (isset($param['pid'])) {
                    return url('admin/items/create?pid=' . $param['pid']);
                }
                break;

            default:
                break;
        }

        return false;
    }

    function get_csv_file_path($filename) {
        return storage_path('app/csv/' . $filename);
    }

    function import_csv($file, $type) {

        $file_path = $this->get_csv_file_path($file);
        $records = $this->read_csv($file_path);

        $info = [];

        if ($type == 'promotions') {
            if (!$this->promotion->csv_validate_file($records[0])) {
                return [];
            }
        } else {
            if (!$this->item->csv_validate_file($records[0])) {
                return [];
            }
        }

        foreach ($records as $key => $record) {
            if ($key == 0) {
                continue;
            }

            if ($type == 'promotions') {

                $input = $this->promotion->csv_match_data($record);

                $status = Promotion::status($input);
                if ($status['status']) {
                    $model = Promotion::create($status['input']);
                    $info[] = $model->id;
                } else {
                    Log::info("CSV input failed (promotion)");
                    Log::info($status['custom_validation']);
                    if (isset($input['promotions_name'])) {
                        Log::info($input['promotions_name']);
                    }
                }
            } else {
                $input = $this->item->csv_match_data($record);

                $status = Item::status($input);
                if ($status['status']) {
                    $model = Item::create($status['input']);
                    $info[] = $model->id;
                } else {
                    Log::info("CSV input failed (item)");
                    Log::info($status['custom_validation']);
                    if (isset($input['material_id'])) {
                        Log::info($input['material_id']);
                    }
                }
            }
        }

        return $info;
    }

    function read_csv($path) {

        $records = [];
        if (($handle = fopen($path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $records[] = $data;
            }
            fclose($handle);
        }

        return $records;
    }

    /**
     * 
     * Delete records for the item from the table
     * promo_week
     * promotions_preperations
     * promotions_results
     * @param int $promotion_id
     */
    function reset_records($promotion_id) {
        $ids = Item::where('promotions_id', $promotion_id)->distinct()->pluck('id')->toArray();

        Sdcalc::whereIn('promo_child_id', $ids)->delete();
        Swcalc::whereIn('promo_child_id', $ids)->delete();
        Spod::whereIn('promo_child_id', $ids)->delete();
    }

   

}
