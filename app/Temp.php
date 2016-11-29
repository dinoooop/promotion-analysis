<?php

namespace App;

class Temp {

    function __construct() {
        
    }

    public static function button_update_promotion_status($promotion) {

        if ($promotion->status == 'active') {
            $button_name = "Stop Promotion";
            $status = "sleep";
        } else {
            $button_name = "Start Promotion";
            $status = "active";
        }
        ob_start();
        ?>
        <button data-pid="<?php echo $promotion->id; ?>" data-status="<?php echo $status; ?>" class="btn btn-primary ajax-promotion-status"><?php echo $button_name; ?></button>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
    
    public static function csv_session_title($record) {
        
        if($record->type == 'promotions'){
            $url = route('promotions.index', ['cvids' => $record->id]);
        }else{
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

}
