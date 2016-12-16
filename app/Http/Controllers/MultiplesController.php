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
use App\Import;
use App\Temp;
use App\Dot;
use App\Multiple;
use App\promotions\Promotion;
use App\promotions\Item;

class MultiplesController extends Controller {

    private $gform;
    private $formHtmlJq;
    private $multiple;
    private $import;

    public function __construct() {
        $this->gform = new Gform;
        $this->formHtmlJq = new FormHtmlJq;
        $this->multiple = new Multiple;
        $this->merge = new Merge;
        $this->import = new Import;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {

        $data = array();

        $data['records'] = Multiple::orderBy('id', 'desc')
                ->paginate(50);

        return View::make('admin.multiples.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {

        $data = [];

        $form = $this->gform->set_form(AppForms::form_multiple());
        $form['form_name'] = 'pv_create_multiple';

        $data['form_create'] = $this->formHtmlJq->create_form($form);

        return View::make('admin.multiples.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {


        $store_info = Dot::save_as_csv($request, 'multiple_promotion_csv');

        if (!$store_info['status']) {
            return Redirect::route('multiples.create')
                            ->withInput()
                            ->withErrors($store_info['message'])
                            ->with('message', 'Validation error');
        }

        $input['title'] = $store_info['title'];
        $input['file'] = $store_info['file_name'];
        $input['type'] = $request->type;
        
        print_r($input);

        $info = $this->import->inject($input['file'], $input['type']);

        if (!empty($info)) {
            $input['start_id'] = $info[0];
            $input['end_id'] = end($info);
        } else {
            Log::info("Import failed file not matching or no valid records");
        }

        $status = Multiple::status($input);
        if ($status['status']) {
            Multiple::create($input);
            return Redirect::route('multiples.index');
        } else {
            return Redirect::route('multiples.create')
                            ->withInput()
                            ->withErrors($status['validation'])
                            ->with('message', 'Validation error');
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
        $data['record'] = Multiple::find($id);
        return View::make('admin.multiples.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        $model = Multiple::find($id);

        if (!$model) {
            exit();
        }

        if ($model->type == 'promotions') {
            Promotion::whereBetween('id', [$model->start_id, $model->end_id])->delete();
            Item::whereBetween('promotions_id', [$model->start_id, $model->end_id])->delete();
        } else {
            Item::whereBetween('id', [$model->start_id, $model->end_id])->delete();
        }

        $file_path = $this->import->get_csv_file_path($model->file);
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        $model->delete();
        exit();
    }

}
