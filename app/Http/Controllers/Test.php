<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Services\ChannelService;
class Test extends Controller
{

    //
    public function test(Request $request,ChannelService $channel){
        $channel->test();
        echo 'test controller';
    }
}
