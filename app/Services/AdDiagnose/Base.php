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
        return $this->params[$key];
    }
    public function match(){
        $result = $this->handle();
        if($result){
            $syncData = [];
            $syncData['account_id'] = $this->getParam('ad_account_ids');
            $syncData['group'] = $this->group;
            $syncData['handle'] = $this->handle;
            #$syncData['name'] = $this->name;
            (new \App\Models\AdDianose())->syncData($syncData,$result);
        }
        #$fail = $this->getFail();
        #print_r(instanceof $success);
        #echo 'match';
    }
    public function getSuccess(){

    }
    public function getFail(){

    }
}