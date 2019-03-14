<?php

namespace App\Services;
use Swoole;
use App\Models;
class Task
{
    //开户状态变化
    public function openAccountNotify($params=[]){
        #echo 'openAccountNotify';
        $url = 'http://www.baidu.com';
        $a = \App\Common\Curl::get($url);
        print_r($a);
    }
}