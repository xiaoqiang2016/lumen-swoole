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
}