<?php

namespace App\Services;
use Swoole;
use App\Models;
class Task
{
    //开户状态变化
    public function openAccountNotify($params=[]){
        echo 'openAccountNotify';
        \App\Common\Curl::post('http://127.0.0.1:9506',['test'=>1]);

    }
}