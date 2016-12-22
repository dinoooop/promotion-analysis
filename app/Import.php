<?php

namespace App;

use Illuminate\Support\Facades\Log;
use App\promotions\Promotion;
use App\promotions\Item;
use Maatwebsite\Excel\Facades\Excel;

class Import {

    private $promotion;
    private $item;

    function __construct() {
        $this->promotion = new Promotion;
        $this->item = new Item;
    }

    function test_disabled() {
        
    }

    function test() {
        echo '<pre>', print_r(pathinfo('tes.csv')), '</pre>';

        exit();

        //$inputFileName = public_path('downloads/template-promotions.xlsx');
        //$inputFileName = public_path('downloads/Baseline Normalization Thresholds.xlsx');
        //$inputFileName = public_path('downloads/template-promotions-newell.xlsx');
        //$inputFileName = public_path('downloads/test.xlsx');

        $inputFileName = public_path('downloads/tp-recreate.csv');
    }

    function inject($file, $type) {
        $this->file_path = $file;
        $this->type = $type;

        Log::info("Promotion type is {$this->type}");

        Log::info("new csv file path {$this->file_path}");
        if (!is_file($this->file_path) || !file_exists($this->file_path)) {
            Log::info("file does not exist");
            return false;
        }

        $this->return = [];
        Excel::load($this->file_path, function($reader) {
            $results = $reader->get()->toArray();
            Log::info($results[0]);
            if (isset($results[0])) {
                if ($this->type == 'Promotions') {
                    Log::info("Type is promotion");
                    $this->return = $this->import_promotions($results[0]);
                    Log::info("Import completed -");
                } elseif ($this->type == 'Items') {
                    Log::info("Type is items");
                    $return = $this->import_items($results[0]);
                }
            }
        });

        return $this->return;
    }

    function import_items($records) {

        $info = [];
        foreach ($records as $key => $record) {

            // Exit the header
            if ($key == 0) {
                continue;
            }

            $input = $this->item->csv_match_data($record);
            $input = $this->item->generate_item($input);
            $status = Item::status($input);
            if ($status['status']) {
                $model = Item::create($status['input']);
                $info[] = $model->id;
            } else {
                Log::info("CSV input failed (item)");
                Log::info($status['validation']->errors());
                if (isset($input['material_id'])) {
                    Log::info($input['material_id']);
                }
            }
        }

        return $info;
    }

    function import_promotions($records) {

        $info = [];
        foreach ($records as $key => $record) {

            $input = $this->match_the_column_promotion($record);
            if ($input == false) {
                return [];
            }

            Log::info("new input");
            Log::info($input);
            $input['status'] = 'active';

            $status = Promotion::status($input);
            if ($status['status']) {
                $model = Promotion::create($status['input']);
                $info[] = $model->id;
            } else {
                Log::info("CSV input failed (item)");
                Log::info($status['validation']->errors());
                if (isset($input['material_id'])) {
                    Log::info($input['material_id']);
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

    function get_csv_file_path($file_path) {
        $return = [];
        $pathinfo = pathinfo($file_path);

        $excel = $file_path;
        $csv = storage_path('app/csv/' . $pathinfo['filename'] . '.csv');
        Log::info("path XL :>> {$excel}");
        Log::info("path csv :>> {$csv}");
        return (self::PY_CONVERT_TO_CSV($excel, $csv) == true) ? $csv : false;
    }

    function match_the_column_promotion($input) {

        $records = self::get_arrayOf('default');
        $formatted = [];
        $errors = [];

        $dup_input = $input;
        foreach ($dup_input as $key => $value) {
            if (is_null($value)) {
                $dup_input[$key] = '';
            }
        }
        
        foreach ($records as $key => $value) {
            if (!isset($dup_input[$value])) {
                $errors[] = 1;
            }
        }

        if (empty($errors)) {
            foreach ($records as $key => $value) {
                $formatted[$key] = $input[$value];
            }
        }
        return $formatted;
    }

    public static function get_arrayOf($param) {
        $headers = [
            'promotions_name' => 'promotion_name',
            'promotions_description' => 'promo_description',
            'promotions_startdate' => 'promo_start_date',
            'promotions_enddate' => 'promo_end_date',
            'retailer' => 'retailer',
            'retailer_country' => 'retailer_country',
            'newell_status' => 'newell_status',
            'promotions_type' => 'promotions_type',
            'level_of_promotions' => 'level_of_promotion',
            'marketing_type' => 'marketing_type',
            'annivarsaried' => 'anniversaried',
            'promotions_budget' => 'promo_budget',
            'promotions_projected_sales' => 'projected_sales',
            'promotions_expected_lift' => 'expected_lift',
            'promotions_budget_type' => 'budget_type',
            'brand' => 'brand',
            'category' => 'category',
            'division' => 'division',
        ];
        switch ($param) {
            case 'default': return $headers;
                break;
            case 'db_table': return array_keys($headers);
                break;
            case 'csv': return array_values($headers);
                break;
        }
        return [];
    }

    public static function PY_CONVERT_TO_CSV($excel, $csv) {
//        $CSV = public_path('downloads/template-promotions-newell.xlsx');
//        $output_path = storage_path('app/csv/sample.csv');
        $command = escapeshellcmd("python " . public_path('ext-python/convert-csv.py') . " {$excel} {$csv}");
        $output = shell_exec($command);
        return $output;
    }

}
