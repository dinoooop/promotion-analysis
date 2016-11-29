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


        $allow_extension = ['csv', 'txt'];
        $errors = [];

        if ($request->hasFile('multiple_promotion_csv')) {
            $title = pathinfo($request->file('multiple_promotion_csv')->getClientOriginalName())['filename'];
            $extention = $request->multiple_promotion_csv->extension();
            Log::info("Extention is");
            Log::info($extention);

            if (!in_array($extention, $allow_extension)) {
                Log::info("Ext not exist");
                $errors[] = "Please upload valid file with data";
            }
        } else {
            $errors[] = "File doesn't exist";
        }

        if (empty($errors)) {
            $file_name = date('Y-m-d-h-i-s') . rand(1000, 9999) . '.csv';
            $path = $request->multiple_promotion_csv->storeAs('csv', $file_name);
            $path = storage_path('app/' . $path);
            $info = $this->merge->import_csv($path, $request->type);
            Log::info("No errors");
            if (!empty($info)) {
                $input = [
                    'title' => $title,
                    'description' => '',
                    'file' => $file_name,
                    'type' => $request->type,
                    'start_id' => $info[0],
                    'end_id' => end($info),
                ];

                $validation = Validator::make($input, Multiple::$form_create_rules);

                if ($validation->passes()) {
                    Log::info("Validation passed");

                    Multiple::create($input);

                    return Redirect::route('multiples.index');
                } else {
                    Log::info("Validation failed");
                }
            } else {
                $errors[] = "Import failed: No valid record found in the file";
            }
        }



        return Redirect::route('multiples.create')
                        ->withInput()
                        ->withErrors($errors)
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

        $data = array();

        $input = Input::get();

        if (!isset($input['pid'])) {
            return Response::make(View::make('errors.404', ['page_404' => true]), 404);
        }

        $data['promotion'] = Promotion::find($input['pid']);

        $data['record'] = Multiple::find($id);

        $form = $this->gform->set_form(AppForms::form_multiple(), $data['record']);
        $form['form_name'] = 'pv_edit_multiple';
        $data['form_edit'] = $this->formHtmlJq->create_form($form);
        return View::make('admin.multiples.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {

        $input = Input::all();
        $input = Multiple::sanitize($input);

        $validation = Validator::make($input, Multiple::$form_edit_rules);

        if ($validation->passes()) {

            $record = Multiple::find($id);
            $record->update($input);
            return Redirect::route('multiples.index', ['pid' => $input['promotions_id']]);
        }

        return Redirect::route('multiples.edit', [$id, 'pid' => $input['promotions_id']])
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
        $model = Multiple::find($id);

        if ($model->type == 'promotions') {
            Promotion::whereBetween('id', [$model->start_id, $model->end_id])->delete();
            unlink(storage_path('app/csv/' . $model->file));
        } else {
            Item::whereBetween('id', [$model->start_id, $model->end_id])->delete();
            unlink(storage_path('app/csv/' . $model->file));
        }


        $model->delete();
        exit();
    }

}
