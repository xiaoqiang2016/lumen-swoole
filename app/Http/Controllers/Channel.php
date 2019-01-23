<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Services\ChannelService;
class Channel extends Controller
{
    private $response;
    private $channelService;
    public function __construct(ChannelService $channelService)
    {
        $this->channelService = $channelService;
    }

    public function getCompaigns(Request $request){
        echo 123;
        exit;
        $this->channelService->test();
        return $this->result();
    }
}
