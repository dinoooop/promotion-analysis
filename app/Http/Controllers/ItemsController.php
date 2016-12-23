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

        $query = Item::orderBy('id', 'asc');

        if (isset($input['cvids'])) {
            $multiple = Multiple::findOrFail($input['cvids']);
            $query->whereBetween('id', [$multiple->start_id, $multiple->end_id]);
        }

        if (isset($input['pid'])) {
            $data['promotion'] = Promotion::findOrFail($input['pid']);
            $query->where('promotions_id', $input['pid']);
            if ($data['promotion']->status == 'processing') {
                return View::make('admin.promotions.editnotallow', $data);
            }
        }

        if (!isset($input['cvids']) && !isset($input['pid'])) {
            return Response::make(View::make('errors.404', ['page_404' => true]), 404);
        }

        // Hide step view on edit mode 
        if (isset($input['hsv']) && $input['hsv'] == 1) {
            $data['item_edit_mode_view'] = true;
        } else {
            $data['item_edit_mode_view'] = false;
        }

        $data['records'] = $query->paginate(50);

        $data['display_message_items'] = (in_array($data['promotion']->level_of_promotions, ['Brand', 'Category'])) && ($data['records']->count() == 0);

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
        
        if (isset($input['action']) && $input['action'] == 'pv_create_item_tbform') {

            $promotion = Promotion::findOrFail($input['promotions_id']);
            
            if ($promotion->status == 'processing') {
                return Redirect::route('items.index', ['pid' => $input['promotions_id'], 'hsv' => 1])
                                ->withInput()
                                ->withErrors(['Calculation running mode, not permitted to edit.'])
                                ->with('message', 'Validation error');
            }

            // tbform form
            if (isset($input['new'])) {
                $records = $this->item->tabular_form_interpreter($input['new']);

                foreach ($records as $value) {

                    $value['promotions_id'] = $input['promotions_id'];

                    $value = $this->item->generate_item($value);
                    if (empty($value)) {
                        continue;
                    }

                    $status = Item::status($value);

                    if ($status['status']) {
                        Item::create($status['input']);
                    }
                }
            }
            if (isset($input['exist'])) {
                $records = $this->item->tabular_form_interpreter($input['exist']);

                foreach ($records as $key => $value) {

                    $value['promotions_id'] = $input['promotions_id'];

                    $value = $this->item->generate_item($value);

                    if (empty($value)) {
                        continue;
                    }

                    $status = Item::status($value, true);
                    if ($status['status']) {
                        $record = Item::find($key);
                        $record->update($status['input']);
                    } else {
                        Log::info("Error in item update");
                        Log::info($status['validation']->errors());
                    }
                }
            }

            if (isset($input['item_edit_mode_view'])) {
                // Items edited on save mode - keep status same
                return Redirect()->route('promotions.index');
            } elseif (isset($input['re_run'])) {
                Promotion::update_promotion_status($input['promotions_id'], 'active');
                return Redirect()->route('promotions.index');
            }

            // Step by step flow
            return Redirect()->route('prepare_promotion', ['pid' => $input['promotions_id']]);
        } else {
            $input = $this->item->generate_item($input);
            $status = Item::status($input);

            if ($status['status']) {
                Item::create($status['input']);
                return Redirect::route('items.index', ['pid' => $input['promotions_id']]);
            }

            return Redirect::route('items.create', ['pid' => $input['promotions_id']])
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

        $status = Item::status($input);

        if ($status['status']) {
            $record = Item::find($id);
            $record->update($status['input']);
            return Redirect::route('items.index', ['pid' => $input['promotions_id']]);
        }

        return Redirect::route('items.edit', [$id, 'pid' => $input['promotions_id']])
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
        Item::find($id)->delete();
        exit();
        //return Redirect::route('items.index');
    }

}
