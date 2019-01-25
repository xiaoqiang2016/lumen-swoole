<?php
namespace App\Repositories\Interfaces;

class Channel{
    public function find($id){
        $className = 'App\Repositories\Channel\\'.$id;
        return new $className();
    }
    public function getCampaigns($account_id){

    }
}