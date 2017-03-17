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

        $input = Input::get();

        $data = [];

        if (isset($input['pid'])) {

            // Import items based on single promotion

            $data['pid'] = $input['pid'];
            $data['disable_item_input'] = false;

            $form = $this->gform->set_form(AppForms::form_multiple_items($data));
            $form['fields']['type']['value'] = 'pid_items';
            $form['form_name'] = 'pv_create_multiple';
            $data['form_multiple_items'] = $this->formHtmlJq->create_form($form);

            return View::make('admin.multiples.create_item', $data);
        }

        if (isset($input['csvid'])) {
            // Promotion have imported
            $multiple = Multiple::findOrFail($input['csvid']);
            $data['promotions'] = Promotion::whereBetween('id', [$multiple->start_id, $multiple->end_id])->get();
            $data['items'] = Item::get_items_range($multiple->start_id, $multiple->end_id);

            $data['disable_item_input'] = false;
            $data['csvid'] = $input['csvid'];
            $data['href_prepare_result'] = route('prepare_promotion', ['csvid' => $input['csvid']]);
        } else {
            // Promotion not imported 
            $data['disable_item_input'] = true;
        }
        
        

        // Create form that import promotions
        $form = $this->gform->set_form(AppForms::form_multiple_promotion());
        $form['form_name'] = 'pv_create_multiple';
        $data['form_multiple_promotion'] = $this->formHtmlJq->create_form($form);

        // Create form that import items
        $form = $this->gform->set_form(AppForms::form_multiple_items($data));
        $form['form_name'] = 'pv_create_multiple';
        $data['form_multiple_items'] = $this->formHtmlJq->create_form($form);

        return View::make('admin.multiples.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {

        $store_info = Dot::save_attachment($request, 'multiple_promotions');

        if (!$store_info['status']) {
            return Redirect::route('multiples.create')
                            ->withInput()
                            ->withErrors($store_info['message'])
                            ->with('message', 'Validation error');
        }

        $input['title'] = $store_info['title'];
        $pathinfo_attachment = pathinfo($store_info['file_path']);

        if ($pathinfo_attachment['extension'] != 'csv') {
            $csv_file = $this->import->get_csv_file_path($store_info['file_path']);
            Log::info("Here is csv");
            Log::info($csv_file);
            $pathinfo_csv = pathinfo($csv_file);
        } else {
            $csv_file = $store_info['file_path'];
            $pathinfo_csv = pathinfo($store_info['file_path']);
        }

        $input['file'] = $pathinfo_csv['basename'];
        $input['type'] = $request->type;
        $input['csv_file'] = $csv_file;
        $input['pid'] = $request->pid;

        $info = $this->import->inject($input);

        if (!empty($info)) {
            $input['start_id'] = $info[0];
            $input['end_id'] = end($info);
        } else {
            Log::info("Import failed file not matching or no valid records");
        }

        $status = Multiple::status($input);
        if ($status['status']) {
            $multiple = Multiple::create($input);
            if ($request->type == "Items") {
                //append item csv id
                return Redirect::route('multiples.create', ['csvid' => $request->csvid]);
            } elseif ($request->type == "pid_items") {
                return Redirect::route('items.index', ['pid' => $request->pid]);
            } else {
                return Redirect::route('multiples.create', ['csvid' => $multiple->id]);
            }
        } else {

            if (isset($request->csvid)) {
                return Redirect::route('multiples.create', ['csvid' => $request->csvid])
                                ->withInput()
                                ->withErrors($status['validation'])
                                ->with('message', 'Validation error');
            } elseif ($request->type == "pid_items") {
                return Redirect::route('multiples.create', ['pid' => $request->pid])
                                ->withInput()
                                ->withErrors($status['validation'])
                                ->with('message', 'Validation error');
            } else {
                return Redirect::route('multiples.create')
                                ->withInput()
                                ->withErrors($status['validation'])
                                ->with('message', 'Validation error');
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

        if (strtolower($model->type) == 'promotions') {
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
