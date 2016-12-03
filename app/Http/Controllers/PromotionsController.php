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
use App\Merge;
use App\Multiple;
use App\promotions\Item;

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

        $query = Promotion::orderBy('id', 'desc');
        if (isset($input['cvids'])) {
            $multiple = Multiple::findOrFail($input['cvids']);
            $query->whereBetween('id', [$multiple->start_id, $multiple->end_id]);
        }

        $data['records'] = $query->paginate(50);
        return View::make('admin.promotions.index', $data);
    }

    /**
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
            
            $promotion = Promotion::create($status['input']);
            if ($input['level_of_promotions'] == 'Category') {
                $this->item->insert_items_under_promotion($promotion, $input['category'], 'category');
            }
            if ($input['level_of_promotions'] == 'Brand') {
                $this->item->insert_items_under_promotion($promotion, $input['brand'], 'brand');
            }
            return Redirect::route('promotions.index');
        }

        return Redirect::route('promotions.create')
                        ->withInput()
                        ->withErrors($status['custom_validation'])
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
        $data['record'] = Promotion::find($id);

        $form = $this->gform->set_form(AppForms::form_promotion(), $data['record']);
        $form['form_name'] = 'pv_edit_promotion';
        $data['form_edit'] = $this->formHtmlJq->create_form($form);
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

        $status = Promotion::status($input);

        if ($status['status']) {
            $promotion = Promotion::find($id);
            $promotion->update($status['input']);
            if ($input['level_of_promotions'] == 'Category') {
                $this->item->insert_items_under_promotion($promotion, $input['category'], 'category');
            }
            if ($input['level_of_promotions'] == 'Brand') {
                $this->item->insert_items_under_promotion($promotion, $input['brand'], 'brand');
            }
            return Redirect::route('promotions.index');
        }

        return Redirect::route('promotions.edit', $id)
                        ->withInput()
                        ->withErrors($status['custom_validation'])
                        ->with('message', 'Validation error');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        Promotion::find($id)->delete();
        exit();
        //return Redirect::route('promotions.index');
    }

    function update_promotion_status($promotion_id, $status) {
        Promotion::update_promotion_status($promotion_id, $status);
        exit(0);
    }

    function submit_promotion_multiple(Request $request) {
        
    }

}
