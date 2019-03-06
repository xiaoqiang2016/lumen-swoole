<?php

namespace App\Services\AdDiagnose\Set;
use Swoole;
use App\Models;
class PlatformsPosition extends Base
{
    public $name = "平台版位";
    public $description = "";
    public function handle(){
        $obj = new \App\Models\Msdw\DimFbAdset();
        $map = [];
//        $map[] = "campaign.objective = '{$objective}'";
//        $map[] = "adset.account_id IN (".implode(",",$this->getParam("ad_account_ids_number")).")";
//        $map[] = "adset.daily_budget < ".($budget*100);
//        $map[] = "adset.status = 'ACTIVE'";
        $all = [];
        $all['publisher_platforms'] = ['facebook','instagram','audience_network','messenger','whatsapp'];
        $all['facebook_positions'] = ['feed','right_hand_column','suggested_video','instant_article','instream_video','marketplace','story'];
        $all['instagram_position'] = ['stream','story','igtv'];
        $all['messenger_positions'] = ['sponsored_messages','messenger_home'];

        #publisher_platforms = 'facebook,instagram,audience_network,messenger,whatsapp';
        #facebook_positions = 'feed,right_hand_column,suggested_video,instant_article,instream_video,marketplace,story';
        #instagram_position = 'stream,story,igtv'
        #messenger_positions = 'sponsored_messages,messenger_home'

        $ad_account_ids = $this->getParam('ad_account_ids_number');
        $sql = "select t.publisher_platforms,t.facebook_positions,t.instagram_positions,t.messenger_positions,t.adset_id,adset.account_id,adset.adset_name,adset.campaign_id
                from facebookods.fb_targeting t 
                inner join msdw.dim_fb_adset adset ON adset.adset_id = t.adset_id
                where adset.account_id IN (".(implode(",",$ad_account_ids)).")
                limit 100;";
        $datas = $obj->getDB()->select($sql);
        #$r = $obj->DimFbCampaign()->where('objective',$objective)->limit(10)->get();
        #$r = $obj->getDB()->table($obj->getTable())->join("msdw.dim_fb_campaign","msdw.dim_fb_adset.campaign_id","=","msdw.dim_fb_campaign.campaign_id")->limit(10)->get();
        $fields = ['publisher_platforms','facebook_positions','instagram_position','messenger_positions'];
        if($datas) foreach($datas as $data){
            foreach($fields as $field){
                $status = 'not';
                $v = $data[$field] ?? false;
                if($v){
                    $v = array_filter(explode(",",$v));
                    if(!array_diff($all[$field],$v)){
                        $status = 'success';
                    }
                }
                if($status == 'success') continue;
                $_result = [];
                $_result['account_id'] = "act_".$data['account_id'];
                $_result['campaign_id'] = $data['campaign_id'];
                $_result['set_id'] = $data['adset_id'];
                $_result['status'] = $status;
                $_result['desc'] = "[{$data['adset_name']}]未全选版位。";
                $_result['addno'] = ['publisher_platforms'=>$data['publisher_platforms'],'facebook_positions'=>$data['facebook_positions'],'instagram_positions'=>$data['instagram_positions'],'messenger_positions'=>$data['messenger_positions'],];
                $result[] = $_result;
            }
        }
        return $result;
    }
    public function getDescription(){

    }
}