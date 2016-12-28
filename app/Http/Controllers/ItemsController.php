<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\Gform;
use App\Merge;
use App\Dot;
use App\FormHtmlJq;
use App\AppForms;
use App\Temp;
use App\promotions\Item;
use App\promotions\Promotion;
use App\Multiple;

class ItemsController extends Controller {

    private $gform;
    private $formHtmlJq;
    private $item;

    public function __construct() {
        $this->gform = new Gform;
        $this->formHtmlJq = new FormHtmlJq;
        $this->item = new Item;
        $this->merge = new Merge;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {

        $data = array();

        $input = Input::get();

        $data['pagination_appends'] = $input;
        $data['href_prepare_result'] = route('prepare_promotion', $data['pagination_appends']);
        
        $data['pagination_appends'] = array_merge($data['pagination_appends'], ['rec' => 1]);
        $data['href_recalculate_promotion'] = route('prepare_promotion', $data['pagination_appends']);

        if (!isset($input['pid'])) {
            return Response::make(View::make('errors.404', ['page_404' => true]), 404);
        }


        $data['promotion'] = Promotion::findOrFail($input['pid']);
        $data['promotions_id'] = $input['pid'];

        if ($data['promotion']->status == 'processing') {
            return View::make('admin.promotions.editnotallow', $data);
        }
        if ($data['promotion']->status == 'completed') {
            $data['display_recalculate_button'] = true;
        }
        if ($data['promotion']->status == 'sleep') {
            $data['display_prepare_result_button'] = true;
        }

        // Hide step view on edit mode 
        if (isset($input['hsv']) && $input['hsv'] == 1) {
            $data['item_edit_mode_view'] = true;
        } 



        $data['count'] = Item::where('promotions_id', $input['pid'])->count();
        $data['display_message_items'] = (in_array($data['promotion']->level_of_promotions, ['Brand', 'Category'])) && ($data['count'] == 0);

        return View::make('admin.items.index', $data);
    }

    public function kendo_index() {
        $data = array();

        $input = Input::get();
        $query = Item::orderBy('id', 'asc');

        if (isset($input['pid'])) {
            $query->where('promotions_id', $input['pid']);
            $records = $query->get()->toArray();
            return response()->json($records);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {

        $input = Input::get();


        if (!isset($input['pid'])) {
            return Response::make(View::make('errors.404', ['page_404' => true]), 404);
        }

        $data = [];

        $data['promotion'] = Promotion::find($input['pid']);

        $form = $this->gform->set_form(AppForms::form_item());
        $form['form_name'] = 'pv_create_item';

        $data['form_create'] = $this->formHtmlJq->create_form($form);



        return View::make('admin.items.create', $data);
    }

    function create_kendo() {
        $input = Input::all();
        return response()->json($input);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {

        $input = Input::all();

        foreach ($input['models'] as $key => $value) {
            $value['promotions_id'] = $input['pid'];
            $value = $this->item->generate_item($value);
            $status = Item::status($value);
            if ($status['status']) {
                Item::create($status['input']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $data = array();
        $data['record'] = Item::find($id);
        return View::make('admin.items.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {

        $data = array();

        $input = Input::get();

        if (!isset($input['pid'])) {
            return Dot::R404();
        }

        $data['promotion'] = Promotion::find($input['pid']);

        $data['record'] = Item::find($id);

        $form = $this->gform->set_form(AppForms::form_item(), $data['record']);
        $form['form_name'] = 'pv_edit_item';
        $data['form_edit'] = $this->formHtmlJq->create_form($form);
        return View::make('admin.items.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {

        $input = Input::all();

        if (!isset($input['models'])) {
            return false;
        }

        $models = $input['models'];
        foreach ($models as $key => $model) {
            $id = $model['id'];
            $model = $this->item->generate_item($model);
            $status = Item::status($model, true);
            if ($status['status']) {
                $record = Item::find($id);
                $record->update($status['input']);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        $input = Input::all();

        $models = $input['models'];
        foreach ($models as $key => $value) {
            $item = Item::find($value['id']);
            if (isset($item->id)) {
                $item->delete();
            }
        }

        exit();
        //return Redirect::route('items.index');
    }

}
