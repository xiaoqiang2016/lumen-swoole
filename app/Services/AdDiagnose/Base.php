<?php

namespace App\Services\AdDiagnose;
use Swoole;
use App\Models;
class Base
{
    public $group = '';
    public $handle = '';
    public $name = '';
    protected $params = [];
    public function __construct($params){
        $this->params = $params;
    }
    public function getParam($key){

        if($key == 'ad_account_ids_number'){
            $result = $this->params['ad_account_ids'];
            foreach($result as &$v) $v = "'".str_replace("act_","",$v)."'";
        }else{
            $result = $this->params[$key];
        }
        return $result;
    }
    public function match(){
        $result = $this->handle();
        $syncData = [];
        $syncData['account_id'] = $this->getParam('ad_account_ids');
        $syncData['group'] = $this->group;
        $syncData['handle'] = $this->handle;
        $syncData['name'] = $this->name;
        if($result){
            foreach($result as &$r) $r['addno'] = json_encode(isset($r['addno']) ? $r['addno'] : [],JSON_UNESCAPED_UNICODE);
        }
        (new \App\Models\AdDiagnose())->syncData($syncData,$result);
        #$fail = $this->getFail();
        #print_r(instanceof $success);
        #echo 'match';
    }
    public function getSuccess(){

    }
    public function getFail(){

    }
}