<?php

namespace App;

use Illuminate\Support\Facades\Validator;
use App\promotions\Promotion;
use App\promotions\Item;
use Illuminate\Support\Facades\Log;

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

    function admin_settings($param) {
        //@testing
        switch ($param) {
            case 'number_weeks_baseline':
                return 4;
                break;
            case 'number_weeks_post_promotion':
                return 2;
                break;
            case 'baseline_normalization_thresholds':
                return 0.25;
                break;
            case 'post_week_avail_week_count':
                // Execute only the promotion with end_date less than 3 weeks before
                return 3;
                break;

            default:
                return 0;
                break;
        }
    }

    function safe_division($numerator, $denominator) {
        if ($denominator == 0) {
            return 0;
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

    function import_csv($path, $type) {
        $records = $this->read_csv($path);
        $info = [];
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

}
