<?php

namespace App;

use Illuminate\Support\Facades\Log;
use App\promotions\Promotion;
use App\promotions\Item;

class Import {

    private $promotion;
    private $item;

    function __construct() {
        $this->promotion = new Promotion;
        $this->item = new Item;
    }

    function inject($file, $type) {
        $this->file = $file;
        $this->type = $type;
        $this->file_path = $this->get_csv_file_path($file);
        if (!is_file($this->file_path) || !file_exists($this->file_path)) {
            Log::info("file does not exist");
            return false;
        }

        $this->records = $this->read_csv($this->file_path);
        $this->headings = $this->records[0];


        if ($this->type == 'items') {
            Log::info("Item csv import");
            if ($this->item->csv_validate_file($this->headings)) {
                return $this->import_items();
            }else{
                Log::info("Head not matching");
            }
        } elseif ($this->type == 'promotions') {
            Log::info("Promotion csv import");
            if ($this->promotion->csv_validate_file($this->headings)) {
                return $this->import_promotions();
            }
        }

        return [];
    }

    function import_items() {

        $info = [];
        foreach ($this->records as $key => $record) {

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

    function import_promotions() {

        $info = [];
        foreach ($this->records as $key => $record) {

            // Exit the header
            if ($key == 0) {
                continue;
            }

            $input = $this->promotion->csv_match_data($record);

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

    function get_csv_file_path($filename) {
        return storage_path('app/csv/' . $filename);
    }

}
