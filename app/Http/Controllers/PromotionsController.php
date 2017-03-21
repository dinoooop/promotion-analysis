<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Gform;
use App\Temp;
use App\FormHtmlJq;
use App\AppForms;
use App\promotions\Promotion;
use App\Option;
use App\Multiple;
use App\promotions\Item;
use App\Merge;
use App\Dot;
use App\Stock;

class PromotionsController extends Controller {

    private $gform;
    private $formHtmlJq;
    private $posts;
    private $item;

    public function __construct() {
        $this->gform = new Gform;
        $this->formHtmlJq = new FormHtmlJq;
        $this->posts = new Promotion;
        $this->merge = new Merge;
        $this->item = new Item;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $data = array();
        $input = Input::get();
        if (isset($input['re']) && $input['re'] == 1) {
            $data['page_heading'] = 'Promotion Results';
            $data['display_result_view_button'] = true;
            $data['kendo_url'] = url('kendo/promotions?re=1');
        } else {
            $data['page_heading'] = 'Promotion Overview';
            $data['kendo_url'] = url('kendo/promotions');
        }

        $form = $this->gform->set_form(AppForms::show_hide_column());
        $form['hide_submit_button'] = true;
        $data['form_show_hide_column'] = $this->formHtmlJq->create_form($form);

        return View::make('admin.promotions.index', $data);
    }

    public function kendo_index() {
        $input = Input::get();

        $query = Promotion::orderBy('id', 'desc');
        if (isset($input['re']) && $input['re'] == 1) {
            // Get result records
            $query->where('status', 'completed');
            $records = $query->get()->toArray();
        } elseif (isset($input['fil']) && $input['fil'] == 'retailer') {
            
            // Column dropdownlist retailer
            $records = $query->pluck('retailer')->toArray();
            $records = Dot::set_title_kento($records);
            return response()->json($records);
            
        } elseif (isset($input['fil']) && $input['fil'] == 'brand') {
            
            // Column dropdownlist brand
            $records = $query->pluck('brand')->toArray();
            $records = Dot::set_title_kento($records);
            return response()->json($records);
            
        } else {
            // Get all records
            $records = $query->get()->toArray();
        }

        foreach ($records as $key => $value) {
            $records[$key]['status'] = Stock::get_value('status', $value['status']);
        }
        return response()->json($records);
    }

    /**
     * 
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {

        $data = array();

        $form = $this->gform->set_form(AppForms::form_promotion());
        $form['form_name'] = 'pv_create_promotion';

        $data['form_create'] = $this->formHtmlJq->create_form($form);

        return View::make('admin.promotions.create', $data);
    }

    /**
     * 
     * Store a newly created resource in storage.
     * @return Response
     */
    public function store() {
        $input = Input::all();

        $status = Promotion::status($input);

        if ($status['status']) {
            $status['input']['status'] = 'sleep';

            $promotion = Promotion::create($status['input']);

            if (in_array($input['level_of_promotions'], ['Category', 'Brand'])) {
                return Redirect()->route('prepare_promotion', ['pid' => $promotion->id]);
            }

            return Redirect::route('items.index', ['pid' => $promotion->id]);
        }

        return Redirect::route('promotions.create')
                        ->withInput()
                        ->withErrors($status['validation'])
                        ->with('message', 'Validation error');
    }

    /**
     * 
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $data = array();
        $data['record'] = Promotion::find($id);
        return View::make('admin.promotions.show', $data);
    }

    /**
     * 
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {

        $data = array();
        $data['record'] = Promotion::findOrFail($id);

        if ($data['record']->status == 'processing') {
            return View::make('admin.promotions.editnotallow', $data);
        }

        $data['pagination_appends'] = ['pid' => $id];

        $form = $this->gform->set_form(AppForms::form_promotion(), $data['record']);
        $form['form_name'] = 'pv_edit_promotion';
        $form['submit'] = 'Save';
        $form['hide_submit_button'] = true;
        $data['form_edit'] = $this->formHtmlJq->create_form($form);

        if ($data['record']->status == 'completed') {
            $data['display_recalculate_button'] = true;
        }

        return View::make('admin.promotions.edit', $data);
    }

    /**
     * 
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {

        $input = Input::all();

        $promotion = Promotion::find($id);

        if ($promotion->status == 'processing') {
            return Redirect::route('promotions.edit', $id)
                            ->withInput()
                            ->withErrors(['Calculation running mode, not permitted to edit.'])
                            ->with('message', 'Validation error');
        }

        // Promotion Save for edit
        if (isset($input['save'])) {
            $input['status'] = $promotion->status;
            $status = Promotion::status($input);

            if ($status['status']) {

                $promotion->update($status['input']);
                $this->item->have_child_items($promotion);

                return Redirect::route('promotions.index');
            }
        } elseif (isset($input['re_run'])) {
            // Promotion Save for RE-RUN

            $input['status'] = 'active';
            $status = Promotion::status($input);
            if ($status['status']) {
                $promotion = Promotion::find($id);
                $promotion->update($status['input']);
                $this->item->have_child_items($promotion);

                $data['pagination_appends']['pid'] = $id;
                $data['pagination_appends']['rec'] = 1;
                return Redirect::route('prepare_promotion', $data['pagination_appends']);
            }
        }

        return Redirect::route('promotions.edit', $id)
                        ->withInput()
                        ->withErrors($status['validation'])
                        ->with('message', 'Validation error');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        $promotion = Promotion::find($id);
        Item::where('promotions_id', $promotion->id)->delete();
        Option::remove('have_child_items_' . $promotion->id);
        $promotion->delete();
        exit();
        //return Redirect::route('promotions.index');
    }

    function update_promotion_status($promotion_id, $status) {
        Promotion::update_promotion_status($promotion_id, $status);
        exit(0);
    }

}
