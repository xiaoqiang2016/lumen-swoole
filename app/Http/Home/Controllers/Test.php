<?php

namespace App\Http\Home\Controllers;
use Illuminate\Http\Request;
use App\Http\Services\Channel;
class Test extends Controller
{

    //
    public function test(){
    	$params = $this->getParams();
        #$channel->test();
        //echo 'test controller';

    	 return $params;
    
    }
}
