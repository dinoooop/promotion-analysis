<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Gform;
use App\FormHtmlJq;
use App\AppForms;
use App\Merge;
use App\Dot;
use App\Temp;
use App\Multiple;
use App\promotions\Promotion;
use App\promotions\Item;
use App\Redshift\Pgquery;

class AjaxController extends Controller {

    private $merge;

    public function __construct() {

        $this->merge = new Merge;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $input = Input::get();

        switch ($input['action']) {

            case 'auto_populate':
                $this->auto_populate($input);
                break;

            default :
                echo Dot::json_boolean_response([]);
                break;
        }
        exit();
    }

    function auto_populate($input) {
        $result = Pgquery::get_items_material_id($input['material_id']);
        if (!empty($result)) {
            $row = [
                'product_name' => $result->material_description,
                'x_plant_material_status' => $result->x_plant_matl_status,
                'x_plant_status_date' => date('m/d/Y', strtotime($result->x_plant_valid_from)),
            ];
            echo Dot::json_boolean_response($row);
        }else{
            echo Dot::json_boolean_response([]);
        }
    }

}
