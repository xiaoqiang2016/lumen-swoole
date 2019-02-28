<?php
namespace App\Models\Msdw;
class FactFbAdinsightsCountry extends Model{
    protected $table = 'msdw.fact_fb_adinsights_country';
    public function getCtrByAdAccountId(array $ids){
        $keys = (new DimAdaccount())->getAdAccountKeys($ids);
        $ad_keys = implode(",",$keys);
        $sql = "select max(adaccount_key) as adaccount_key,max(campaign_id) as campaign_id,max(adset_id) as set_id,ad_id,case when sum(impressions) = 0 then 0 else sum(clicks)/sum(impressions) end as ctr from msdw.fact_fb_adinsights_country where adaccount_key IN ({$ad_keys}) and calendar_date = '2018-03-16' group by ad_id ;";
        $result = $this->getDB()->select($sql);
        $keys = array_flip($keys);
        if($result) foreach($result as &$v){
            $v['account_id'] = $keys[$v['adaccount_key']];
        }
        return $result;
    }
    public function getCpmByAdAccountId(array $ids){
        $keys = (new DimAdaccount())->getAdAccountKeys($ids);
        $ad_keys = implode(",",$keys);
        $sql = "select max(adaccount_key) as adaccount_key,max(campaign_id) as campaign_id,max(adset_id) as set_id,ad_id,case when sum(impressions) = 0 then 0 else sum(spend)/sum(impressions)*1000 end as cpm from msdw.fact_fb_adinsights_country where adaccount_key IN ({$ad_keys}) and calendar_date = '2018-03-16' group by ad_id ;";
        $result = $this->getDB()->select($sql);
        $keys = array_flip($keys);
        if($result) foreach($result as &$v){
            $v['account_id'] = $keys[$v['adaccount_key']];
        }
        return $result;
    }
    public function getCpaByAdAccountId(array $ids){
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
                where adaccount_key IN ({$ad_keys}) and calendar_date = '2018-03-16' group by ad_id ;
                ";
        $result = $this->getDB()->select($sql);
        foreach($result as &$v) $v['account_id'] = $_keys[$v['adaccount_key']];
        return $result;
    }
}