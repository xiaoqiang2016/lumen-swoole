<?php

namespace App\Services\AdDiagnose;
use Swoole;
use App\Models;
class Base
{
    protected $params = [];
    public function __construct($params){
        $this->params = $params;
    }
    public function getParam($key){
        return $this->params[$key];
    }
    public function match(){
        $success = $this->getSuccess();
        #$fail = $this->getFail();
        #echo 'match';
    }
    public function getSuccess(){

    }
}