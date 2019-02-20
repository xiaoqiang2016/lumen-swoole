<?php

namespace App\Services\AdDiagnose\Account;
use Swoole;
use App\Models;
class PageStatis extends Base
{
    public $name = "主页状态";
    public $description = "";
    public function handle(){
        $ad_account_ids = $this->getParam('ad_account_ids');
        $r = Models\AdAccount::whereIn('id',$ad_account_ids)->get(['id','remote_status']);
        $result = [];
        foreach($r as $_r){
            $_result = [];
            $_result['account_id'] = $_r['id'];
            $_result['status'] = $_r['remote_status'] == 1 ? 'success' : strtolower($disable[$_r['remote_status']]);

            if($_result['status'] == 'success'){
                $_result['desc'] = "您的广告账号状态正常。";
            }else{
                $_result['desc'] = "您的广告账号因[".$disable_reason[$disable[$_r['remote_status']]]."]被关闭。";
            }
            $result[] = $_result;
        }
        return $result;
    }
    public function getDescription(){

    }
}