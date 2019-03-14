<?php

namespace App\Services;
use Swoole;
use App\Models;
class WebHook extends Service
{
    //开户状态变化
    public function openAccountNotify($params=[]){
        echo 'openAccountNotify';
    }
}