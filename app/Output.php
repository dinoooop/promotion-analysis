<?php

namespace App;

use App\Merge;
use App\Spinput;
use App\Dot;
use App\promotions\Promotion;
use App\Redshift\Pgquery;
use App\Merge;

class Output {

    public function __construct() {
        $this->calendar = new Calendar;
        $this->merge = new Merge;
    }

    function init($promotions_id) {
        $merge = new Merge();
        $promotion = Promotion::find($promotions_id)->toArray();
        $settings = $this->merge->admin_settings($promotion);
        $this->post_weeks = $settings['post_weeks'];

        
        $query = Spod::orderBy('id', 'asc');
        $query->where('promotions_id', $input['pid']);
        $data['records'] = $query->paginate(50);

        if ($promotion['promotions_startdate'] == $promotion['promotions_enddate']) {
            $input = $this->multiple_day($input);
        } else {
            $input = $this->single_day($input);
        }
    }

    function multiple_day($input) {
        // MULTIPLE DAY PROMOTIONS ---------------------------------------------
        // Baseline (daily) 
        $input->daily_baseline_ordered_amount = $input->baseline_ordered_amount;
        $input->daily_baseline_ordered_units = round($input->baseline_ordered_units);

        // Baseline (wkly) 
        $input->wkly_baseline_ordered_amount = $input->baseline_ordered_amount * 7;
        $input->wkly_baseline_ordered_units = round($input->baseline_ordered_units * 7);
        
        // During (wkly)
        $input->wkly_during_ordered_amount = $input->during_ordered_amount * 7;
        $input->wkly_during_ordered_units = round($input->during_ordered_units * 7);

        //Post (wkly)
        $input->wkly_post_ordered_amount = $input->post_ordered_amount * 7;
        $input->wkly_post_ordered_units = round($input->post_ordered_units * 7);

        //DURING_INCREMENTAL
        $input->wkly_during_incremental_ordered_amount = round($input->wkly_during_ordered_amount - $input->wkly_baseline_ordered_amount, 2);
        $input->wkly_during_incremental_ordered_units = $input->wkly_during_ordered_units - $input->wkly_baseline_ordered_units;

        //POST INCREMENTAL 
        $input->wkly_post_incremental_ordered_amount = $input->wkly_post_ordered_amount - $input->wkly_baseline_ordered_amount;
        $input->wkly_post_incremental_ordered_units = $input->wkly_post_ordered_units - $input->wkly_baseline_ordered_units;

        $input->total_during_incremental_ordered_amount = round(($input->wkly_during_ordered_amount - $input->wkly_baseline_ordered_amount) * ($input->no_of_promotion_days / 7), 2);
        $input->total_during_incremental_ordered_units = round(($input->wkly_during_ordered_units - $input->wkly_baseline_ordered_units) * ($input->no_of_promotion_days / 7));

        $input->total_post_incremental_ordered_amount = round(($input->wkly_post_ordered_amount - $input->wkly_baseline_ordered_amount) * $this->post_weeks, 2);
        $input->total_post_incremental_ordered_units = round(($input->wkly_post_ordered_units - $input->wkly_baseline_ordered_units) * $this->post_weeks);

        $input->during_lift_ordered_amount = ($input->wkly_during_ordered_amount / $input->wkly_baseline_ordered_amount) - 1;
        $input->during_lift_ordered_amount = round($input->during_lift_ordered_amount, 2);

        $input->during_lift_ordered_units = ($input->wkly_during_ordered_units / $input->wkly_baseline_ordered_units) - 1;
        $input->during_lift_ordered_units = round($input->during_lift_ordered_units, 2);

        $input->post_lift_ordered_amount = ($input->wkly_post_ordered_amount / $input->wkly_baseline_ordered_amount) - 1;
        $input->post_lift_ordered_amount = round($input->post_lift_ordered_amount, 2);

        $input->post_lift_ordered_units = ($input->wkly_post_ordered_units / $input->wkly_baseline_ordered_units) - 1;
        $input->post_lift_ordered_units = round($input->post_lift_ordered_units, 2);
    }

    function single_day($input) {
        // SINGLE DAY PROMOTION
        
        // Baseline (daily) 
        $input->daily_baseline_ordered_amount = $input->baseline_ordered_amount;
        $input->daily_baseline_ordered_units = round($input->baseline_ordered_units);

        // Baseline (wkly) 
        $input->wkly_baseline_ordered_amount = $input->baseline_ordered_amount * 7;
        $input->wkly_baseline_ordered_units = round($input->baseline_ordered_units * 7);

        // During (daily)
        $input->daily_during_ordered_amount = $input->during_ordered_amount;
        $input->daily_during_ordered_units = $input->during_ordered_units;

        //Post (wkly)
        $input->wkly_post_ordered_amount = $input->post_ordered_amount * 7;
        $input->wkly_post_ordered_units = round($input->post_ordered_units * 7);

        //DURING_INCREMENTAL
        $input->daily_during_incremental_ordered_amount = $input->daily_during_ordered_amount - $input->daily_baseline_ordered_amount;
        $input->daily_during_incremental_ordered_units = $input->daily_during_ordered_units - $input->daily_baseline_ordered_units;

        //POST INCREMENTAL 
        $input->wkly_post_incremental_ordered_amount = $input->wkly_post_ordered_amount - $input->wkly_baseline_ordered_amount;
        $input->wkly_post_incremental_ordered_units = $input->wkly_post_ordered_units - $input->wkly_baseline_ordered_units;

        $input->total_during_incremental_ordered_amount = $input->daily_during_incremental_ordered_amount;
        $input->total_during_incremental_ordered_units = $input->daily_during_incremental_ordered_units;

        $input->total_post_incremental_ordered_amount = ($input->wkly_post_ordered_amount - $input->wkly_baseline_ordered_amount) * $this->post_weeks;
        $input->total_post_incremental_ordered_units = ($input->wkly_post_ordered_units - $input->wkly_baseline_ordered_units) * $this->post_weeks;

        $input->during_lift_ordered_amount = ($input->daily_during_ordered_amount / $input->daily_baseline_ordered_amount) - 1;
        $input->during_lift_ordered_amount = round($input->during_lift_ordered_amount, 2);

        $input->during_lift_ordered_units = ($input->during_ordered_units / $input->baseline_ordered_units) - 1;
        $input->during_lift_ordered_units = round($input->during_lift_ordered_units);

        $input->post_lift_ordered_amount = ($input->wkly_post_ordered_amount / $input->wkly_baseline_ordered_amount) - 1;
        $input->post_lift_ordered_amount = round($input->post_lift_ordered_amount, 2);

        $input->post_lift_ordered_units = ($input->wkly_post_ordered_units / $input->wkly_baseline_ordered_units) - 1;
        $input->post_lift_ordered_units = round($input->post_lift_ordered_units, 2);

        return $input;
    }

}
