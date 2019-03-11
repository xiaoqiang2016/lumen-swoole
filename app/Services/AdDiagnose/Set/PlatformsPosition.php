<?php

namespace App\Services\AdDiagnose\Set;
use Swoole;
use App\Models;
class PlatformsPosition extends Base
{
    public $name = "平台版位";
    public $description = "";
    public $point = 4;
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
        $allFields = array_merge($all['publisher_platforms'],$all['facebook_positions'],$all['instagram_position'],$all['messenger_positions']);
        #publisher_platforms = 'facebook,instagram,audience_network,messenger,whatsapp';
        #facebook_positions = 'feed,right_hand_column,suggested_video,instant_article,instream_video,marketplace,story';
        #instagram_position = 'stream,story,igtv'
        #messenger_positions = 'sponsored_messages,messenger_home'

        $ad_account_id = $this->getParam('ad_account_id_number');
        $sql = "select t.publisher_platforms,t.facebook_positions,t.instagram_positions,t.messenger_positions,t.adset_id,adset.account_id,adset.adset_name,adset.campaign_id
                from facebookods.fb_targeting t 
                inner join msdw.dim_fb_adset adset ON adset.adset_id = t.adset_id
                where adset.account_id = {$ad_account_id};";
        $datas = $obj->getDB()->select($sql);
        #$r = $obj->DimFbCampaign()->where('objective',$objective)->limit(10)->get();
        #$r = $obj->getDB()->table($obj->getTable())->join("msdw.dim_fb_campaign","msdw.dim_fb_adset.campaign_id","=","msdw.dim_fb_campaign.campaign_id")->limit(10)->get();
        $result = [];
        if($datas) foreach($datas as $data){
            $dataFeilds = [];
            $dataFeilds = array_filter(array_merge(explode(",",$data['publisher_platforms']),explode(",",$data['facebook_positions']),explode(",",$data['instagram_positions']),explode(",",$data['messenger_positions'])));
            $diff = array_diff($allFields,$dataFeilds);
//            print_r($allFields);
//            print_r($dataFeilds);
//            print_r($diff);
//            echo "============";
            if(count($diff) > 0){

                $_result = [];
                $_result['account_id'] = "act_".$data['account_id'];
                $_result['campaign_id'] = $data['campaign_id'];
                $_result['set_id'] = $data['adset_id'];
                $_result['status'] = 'notAll';
                $_result['desc'] = "[{$data['adset_name']}]未全选版位。";
                $_result['addno'] = ['diff'=>$diff];
                $result[] = $_result;
            }

            $this->count++;
        }
        return $result;
    }
    public function count(){
        return $this->count;
    }
}