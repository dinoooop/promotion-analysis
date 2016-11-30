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
use App\Temp;
use App\Multiple;
use App\promotions\Promotion;
use App\promotions\Item;

class MultiplesController extends Controller {

    private $gform;
    private $formHtmlJq;
    private $multiple;
    private $merge;

    public function __construct() {
        $this->gform = new Gform;
        $this->formHtmlJq = new FormHtmlJq;
        $this->multiple = new Multiple;
        $this->merge = new Merge;
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

        $status = Multiple::status($request);

        if ($status['status']) {
            $input = $status['input'];
            Log::info($input);
            $info = $this->merge->import_csv($input['file'], $input['type']);
            if (!empty($info)) {
                $input['start_id'] = $info[0];
                $input['end_id'] = end($info);
                Multiple::create($input);
                return Redirect::route('multiples.index');
            } else {
                $status['custom_validation']['message'][] = 'Import failed: No valid record found in the file';
            }
        }

        return Redirect::route('multiples.create')
                        ->withInput()
                        ->withErrors($status['custom_validation'])
                        ->with('message', 'Validation error');
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

        if ($model->type == 'promotions') {
            Promotion::whereBetween('id', [$model->start_id, $model->end_id])->delete();
            unlink($this->merge->get_csv_file_path($model->file));
        } else {
            Item::whereBetween('id', [$model->start_id, $model->end_id])->delete();
            unlink($this->merge->get_csv_file_path($model->file));
        }


        $model->delete();
        exit();
    }

}
