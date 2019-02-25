<?php

namespace App\Services\AdDiagnose\Ad;
use Swoole;
use App\Models;
use App\Common\Helper;
class CtrLow extends Base
{
    public $name = "CTR较低";
    public $description = "";
    public $connection = 'msdw';
    public function handle(){
        $ad_account_ids = $this->getParam('ad_account_ids');
        $ctrs = (new Models\Msdw\FactFbAdinsightsCountry())->getCtrByAdAccountId($ad_account_ids);
        $ad_ids = array_column($ctrs,'ad_id');
        $categorys = (new Models\Msdw\DimFbAd())->getCategoryByAdIds($ad_ids);
        //归类类别
        $categoryData = [];
        foreach($categorys as $category){
            if($category['category1_cn']!='') $categoryData[md5($category['category1_cn']."_".$category['category2_cn']."_".$category['category3_cn'])] = ['category1_cn'=>$category['category1_cn'],'category2_cn'=>$category['category2_cn'],'category3_cn'=>$category['category3_cn']];
        }
        $aias = new Models\AdIndustryAverageStats();
        if(!$categoryData) return;
        $wstr = [];
        foreach($categoryData as $cd){

            for($i=1;$i<=3;$i++){

            }
            $wstr[] = "(category_level_1 ".($cd['category1_cn'])."= 'APP' AND category_level_2 is NULL AND category_level_3 is NULL)";
        }
        $sql = "SELECT ctr FROM {$aias->getTable()} WHERE ".implode(" OR ",$wstr)." ";
        echo $sql;
        return;
        print_r($categoryData);
        #print_r($ad_ids);
        return;
        //select max(adaccount_key) as adaccount_key,max(campaign_id) as campaign_id,max(adset_id) as set_id,ad_id,case when sum(impressions) = 0 then 0 else sum(clicks)/sum(impressions) end as ctr from msdw.fact_fb_adinsights_country where adaccount_key IN (393138,393139) and calendar_date = '2018-03-16' group by ad_id ;
        $obj = new Models\Msdw\FactFbAdinsightsCountry();
        $datas = $obj->limit(10)->groupby('ad_id')->get(["max(adaccount_key)"]);
        print_r($datas);
        return;
        $adAd = new Models\AdAd();
        $adAd = $adAd->where('status','ACTIVE');
        $adAd = $adAd->whereIn('account_id',$ad_account_ids);
        $adAd = $adAd->where('category1_cn','!=','');
        $adAd = $adAd->where('created_time','<=',Helper::GND(-2,"Y-m-d 00:00:00"));
        $r = $adAd->get(['id','category1_cn','category2_cn','category3_cn']);
        $r = $r->toArray();
        $r = array_slice($r,0,10);
        $ad_ids = array_column($r,'id');
        $r = (new Models\Msdw\FactFbAdinsightsCountry)->getCtrByAdIds($ad_ids);
        print_r($r);
        return;
        $ad_account_ids = $this->getParam('ad_account_ids');
        app('db')->connection($this->connection);
        print_r($ad_account_ids);
    }
    public function getDescription(){

    }
}