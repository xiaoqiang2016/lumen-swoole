<?php

namespace App\Http\Controllers;

class Test extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    //
    public function test(array $params){
        print_r($params);
        echo 'test controller';
    }
}
