<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Services as Services;
use App\Repositories\Interfaces as Interfaces;
class Channel extends Controller
{
    private $response;
    private $channelService;
    public function __construct(Interfaces\Channel $ServicesChannel)
    {
        $this->channel = $ServicesChannel;
    }
    public function getCampaigns(Array $params){
        $channel_id = $params['channel_id'];
        $result = $this->channel->find($channel_id)->getCampaigns($params);
        return $this->result($result);
    }
}
