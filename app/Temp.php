<?php

namespace App;

class Temp {

    function __construct() {
        
    }

    public static function button_result($promotion) {
        if ($promotion->status != 'completed') {
            return false;
        }
        ob_start();
        ?><a href="<?php echo route('results.index', array('pid' => $promotion->id)); ?>" class="btn btn-info"><i class="fa fa-pie-chart" aria-hidden="true"></i></a><?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public static function button_update_promotion_status($promotion) {

        if ($promotion->status == 'active') {
            $button_name = "Stop Processing";
            $status = "sleep";
        } elseif ($promotion->status == 'sleep') {
            $button_name = "Prepare Promotion Result";
            $status = "active";
        } elseif ($promotion->status == 'completed') {
            $button_name = "Restart Promotion";
            $status = "active";
        }
        ob_start();
        ?><button type="button" data-pid="<?php echo $promotion->id; ?>" data-status="<?php echo $status; ?>" class="btn btn-danger ajax-promotion-status"><?php echo $button_name; ?></button><?php
            $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }

        public static function csv_session_title($record) {

            $url = route('multiples.create', ['csvid' => $record->id]);
            ob_start();
            ?>
        <a href="<?php echo $url; ?>"><?php echo $record->title; ?></a>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public static function dynamic_table_form($id) {
        $promotions_budget_type = Stock::get('promotions_budget_type');
        ob_start();
        ?>
        <tr>
            <td><input type="text" name="new[<?php echo $id; ?>][0]" value="" class="form-control auto-complete" data-coll="material_id"></td>
            <td><input type="text" name="new[<?php echo $id; ?>][1]" value="" class="form-control auto-complete" data-coll="asin"></td>
            <td><input type="text" name="new[<?php echo $id; ?>][2]" value="" class="form-control date-picker-tool"></td>
            <td><input type="text" name="new[<?php echo $id; ?>][3]" value="" class="form-control date-picker-tool"></td>
            <td><input type="text" name="new[<?php echo $id; ?>][4]" value="" class="form-control"></td>
            <td><input type="text" name="new[<?php echo $id; ?>][5]" value="" class="form-control"></td>
            <td><input type="text" name="new[<?php echo $id; ?>][6]" value="" class="form-control"></td>
            <td><select name="new[<?php echo $id; ?>][7]" class="form-control">
                    <?php foreach ($promotions_budget_type as $key => $value): ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select></td>
            <td><input type="text" name="new[<?php echo $id; ?>][8]" value="" class="form-control"></td>
            <td><input type="text" name="new[<?php echo $id; ?>][9]" value="" class="form-control"></td>
            <td><input type="text" name="new[<?php echo $id; ?>][10]" value="" class="form-control"></td>
            <td><button class="btn btn-danger remove-item-row"><i class="fa fa-trash"></i></button></td>
        </tr>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public static function dynamic_table_form_exist($record) {
        $promotions_budget_type = Stock::get('promotions_budget_type');
        ob_start();
        ?>
        <tr>
            <td><input type="text" name="exist[<?php echo $record->id; ?>][0]" value="<?php echo $record->material_id ?>" class="form-control auto-complete" data-coll="material_id"></td>
            <td><input type="text" name="exist[<?php echo $record->id; ?>][1]" value="<?php echo $record->asin ?>" class="form-control auto-complete" data-coll="asin"></td>
            <td><input type="text" name="exist[<?php echo $record->id; ?>][2]" value="<?php echo $record->promotions_startdate ?>" class="form-control date-picker-tool"></td>
            <td><input type="text" name="exist[<?php echo $record->id; ?>][3]" value="<?php echo $record->promotions_enddate ?>" class="form-control date-picker-tool"></td>
            <td><input type="text" name="exist[<?php echo $record->id; ?>][4]" value="<?php echo $record->promotions_budget ?>" class="form-control"></td>
            <td><input type="text" name="exist[<?php echo $record->id; ?>][5]" value="<?php echo $record->promotions_projected_sales ?>" class="form-control"></td>
            <td><input type="text" name="exist[<?php echo $record->id; ?>][6]" value="<?php echo $record->promotions_expected_lift ?>" class="form-control"></td>
            <td>
                <select name="exist[<?php echo $record->id; ?>][7]" class="form-control">
                    <?php foreach ($promotions_budget_type as $key => $value): ?>
                        <?php $selected = ($value == $record->promotions_budget_type) ? 'selected' : ''; ?>
                        <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="text" name="exist[<?php echo $record->id; ?>][8]" value="<?php echo $record->funding_per_unit ?>" class="form-control"></td>
            <td><input type="text" name="exist[<?php echo $record->id; ?>][9]" value="<?php echo $record->forecasted_qty ?>" class="form-control"></td>
            <td><input type="text" name="exist[<?php echo $record->id; ?>][10]" value="<?php echo $record->forecasted_unit_sales ?>" class="form-control"></td>
            <td><a class="btn btn-danger row-delete-no-alert" href="<?php echo route('items.destroy', array($record->id))?>" data-modal_id="<?php echo $record->id?>"><i class="fa fa-trash"></i></a></td>
        </tr>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public static function step_progress($active_step) {
        $steps_status = ['complete', 'active', 'disabled'];
        $steps = ['step_1', 'step_2', 'step_3', 'step_4'];

        $set_steps = [];

        foreach ($steps as $value) {
            if ($value == $active_step) {
                $set_steps[$value] = 'active';
            } elseif (in_array('active', $set_steps)) {
                $set_steps[$value] = 'disabled';
            } else {
                $set_steps[$value] = 'complete';
            }
        }


        ob_start();
        ?>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <!-- start progress -->
                <div class="row bs-wizard" style="border-bottom:0;">
                    <div class="col-xs-3 bs-wizard-step <?php echo $set_steps['step_1']; ?>">
                        <div class="text-center bs-wizard-stepnum">Step 1</div>
                        <div class="progress"><div class="progress-bar"></div></div>
                        <a href="#" class="bs-wizard-dot"></a>
                        <div class="bs-wizard-info text-center">Create promotion</div>
                    </div>

                    <div class="col-xs-3 bs-wizard-step <?php echo $set_steps['step_2']; ?>">
                        <div class="text-center bs-wizard-stepnum">Step 2</div>
                        <div class="progress"><div class="progress-bar"></div></div>
                        <a href="#" class="bs-wizard-dot"></a>
                        <div class="bs-wizard-info text-center">Add promoted items</div>
                    </div>

                    <div class="col-xs-3 bs-wizard-step <?php echo $set_steps['step_3']; ?>">
                        <div class="text-center bs-wizard-stepnum">Step 3</div>
                        <div class="progress"><div class="progress-bar"></div></div>
                        <a href="#" class="bs-wizard-dot"></a>
                        <div class="bs-wizard-info text-center">Prepare promotions results</div>
                    </div>

                    <div class="col-xs-3 bs-wizard-step <?php echo $set_steps['step_4']; ?>">
                        <div class="text-center bs-wizard-stepnum">Step 4</div>
                        <div class="progress"><div class="progress-bar"></div></div>
                        <a href="#" class="bs-wizard-dot"></a>
                        <div class="bs-wizard-info text-center"> Promotions results</div>
                    </div>
                </div>
                <!-- end progress  -->
            </div>
        </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

}
