<?php

namespace App\Services\AdDiagnose;
use Swoole;
use App\Models;
class Base
{
    public $group = '';
    public $handle = '';
    public $name = '';
    public $point;
    protected $params = [];
    public $count = 0;
    public function __construct($params){
        $this->params = $params;
    }
    public function getParam($key){

        if($key == 'ad_account_id_number'){
            $result = $this->params['ad_account_id'];
            $result = "'".str_replace("act_","",$result)."'";
        }else{
            $result = $this->params[$key];
        }
        return $result;
    }
    public function match(){

        $result = $this->handle();
        $syncData = [];
        $syncData['account_id'] = $this->getParam('ad_account_id');
        $syncData['group'] = $this->group;
        $syncData['handle'] = $this->handle;
        $syncData['name'] = $this->name;
        $this->log('handle',$syncData);
        if($result){
            foreach($result as &$r) $r['addno'] = json_encode(isset($r['addno']) ? $r['addno'] : [],JSON_UNESCAPED_UNICODE);
        }else{
            $result = [];
        }
        (new \App\Models\AdDiagnose())->syncData($syncData,$result);

        $failCount = count($result);
        $result = [];
        $count = (int)$this->count();
        $this->log('handle_success',['总数'=>$count,"错误数"=>$failCount]);
        if($count > 0){
            $basePoint = 5;
            $result['count'] = $count;
            $result['point'] = $basePoint - $this->point;
            $result['fail'] = $failCount;
            $result['math_point'] = $result['point'] * ($result['count'] - $result['fail']) / $result['count'];
            #$result = [];
        }else{
            $result = false;
        }
        (new \App\Models\AdDiagnoseLog())->syncData($syncData,[$result]);
    }
    public function log($action,$params){
        $message = "";
        $action_data = [
            'handle' => '执行诊断',
            'handle_success' => '诊断执行完毕',
        ];
        $message .=  $action_data[$action].":".PHP_EOL;
        $message .=  json_encode($params,JSON_UNESCAPED_UNICODE).PHP_EOL;
        $message .= "===================================".PHP_EOL;
        echo $message;
    }
    public function getSuccess(){

    }
    public function getFail(){

    }
}