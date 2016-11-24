<?php

namespace App;

class Notification {

    public $message = "";
    public $notification_types = array(
        'error' => array(
            'name' => 'Error',
            'class' => 'alert-danger',
        ),
        'success' => array(
            'name' => 'Success',
            'class' => 'alert-success',
        ),
        'warning' => array(
            'name' => 'warning',
            'class' => 'alert-warning',
        ),
    );

    function __construct() {
        
    }

    function has_notification() {

        if ($this->message != "") {
            
            return true;
        }
        
        return false;
    }

    function set_notification($message, $type) {

        $this->message = $message;

        if (isset($this->notification_types[$type])) {
            Log::info("notification have a type");
            $this->class = $this->notification_types[$type]['class'];
            $this->type = $this->notification_types[$type]['name'];
        } else {
            $this->class = $this->notification_types['error']['class'];
            $this->type = '';
        }
    }

    function display_notification() {
        if(!$this->has_notification()){
            return false;
        }
        ob_start();
        
        ?>
        <div class="alert <?php echo $this->class; ?> animated shake" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="fa fa-times-circle"></i> <strong><?php echo $this->type; ?>!</strong> <?php echo $this->message; ?>
        </div>
        <?php
        
        $contents = ob_get_contents();
        ob_end_clean();
        
        return $contents;
    }
    
    function min() {
        
        $user = User::find(291);
        echo '<pre>', print_r($user), '</pre>';
        exit();
        
    }

}
