<?php

namespace App\Services\AdDiagnose\Set;
use Swoole;
use App\Models;
class LinkClickBidAmount extends Base
{
    public $name = "访问量广告组竞价上限";
    public $description = "";
    public function handle(){
        $obj = new \App\Models\Msdw\DimFbAdset();
        $objective = "LINK_CLICKS";
        $budget = "10";//$10
        $map = [];
        $map[] = "campaign.objective = '{$objective}'";
        $map[] = "adset.account_id IN (".implode(",",$this->getParam("ad_account_ids_number")).")";
        $map[] = "adset.bid_amount < ".($budget);
        $map[] = "adset.status = 'ACTIVE'";
        $sql = "SELECT adset.account_id,adset.adset_name as set_name,adset.adset_id as set_id,adset.daily_budget,adset.campaign_id,adset.bid_amount
                FROM msdw.dim_fb_adset adset 
                INNER JOIN msdw.dim_fb_campaign campaign ON adset.campaign_id = campaign.campaign_id 
                WHERE ".implode(" AND ",$map)." ;";
        $datas = $obj->getDB()->select($sql);

        #$r = $obj->DimFbCampaign()->where('objective',$objective)->limit(10)->get();
        #$r = $obj->getDB()->table($obj->getTable())->join("msdw.dim_fb_campaign","msdw.dim_fb_adset.campaign_id","=","msdw.dim_fb_campaign.campaign_id")->limit(10)->get();
        $result = [];
        if($datas) foreach($datas as $data){
            $_result = [];
            $_result['account_id'] = "act_".$data['account_id'];
            $_result['campaign_id'] = $data['campaign_id'];
            $_result['set_id'] = $data['set_id'];
            if($data['bid_amount'] == 0){
                continue;
            }else{
                if($budget > $data['bid_amount']){
                    $_result['status'] = 'low';
                    $_result['desc'] = $this->name."[{$data['set_name']}]小于$".($budget)."。";
                }
            }
            $_result['addno'] = ['set_name'=>$data['set_name'],'bid_amount'=>sprintf("%.2f",$data['bid_amount']),'set_id'=>$data['set_id'],'check_budget'=>sprintf("%.2f",$budget)];
            $result[] = $_result;
        }
        return $result;
    }
    public function getDescription(){

    }
}