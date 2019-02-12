<?php
namespace App\Repositories\Interfaces;

class Channel{
    public function find($id){
        $className = 'App\Repositories\Channel\\'.$id;
        return new $className();
    }
    public function getCampaigns($account_id){

    }
    /*
     * 根据ClientID获取本地广告账号列表
     * $map : 筛选条件
     * client_id
     */
    public function getAccountListLocal($map){
        if(!$map) return;
        $map = $params['map'];
    }
    public function getAccountListRemote($map){
        
    }

}