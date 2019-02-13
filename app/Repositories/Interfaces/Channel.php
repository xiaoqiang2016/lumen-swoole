<?php
namespace App\Repositories\Interfaces;

class Channel{
    public function find($id){
        $className = 'App\Repositories\Channel\\'.$id;
        return new $className();
    }
    public function getCampaignList($account_id){

    }
    public function pullCampaignList($account_id){

    }
}