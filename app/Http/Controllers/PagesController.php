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
use App\Calendar;

class PagesController extends Controller {

    private $gform;
    private $formHtmlJq;
    private $posts;
    private $item;

    function __construct() {
        $this->gform = new Gform;
        $this->formHtmlJq = new FormHtmlJq;
        $this->posts = new Promotion;
        $this->merge = new Merge;
        $this->item = new Item;
        $this->calendar = new Calendar;
    }

    /**
     * 
     * Show step 3
     * @param int $id Promotion id
     * @return html page
     */
    function prepare_result(Request $request) {

        $data = [];

        if ($request->csvid) {
            $multiple = Multiple::findOrFail($request->csvid);
            Promotion::whereBetween('id', [$multiple->start_id, $multiple->end_id])
                    ->update(['status' => 'active']);
            return View::make('admin.others.prepare_promotion_multiple');
        }

        $data['promotion'] = Promotion::findOrFail($request->pid);

        Promotion::update_promotion_status($data['promotion']->id, 'active');

        if ($data['promotion']['level_of_promotions'] == 'Category') {
            $data['message_level_of_promotions'] = "Since it is a category level of promotion you don't need to fill up STEP 2.";
        } elseif ($data['promotion']['level_of_promotions'] == 'Brand') {
            $data['message_level_of_promotions'] = "Since it is a brand level of promotion you don't need to fill up STEP 2.";
        } else {
            $data['message_level_of_promotions'] = '';
        }

        if (!$this->calendar->is_avail_post_week($data['promotion'])) {
            $data['message_start_time'] = "Its look like a future promotion since start date is {$data['promotion']['promotions_startdate']} (post weeks not available). \n";
            $data['message_start_time'] .= "Once the calculation get completed you will be notified by an email. \n";
        } else {
            $data['message_start_time'] = "The promotion is now under processing. \n";
            $data['message_start_time'] .= "Once the calculation get completed you will be notified by an email. \n";
        }

        // Hide step view on edit mode 
        if (isset($request->hsv) && $request->hsv == 1) {
            $data['hide_step_view'] = true;
        }

        if (isset($request->rec) && $request->rec == 1) {
            return View::make('admin.others.recalculate_promotion', $data);
        } else {
            return View::make('admin.others.prepare_promotion', $data);
        }
    }

}
