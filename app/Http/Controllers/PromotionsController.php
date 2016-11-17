<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

use App\Gform;
use App\FormHtmlJq;
use App\AppForms;
use App\promotions\Promotion;

use App\Dot;
use App\Sdcalc;
use App\Swcalc;
use App\Merge;
use App\RawData;
use App\Calendar;
use App\Printm;
use App\Redshift\Dmaterial;
use App\Redshift\Dsales;
use App\Redshift\Dchannel;
use Illuminate\Support\Facades\DB;


class PromotionsController extends Controller {

    private $gform;
    private $formHtmlJq;
    private $posts;

    public function __construct() {
        $this->gform = new Gform;
        $this->formHtmlJq = new FormHtmlJq;
        $this->posts = new Promotion;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $data = array();
        $data['records'] = Promotion::orderBy('id', 'desc')->paginate(50);
        return View::make('admin.promotions.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {

        $data = array();

        $form = $this->gform->set_form(AppForms::get_form('pv_form_promotion'));
        $form['form_name'] = 'pv_create_promotion';

        $data['form_create'] = $this->formHtmlJq->create_form($form);

        return View::make('admin.promotions.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        $input = Input::all();
        
        $input = Promotion::sanitize($input);

        $validation = Validator::make($input, Promotion::$form_create_rules);

        if ($validation->passes()) {

            Promotion::create($input);

            return Redirect::route('promotions.index');
        }

        return Redirect::route('promotions.create')
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
        $data['records'] = Promotion::find($id);
        return View::make('admin.promotions.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $data = array();
        $data['record'] = Promotion::find($id);
        
        $form = $this->gform->set_form(AppForms::get_form('pv_form_promotion'), $data['record']);
        $form['form_name'] = 'pv_edit_promotion';
        $data['form_edit'] = $this->formHtmlJq->create_form($form);
        return View::make('admin.promotions.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {

        $input = Input::all();
        $input = Promotion::sanitize($input);
        
        $validation = Validator::make($input, Promotion::$form_edit_rules);

        if ($validation->passes()) {
            
            $record = Promotion::find($id);
            $record->update($input);
            return Redirect::route('promotions.index');
        }

        return Redirect::route('promotions.edit', $id)
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
        Promotion::find($id)->delete();
        exit();
        //return Redirect::route('promotions.index');
    }

}
