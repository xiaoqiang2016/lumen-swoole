<?php

namespace App\Services;
use App\Repositories\Interfaces as RepositoriesInterfaces;
class Channel implements Interfaces\Channel {
    private $channel;
    public function __construct(RepositoriesInterfaces\Channel $RepositoriesChannel)
    {
        $this->channel = $RepositoriesChannel;
    }

    public function getCampaignList(Array $params){
        $channel_id = $params['channel_id'];
        $account_id = $params['account_id'];
        $channel = $this->channel->find($channel_id);

        $refresh = @$params['refresh'] ?: 0;
        if(!$refresh){
            $result = $channel->getCampaignList(['account_id'=>$account_id]);
            print_r($result);
            if($result){
               return $result;
            }else{
                $refresh = 1;
            }
        }
        if($refresh){
            $pullData = $channel->pullCampaignList(['account_id'=>$account_id]);
            $channel->saveCampaignList($pullData);
            #print_r($pullData);
            return;
        }
    }
}