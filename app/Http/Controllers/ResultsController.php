<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Temp;
use App\FormHtmlJq;
use App\AppForms;
use App\promotions\Promotion;
use App\Option;
use App\Multiple;
use App\Spod;
use App\promotions\Item;
use App\Merge;
use App\Dot;

class ResultsController extends Controller {


    private $formHtmlJq;
    private $posts;
    private $item;

    public function __construct() {
        $this->model = new Spod;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        
        $data = [];
        $input = Input::get();

        $query = Spod::orderBy('id', 'asc');
        if (isset($input['pid'])) {
            $data['promotion'] = Promotion::findOrFail($input['pid']);
            $query->where('promotions_id', $input['pid']);
        }else{
            return Dot::R404();
        }

        $data['records'] = $query->paginate(50);
        return View::make('admin.results.index', $data);
    }

}
