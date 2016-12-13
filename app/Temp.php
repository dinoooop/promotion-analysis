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

            if ($record->type == 'promotions') {
                $url = route('promotions.index', ['cvids' => $record->id]);
            } else {
                $url = route('items.index', ['cvids' => $record->id]);
            }
            ob_start();
            ?>
        <a href="<?php echo $url; ?>"><?php echo $record->title; ?></a>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public static function dynamic_table_form($id) {
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
            <td><input type="text" name="new[<?php echo $id; ?>][7]" value="" class="form-control"></td>
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

}
