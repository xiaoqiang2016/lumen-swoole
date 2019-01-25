<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Services\Channel;
class Test extends Controller
{

    //
    public function test(Request $request, Channel $channel){
        $channel->test();
        echo 'test controller';
    }
}
