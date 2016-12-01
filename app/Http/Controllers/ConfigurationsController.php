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
use App\promotions\Configuration;
use App\Option;
use App\Merge;
use App\Multiple;

class ConfigurationsController extends Controller {

    private $gform;
    private $formHtmlJq;
    private $posts;

    public function __construct() {
        $this->gform = new Gform;
        $this->formHtmlJq = new FormHtmlJq;
        $this->posts = new Configuration;
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

        $query = Configuration::orderBy('id', 'desc');
        $data['records'] = $query->paginate(50);
        
        return View::make('admin.configurations.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {

        $data = array();
        $form = $this->gform->set_form(AppForms::form_configuration());
        $form['form_name'] = 'pv_create_configuration';

        $data['form_create'] = $this->formHtmlJq->create_form($form);

        return View::make('admin.configurations.create', $data);
    }

    /**
     * 
     * Store a newly created resource in storage.
     * @return Response
     */
    public function store() {
        $input = Input::all();

        $status = Configuration::status($input);

        if ($status['status']) {
            Configuration::create($status['input']);
            return Redirect::route('configurations.index');
        }

        return Redirect::route('configurations.create')
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
        $data['record'] = Configuration::find($id);
        return View::make('admin.configurations.show', $data);
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
        $data['record'] = Configuration::find($id);

        $form = $this->gform->set_form(AppForms::form_configuration(), $data['record']);
        $form['form_name'] = 'pv_edit_configuration';
        $data['form_edit'] = $this->formHtmlJq->create_form($form);
        return View::make('admin.configurations.edit', $data);
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

        $status = Configuration::status($input);

        if ($status['status']) {
            $record = Configuration::find($id);
            $record->update($status['input']);
            return Redirect::route('configurations.index');
        }

        return Redirect::route('configurations.edit', $id)
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
        Configuration::find($id)->delete();
        exit();
        //return Redirect::route('configurations.index');
    }


}
