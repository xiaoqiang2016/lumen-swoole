<?php

namespace App\Http\Controllers;
use Swoole;
use App;
class Facebook extends Controller
{
    public $syncAll = 0; 
    public function sync($params){
        $this->syncAll = 1;
        $account_id = $params['account_id'];
        $facebook = new App\Repository\Facebook();
        $facebook->account_id = '1634320873332648';
        $facebook->getCampaigns(function($response) use ($facebook){
            $this->result($response);
        });
    }
    public function getCompaigns(){

    }
    public function syncOeRequest(){
        #\App\Services\Channels\Facebook::syncOeRequest();
        \App\Services\Channels\Facebook::syncFbRequest();
    }
    public function getVerticalList(){

    }
}