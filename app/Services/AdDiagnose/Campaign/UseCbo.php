<?php

namespace App\Services\AdDiagnose\Campaign;
use Swoole;
use App\Models;
use App\Common\Helper;
class UseCbo extends Base
{
    public $name = "未使用CBO";
    public $description = "";
    public $connection = 'msdw';
    public function handle(){
        $result = [];
        $obj = new \App\Models\Msdw\DimFbAdset();
        $objective = "CONVERSIONS";
        $budget = "30";//$10
        $map = [];

        $map[] = "t.account_id IN (".implode(",",$this->getParam("ad_account_ids_number")).")";
        $map[] = "campaign.status = 'ACTIVE'";
        $map[] = "t.spend_cap <> ''";
        $map[] = "(t.bid_strategy = 'False' OR t.bid_strategy = '')";
        $sql = "SELECT t.campaign_id,t.account_id,t.bid_strategy,campaign.campaign_name
                FROM facebookods.fb_campaign t
                INNER JOIN msdw.dim_fb_campaign campaign ON campaign.campaign_id = t.campaign_id
                WHERE ".implode(" AND ",$map)." ;";
        $datas = $obj->getDB()->select($sql);
        if($datas){
            foreach($datas as $data){
                $_result = [];
                $_result['account_id'] = "act_".$data['account_id'];
                $_result['campaign_id'] = $data['campaign_id'];
                $_result['status'] = 'fail';
                $_result['desc'] = "[".$data['campaign_name']."]未使用CBO。";
                $_result['addno'] = [];
                $result[] = $_result;
            }
        }
        return $result;
    }
}