<?php

namespace App\Http\Home\Controllers;
use Illuminate\Http\Request;
use App\Services as Services;
class AdAccount extends Controller
{
    private $channelService;
    public function __construct(Services\Channel $ServicesChannel)
    {
        $this->channelService = $ServicesChannel;
    }
    public function getAdAccountList(){
        $this->channelService->syncAllByUser($this->getLoginUser());
    }
    public function getCampaignList(Request $request){
        $params = $request->all();
        $channel_id = $params['channel_id'];
        $user = $this->getLoginUser();
        $client_id = 1;
        #$result = $this->channelService->adAccount()->getListByClient($client);
        #return $this->result($result);
    }
}
