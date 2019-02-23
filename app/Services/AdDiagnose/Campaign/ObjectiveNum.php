<?php

namespace App\Services\AdDiagnose\Campaign;
use Swoole;
use App\Models;
class ObjectiveNum extends Base
{
    public $name = "营销目标数量";
    public $description = "";
    public function handle(){
        $ad_account_ids = $this->getParam('ad_account_ids');
        $r = Models\AdCampaign::whereIn('account_id',$ad_account_ids)->select('account_id','objective')->groupby("account_id","objective")->get();
        $result = [];
        $countData = [];

        if($r) foreach($r->toArray() as $v){
            $countData[$v['account_id']] = $countData[$v['account_id']] ?? [];
            $countData[$v['account_id']][$v['objective']] = isset($r[$v['account_id']][$v['objective']]) ? $r[$v['account_id']][$v['objective']] + 1 : 1;
        }
        if($countData) foreach($countData as $k=>$data){
            $_result = [];
            $_result['account_id'] = $k;
            $_result['status'] = count($data) == 1 ? 'single' : 'success';
            if($_result['status'] == 'success'){
                $_result['desc'] = "已使用多个营销目标。";
            }else{
                $_result['desc'] = "营销目标单一。";
            }
            $result[] = $_result;
        }
        return $result;
    }
    public function getDescription(){

    }
}