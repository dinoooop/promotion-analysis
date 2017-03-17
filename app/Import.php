<?php

namespace App;

use Illuminate\Support\Facades\Log;
use App\promotions\Promotion;
use App\promotions\Item;
use Maatwebsite\Excel\Facades\Excel;
use App\Stock;

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

    function inject($input) {
        Log::info($input);
        $this->file_path = $input['csv_file'];
        $this->type = $input['type'];
        $this->pid = isset($input['pid']) ? $input['pid'] : null;

        if (!is_file($this->file_path) || !file_exists($this->file_path)) {
            Log::info("file does not exist");
            return false;
        }

        $this->return = [];
        Excel::load($this->file_path, function($reader) {
            $results = $reader->get()->toArray();

            if (isset($results[0])) {
                
                if ($this->type == 'Promotions') {
                    Log::info("Type is promotion");
                    $this->return = $this->import_promotions($results[0]);
                    Log::info("Import completed -");
                } elseif ($this->type == 'Items') {
                    Log::info("Type is items");
                    Log::info($results[0]);
                    $this->return = $this->import_items($results[0]);
                } elseif ($this->type == 'pid_items') {
                    Log::info("Import items for single promotion");
                    Log::info($results[0]);
                    
                    $this->return = $this->import_items($results[0], $this->pid);
                }
            }
        });

        return $this->return;
    }

    function import_items($records, $pid = null) {

        $info = [];
        foreach ($records as $key => $record) {
            
            if(!is_null($pid)){
                $record['promotions_id'] = $pid;
            }

            $input = $this->match_the_column($record, 'items');
            
            Log::info("Matched columns");
            Log::info($input);

            if ($input == false) {
                continue;
            }
            

            $input = $this->item->generate_item($input);
            Log::info("CSV import items value generated");
            Log::info($input);
            
            if ($input == false) {
                continue;
            }
            
            $status = Item::status($input);
            if ($status['status']) {
                $model = Item::create($status['input']);
                $info[] = $model->id;
            } else {
                Log::info("CSV input failed (items)");
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

            $input = $this->match_the_column($record, 'promotions');

            if ($input == false) {
                continue;
            }

            $input['status'] = 'sleep';

            $status = Promotion::status($input);
            if ($status['status']) {
                $model = Promotion::create($status['input']);
                $info[] = $model->id;
            } else {
                Log::info("CSV input failed (promotion)");
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
        Log::info("Convert xl to csv");
        Log::info($file_path);
        $return = [];
        $pathinfo = pathinfo($file_path);

        $excel = $file_path;
        $csv = storage_path('app/csv/' . $pathinfo['filename'] . '.csv');
        Log::info("csv path");
        Log::info($csv);
        $return = (self::PY_CONVERT_TO_CSV($excel, $csv) == true) ? $csv : false;
        if($return){
            Log::info("Excel converted to csv - success");
        }else{
            Log::info("Excel converted to csv - failed");
        }
        
        return $return;
    }

    function match_the_column($input, $type) {

        $records = Stock::csv_header($type);
        
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

    public static function PY_CONVERT_TO_CSV($excel, $csv) {
//        $excel = public_path('downloads/template-promotions-newell.xlsx');
//        $csv = storage_path('app/csv/sample.csv');
        $command = escapeshellcmd("python " . public_path('ext-python/convert-csv.py') . " {$excel} {$csv}");
        $output = shell_exec($command);
        echo $output;
        return $output;
    }

}
