<?php

namespace App\Services\AdDiagnose\Ad;
use Swoole;
use App\Models;
use App\Common\Helper;
class CpaHigh extends Base
{
    public $name = "CPA较高";
    public $description = "";
    public $connection = 'msdw';
    public function handle(){
        $category_key = 'cpm';
        $per = '20';
        $ad_account_ids = $this->getParam('ad_account_ids');
        $datas = (new Models\Msdw\FactFbAdinsightsCountry())->getCpaByAdAccountId($ad_account_ids);
        $cpaMap = [];
        $capMap['MOBILE_APP_ENGAGEMENT'] = 'cpa_link_click'; //app
        $capMap['CONVERSIONS'] = 'cpa_purchase';
        $capMap['EVENT_RESPONSES'] = 'cpa_event_responses';
        $capMap['POST_ENGAGEMENT'] = 'cpa_post_engagement';
        $capMap['LINK_CLICKS'] = 'cpa_link_click';
        $capMap['PAGE_LIKES'] = 'cpa_like';
        $capMap['VIDEO_VIEWS'] = 'cpa_video_views';
        $capMap['MOBILE_APP_INSTALLS'] = 'cpa_install';//app
        $capMap['BRAND_AWARENESS'] = 'cpa_estimated_ad_recallers';
        $capMap['REACH'] = 'cpa_reach';
        $capMap['APP_INSTALLS'] = 'cpa_install';//app
        $capMap['LEAD_GENERATION'] = 'cpa_lead';
        $capMap['PRODUCT_CATALOG_SALES'] = 'cpa_purchase';
        $capMap['TRAFFIC'] = 'cpc';


        $ad_ids = array_column($datas,'ad_id');
        $categorys = (new Models\Msdw\DimFbAd())->getCategoryByAdIds($ad_ids);

        foreach($datas as &$data){
            foreach($categorys as $category){
                if($category['ad_id'] == $data['ad_id']){
                    $data['category1_cn'] = $category['category1_cn'];
                    $data['category2_cn'] = $category['category2_cn'];
                    $data['category3_cn'] = $category['category3_cn'];
                }
            }
        }
        foreach($datas as $k=>$v){
            if(!isset($v['category1_cn']) || !$v['category1_cn']) unset($datas[$k]);
        }
        //归类类别
        $categoryData = [];
        foreach($categorys as $category){
            if($category['category1_cn']!='') $categoryData[md5($category['category1_cn']."_".$category['category2_cn']."_".$category['category3_cn'])] = ['category1_cn'=>$category['category1_cn'],'category2_cn'=>$category['category2_cn'],'category3_cn'=>$category['category3_cn']];
        }
        $aias = new Models\AdIndustryAverageStats();
        if(!$categoryData) return;
        $wstr = [];
        foreach($categoryData as $cd){
            $_wstr = [];
            for($i=1;$i<=3;$i++){
                $value = $cd["category{$i}_cn"] ? "'".$cd["category{$i}_cn"]."'" : 'NULL';
                $op = $value == 'NULL' ? 'is' : '=';
                $_wstr[] = "category_level_{$i} {$op} {$value}";
            }
            $wstr[] = "(".implode(" AND ",$_wstr).")";
        }
        $category_key = implode(",",$capMap);
        $sql = "SELECT category_level_1 as category1_cn,category_level_2 as category2_cn,category_level_3 as category3_cn,{$category_key} FROM {$aias->getTable()} WHERE ".implode(" OR ",$wstr)." ";
        $diagnoseData = $aias->getDB()->select($sql);
        $diagnoseData = json_decode(json_encode($diagnoseData),true);
        #$category_value = $r[$category_key];

        #print_r($category_value);
        #print_r($diagnoseData);
        $result = [];
        foreach($datas as &$data){
            #print_r($data);
            foreach($diagnoseData as $diagnose){
                if($diagnose['category1_cn'] == $data['category1_cn'] && $diagnose['category2_cn'] == $data['category2_cn'] && $diagnose['category3_cn'] == $data['category3_cn']){
                    $bKey = $capMap[$data['objective']];
                    $value = $data['cpa'];
                    $r = [];
                    $r['account_id'] = $data['account_id'];
                    $r['campaign_id'] = $data['campaign_id'];
                    $r['ad_id'] = $data['ad_id'];
                    $r['addno'] = [
                        'category_value' => sprintf("%.2f",$diagnose[$bKey]),
                        #'category_value' => $diagnose[$bKey],
                        'high_per' => 0,
                        'value' => sprintf("%.2f",$value),
                        #'value' => $value,
                        'category1_cn' => $data['category1_cn'],
                        'category2_cn' => $data['category2_cn'],
                        'category3_cn' => $data['category3_cn'],
                        'objective' => $data['objective'],
                        'bKey' => $bKey,
                    ];

                    if($data['cpa'] == 0){
                        $r['status'] = 'zero';
                        $r['desc'] = "CPA为零";
                    }else{
                        $_per = ($data['cpa']-$diagnose[$bKey]) / $diagnose[$bKey] * 100;
                        if($_per > $per) {
                            $r['addno']['high_per'] = sprintf("%.2f",$_per);
                            $r['status'] = 'high';
                            $r['desc'] = "高于行业标准".($r['addno']['high_per'])."%";
                        }else{
                            continue;
                        }
                    }
                    if($r) $result[] = $r;
                }
            }
        }
        return $result;

    }
}