<?php

namespace App\Services;
use Swoole;
use App\Models;
class Channel{
    public $ids = [];
	public function getInstance($ids=[]){
	    $result = [];
	    if($ids == []) $ids = $this->ids;
		$data = [
			1 => 'Facebook',
			3 => 'Google',
		];
		if($ids){
		    foreach($data as $k=>$v) if(in_array($k,$ids)) $result[$k] = $v;
        }else{
		    $result = $data;
        }
        $channels = [];
        if($result) foreach($result as $k=>$v){
            $channels[$k] = app()->make("App\\Channels\\{$v}\\Main");
        }
		return $channels;
	}
	#更新用户全部数据
	public function syncAllByUser(Models\User $user){
	    $user = Models\User::find(1006631);
        #\App\Common\Helper::runTime();
	    #$this->syncAdAccountByUser($user);#0.2
        #\App\Common\Helper::runTime();
	    #return;
        #$this->syncAdCampaignByUser($user);#8s 24268

        #$this->syncAdSetByUser($user);#12s 25k

        #$this->syncAdAdByUser($user);#8s 28k
        $this->AdDiagnoseByUser();
        \App\Common\Helper::runTime();
        return;
        $this->syncAdAdInsightsByUser($user);
    }
    public function AdDiagnoseByUser(Models\User $user){
        $channels = $this->getInstance([1]);
        if($channels) foreach($channels as $channel){
            $channel->adDiagnoseByUser($user);
        }
    }
    #
    public function syncAdAccountByUser(Models\User $user){

	    $clients = $user->getClients();
        if($clients) foreach($clients as $client){
            #$this->syncLocalAdAccountByClient($client);
        }
        #同步远端账号
        $this->syncRemoteAdAccountByUser($user);
    }
    #同步广告系列
    public function syncAdCampaignByUser(Models\User $user){
        $channels = $this->getInstance([1]);
        if($channels) foreach($channels as $channel){
            $channel->syncAdCampaignByUser($user);
        }
    }
    #同步广告组
    public function syncAdSetByUser(Models\User $user){
        $channels = $this->getInstance([1]);
        if($channels) foreach($channels as $channel){
            $channel->syncAdSetByUser($user);
        }
    }
    #同步广告Ad
    public function syncAdAdByUser(Models\User $user){
        $channels = $this->getInstance([1]);
        if($channels) foreach($channels as $channel){
            $channel->syncAdAdByUser($user);
        }
    }
    #同步广告消耗
    public function syncAdAdInsightsByUser(Models\User $user){
        $channels = $this->getInstance([1]);
        if($channels) foreach($channels as $channel){
            $channel->syncAdAdInsightsByUser($user);
        }
    }
    #同步本地client广告账号
    public function syncLocalAdAccountByClient(Models\Client $client){

        Models\AdAccount::syncFromStorageByClient($client);
    }
    #
    #同步远程账号
	public function syncRemoteAdAccountByUser(Models\User $user){
        $channels = $this->getInstance([1]);
        if($channels) foreach($channels as $channel){
            $channel->syncAdAccountByUser($user);
        }
	}
    public function login(){
        echo 'login';
    }
    public function test(){
        echo 'test';
    }
    
}