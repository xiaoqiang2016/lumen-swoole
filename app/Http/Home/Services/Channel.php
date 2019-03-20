<?php

namespace App\Http\Home\Services;
use Swoole;
use App\Repositories\Interfaces as Interfaces;
class Channel{
    private $channel = [
        1 => ['name' => 'Facebook'],
        3 => ['name' => 'Google'],
    ];
    public function __construct()
    {
    }
    public function test(){
        echo 123;
    }
    public function find($channel_id){
    }
    /*
     * 根据ClientID获取广告账户列表
     */ 
    public function getAdAccountListByClient($client_id){

        #$this->channelService->find('Facebook')->getCampaigns('2119471031652037');
    }
}