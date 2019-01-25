<?php

namespace App\Http\Services;
use Swoole;
use App\Repositories\Interfaces as Interfaces;
class Channel{
    public function __construct(Interfaces\Channel $ServicesChannel)
    {
        $this->channelService = $ServicesChannel;
    }
    public function getCompaigns($params){

        $this->channelService->find('Facebook')->getCampaigns('2119471031652037');
    }
}