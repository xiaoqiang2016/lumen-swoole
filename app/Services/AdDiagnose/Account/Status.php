<?php

namespace App\Services\AdDiagnose\Account;
use Swoole;
use App\Models;
class Status extends Base
{
    public $name = "账户状态";
    public $description = "";
    public $point = 1;
    public function handle(){
        $disable = [
            'NONE',
            'ADS_INTEGRITY_POLICY',
            'ADS_IP_REVIEW',
            'RISK_PAYMENT',
            'GRAY_ACCOUNT_SHUT_DOWN',
            'ADS_AFC_REVIEW',
            'BUSINESS_INTEGRITY_RAR',
            'PERMANENT_CLOSE',
            'UNUSED_RESELLER_ACCOUNT',
            'UNUSED_ACCOUNT',
        ];
        $disable_reason = [
            'NONE' => '正常',
            'ADS_INTEGRITY_POLICY' => '广告诚信政策',
            'ADS_IP_REVIEW' => '广告_ IP _评论',
            'RISK_PAYMENT' => '支付风险',
            'GRAY_ACCOUNT_SHUT_DOWN' => '灰色帐户',
            'ADS_AFC_REVIEW' => '广告_ AFC _评论',
            'BUSINESS_INTEGRITY_RAR' => '商业诚信',
            'PERMANENT_CLOSE' => '永久关闭',
            'UNUSED_RESELLER_ACCOUNT' => '未使用的经销商账户',
            'UNUSED_ACCOUNT' => '未使用的账户',
        ];
        $ad_account_id = $this->getParam('ad_account_id');
        $r = Models\AdAccount::where('id',$ad_account_id)->first(['id','remote_status']);
        $result = [];
        if($r->remote_status != 1){
            $result['status'] = 'fail';
            $result['desc'] = "您的广告账号因[".$disable_reason[$disable[$r->remote_status]]."]被关闭。";
            $result['addno'] = ['status'=>$r->remote_status,'disable_reason'=>$disable_reason[$disable[$r->remote_status]]];
            $result = [$result];
        }
        return $result;
    }
    public function count(){
        return 1;
    }
}