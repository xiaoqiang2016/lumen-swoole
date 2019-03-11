<?php

namespace App\Services\AdDiagnose\Account;
use Swoole;
use App\Models;
class ObjectiveNum extends Base
{
    public $name = "营销目标数量";
    public $description = "";
    public $point = 2;
    public function handle(){
        $ad_account_id = $this->getParam('ad_account_id_number');
        $r = Models\Msdw\DimFbCampaign::where('account_id',(int)$ad_account_id)->select('account_id','objective')->groupby("account_id","objective")->get();

        $result = [];
        $countData = [];

        if($r) foreach($r->toArray() as $v){
            $countData[$v['account_id']] = $countData[$v['account_id']] ?? [];
            $countData[$v['account_id']][$v['objective']] = isset($r[$v['account_id']][$v['objective']]) ? $r[$v['account_id']][$v['objective']] + 1 : 1;
        }
        if($countData) foreach($countData as $account_id=>$data){
            if(count($data) == 1){
                $_result = [];
                $_result['account_id'] = $account_id;
                $_result['status'] =  'single' ;
                $_result['desc'] = "营销目标单一。";
                $_result['addno'] = ['objective'=>array_keys($data)[0]];
                $result[] = $_result;
            }

        }
        return $result;
    }
    public function count(){
        return 1;
    }
    public function getDescription(){

    }
}