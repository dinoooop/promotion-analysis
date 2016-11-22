<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\Gform;
use App\FormHtmlJq;
use App\AppForms;
use App\Merge;
use App\promotions\Item;
use App\promotions\Promotion;

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

        if (!isset($input['pid'])) {
            return Response::make(View::make('errors.404', ['page_404' => true]), 404);
        }
        
        $data['records'] = Item::where('promotions_id', $input['pid'])
                ->orderBy('id', 'desc')
                ->paginate(50);

        $data['promotion'] = Promotion::find($input['pid']);

        return View::make('admin.items.index', $data);
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

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        $input = Input::all();
        
        if (!isset($input['promotions_id'])) {
            return Response::make(View::make('errors.404', ['page_404' => true]), 404);
        }
        
        

        $input = Item::sanitize($input);

        $validation = Validator::make($input, Item::$form_create_rules);

        if ($validation->passes()) {

            Item::create($input);

            // return Redirect::route('items.index');
            // return redirect('admin/items?pid=' . $input['pid']);
            // return redirect($this->merge->url('items_index', $input));
            return Redirect::route('items.index', ['pid' => $input['promotions_id']]);
        }

        return Redirect::route('items.create', ['pid' => $input['promotions_id']])
                        ->withInput()
                        ->withErrors($validation)
                        ->with('message', 'There were validation errors.');
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
            return Response::make(View::make('errors.404', ['page_404' => true]), 404);
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
        $input = Item::sanitize($input);

        $validation = Validator::make($input, Item::$form_edit_rules);

        if ($validation->passes()) {

            $record = Item::find($id);
            $record->update($input);
            return Redirect::route('items.index', ['pid' => $input['promotions_id']]);
        }

        return Redirect::route('items.edit', [$id, 'pid' => $input['promotions_id']])
                        ->withInput()
                        ->withErrors($validation)
                        ->with('message', 'There were validation errors.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        Item::find($id)->delete();
        exit();
        //return Redirect::route('items.index');
    }

}
