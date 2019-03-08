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
    public $count = 0;
    public function handle(){
        $category_key = 'ctr';
        $per = '20';
        $ad_account_id = $this->getParam('ad_account_id');
        #echo $ad_account_id.PHP_EOL;

        $datas = (new Models\Msdw\FactFbAdinsightsCountry())->getCtrByAdAccountId([$ad_account_id]);
        #print_r($datas);
        $ad_ids = array_column($datas,'ad_id');
        if(!$ad_ids) return [];
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
                    $this->count++;
                    $data[$category_key] = sprintf("%.2f",$data[$category_key]);
                    $diagnose[$category_key] = sprintf("%.2f",$diagnose[$category_key]);
                    if($diagnose[$category_key] * (1-$per/100) > $data[$category_key]){
                        $r = [];
                        $r['account_id'] = $data['account_id'];
                        $r['campaign_id'] = $data['campaign_id'];
                        $r['ad_id'] = $data['ad_id'];
                        $r['addno'] = [
                            'category_value' => sprintf("%.2f",$diagnose[$category_key]*100),
                            'low_per' => sprintf("%.2f",(1 - $data[$category_key]/$diagnose[$category_key] ) * 100),
                            'value' => sprintf("%.2f",$data[$category_key]*100),
                            'category1_cn' => $data['category1_cn'],
                            'category2_cn' => $data['category2_cn'],
                            'category3_cn' => $data['category3_cn'],
                        ];
                        $r['status'] = 'low';
                        $r['desc'] = "低于行业标准".($r['addno']['low_per'])."%";
                        $result[] = $r;
                    }
                }
            }
        }
        #print_r($ad_ids);
        return $result;

    }
    public function count(){
        return $this->count;
    }
}