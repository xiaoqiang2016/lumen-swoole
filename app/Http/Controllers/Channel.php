<?php

namespace App\Http\Controllers;
use App\Services\Interfaces as ServicesInterfaces;
class Channel extends Controller
{
    private $response;
    private $channelService;
    public function __construct(ServicesInterfaces\Channel $ServicesChannel)
    {
        $this->channel = $ServicesChannel;
    }
    public function getCampaignList(Array $params){
        $param = ['channel_id'=>$params['channel_id'],'account_id'=>$params['account_id']];
        #$param['refresh'] = 1;
        $result = $this->channel->getCampaignList($param);
        return $this->result($result);
    }
}
