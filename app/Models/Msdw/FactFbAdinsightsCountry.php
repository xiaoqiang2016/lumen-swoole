<?php
namespace App\Models\Msdw;
class FactFbAdinsightsCountry extends Model{
    protected $table = 'msdw.fact_fb_adinsights_country';
    public function getCtrByAdAccountId(array $ids,$date=''){
        if($date == ''){
            $date = \App\Common\Helper::GND(-1,"Y-m-d");
        }
        $dateStart = date("Y-m-d",strtotime($date) - 86400 * 2);
        $keys = (new DimAdaccount())->getAdAccountKeys($ids);
        $ad_keys = implode(",",$keys);

        #$sql = "select max(adaccount_key) as adaccount_key,max(campaign_id) as campaign_id,max(adset_id) as set_id,ad_id,case when sum(impressions) = 0 then 0 else sum(clicks)/sum(impressions) end as ctr from msdw.fact_fb_adinsights_country where adaccount_key IN ({$ad_keys}) and calendar_date = '{$date}' group by ad_id ;";
        $sql = "select max(ac.adaccount_key) as adaccount_key,
                       max(ac.campaign_id) as campaign_id,
                       max(ac.adset_id) as set_id,max(ac.ad_id) as ad_id,
                       case when sum(ac.impressions) = 0 then 0 else sum(ac.clicks)/sum(ac.impressions) end as ctr 
                       from msdw.fact_fb_adinsights_country  ac
                       inner join msdw.dim_fb_ad ad on ad.ad_id = ac.ad_id 
                       where  
                       adaccount_key IN ({$ad_keys}) AND
                       ac.calendar_date = '{$date}' AND ad.created_time > '{$dateStart}' 
                       group by ac.ad_id;";

        $result = $this->getDB()->select($sql);
        $keys = array_flip($keys);
        if($result) foreach($result as &$v){
            #$v['ctr'] = 0;
            $v['account_id'] = $keys[$v['adaccount_key']];
        }
        return $result;
    }
    public function getCpmByAdAccountId(array $ids,$date=''){
        if($date == ''){
            $date = \App\Common\Helper::GND(-1,"Y-m-d");
        }
        $dateStart = date("Y-m-d",strtotime($date) - 86400 * 2);
        $keys = (new DimAdaccount())->getAdAccountKeys($ids);
        $ad_keys = implode(",",$keys);
        #$sql = "select max(adaccount_key) as adaccount_key,max(campaign_id) as campaign_id,max(adset_id) as set_id,ad_id,case when sum(impressions) = 0 then 0 else sum(spend)/sum(impressions)*1000 end as cpm from msdw.fact_fb_adinsights_country where adaccount_key IN ({$ad_keys}) and calendar_date = '{$date}' group by ad_id ;";
        $sql = "select max(ac.adaccount_key) as adaccount_key,
                       max(ac.campaign_id) as campaign_id,
                       max(ac.adset_id) as set_id,max(ac.ad_id) as ad_id,
                       case when sum(ac.impressions) = 0 then 0 else sum(ac.spend)/sum(ac.impressions)*1000 end as cpm 
                       from msdw.fact_fb_adinsights_country  ac
                       inner join msdw.dim_fb_ad ad on ad.ad_id = ac.ad_id 
                       where  
                       adaccount_key IN ({$ad_keys}) AND
                       ac.calendar_date = '{$date}' AND ad.created_time > '{$dateStart}' 
                       group by ac.ad_id;";
        $result = $this->getDB()->select($sql);
        $keys = array_flip($keys);
        if($result) foreach($result as &$v){
            $v['account_id'] = $keys[$v['adaccount_key']];
        }
        return $result;
    }
    public function getCpaByAdAccountId(array $ids,$date=''){
        if($date == ''){
            $date = \App\Common\Helper::GND(-1,"Y-m-d");
        }
        $dateStart = date("Y-m-d",strtotime($date) - 86400 * 2);
        $keys = (new DimAdaccount())->getAdAccountKeys($ids);
        $_keys = array_flip($keys);
        $ad_keys = implode(",",$keys);
        $caseStr = "CASE ";
        $objectiveData = [];
        $objectiveData['MOBILE_APP_ENGAGEMENT'] = 'click_link';
        $objectiveData['CONVERSIONS'] = 'purchase';
        $objectiveData['EVENT_RESPONSES'] = 'event_responses';
        $objectiveData['POST_ENGAGEMENT'] = 'post_engagement';
        $objectiveData['LINK_CLICKS'] = 'click_link';
        $objectiveData['PAGE_LIKES'] = 'page_Likes';
        $objectiveData['VIDEO_VIEWS'] = 'video_views';
        $objectiveData['MOBILE_APP_INSTALLS'] = 'Mobile_App_Installs';
        $objectiveData['BRAND_AWARENESS'] = 'estimated_ad_recallers';
        $objectiveData['REACH'] = 'reach';
        $objectiveData['APP_INSTALLS'] = 'Mobile_App_Installs';
        $objectiveData['LEAD_GENERATION'] = 'leads_form';
        $objectiveData['PRODUCT_CATALOG_SALES'] = 'purchase';
        $objectiveData['TRAFFIC'] = 'clicks';

        foreach($objectiveData as $k=>$od){
            $caseStr .= " WHEN max(campaign.objective) = '{$k}' THEN ( CASE when sum(cou.{$od}) = 0 then 0 else sum(cou.spend) / sum(cou.{$od}) end)";
        }
        $caseStr .= " ELSE 0 END as cpa";
        $sql = "select max(cou.adaccount_key) as adaccount_key,max(cou.campaign_id) as campaign_id,max(cou.adset_id) as set_id,cou.ad_id,max(campaign.objective) as objective,{$caseStr}
                from msdw.fact_fb_adinsights_country cou
                INNER JOIN msdw.dim_fb_campaign campaign ON campaign.campaign_id = cou.campaign_id
                inner join msdw.dim_fb_ad ad on ad.ad_id = cou.ad_id 
                where adaccount_key IN ({$ad_keys}) and calendar_date = '{$date}' AND ad.created_time > '{$dateStart}'  group by cou.ad_id ;
                ";
        $result = $this->getDB()->select($sql);
        foreach($result as &$v) $v['account_id'] = $_keys[$v['adaccount_key']];
        return $result;
    }
}