<?php
namespace App\Services\AdDiagnose\Ad;
use Swoole;
use App\Models;
use App\Common\Helper;
class UseCta extends Base
{
    public $name = "行动号召";
    public $description = "";
    public function handle(){
        $ad_account_ids = $this->getParam('ad_account_ids_number');
        $obj = new Models\Msdw\DimFbAd();

        $sql = "SELECT ad_id,ad_name,adset_id as set_id,campaign_id,account_id FROM msdw.dim_fb_ad WHERE account_id IN (".implode(",",$ad_account_ids).") AND call_to_action_type = ''";
        $data = $obj->getDB()->select($sql);
        $result = [];
        if($data) foreach($data as $v){
            $_result = [];
            $_result['campaign_id'] = $v['campaign_id'];
            $_result['set_id'] = $v['set_id'];
            $_result['ad_id'] = $v['ad_id'];
            $_result['status'] = 'not';
            $_result['account_id'] = 'act_'.$v['account_id'];
            $_result['desc'] = "[{$v['ad_name']}]未使用行动号召。";
            $_result['addno'] = ['ad_name'=>$v['ad_name'],'ad_id'=>$v['ad_id']];
            $result[] = $_result;
        }
        return $result;
    }
}