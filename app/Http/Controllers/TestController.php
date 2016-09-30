<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Dot;

class TestController extends BaseController {

    

    public function __construct() {
        
    }

    function test() {
        
        echo csrf_token();
        
    }

}
