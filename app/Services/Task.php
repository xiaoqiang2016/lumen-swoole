<?php

namespace App\Services;
use Swoole;
use App\Models;
class Task
{
    //开户状态变化
    public function openAccountNotify($params=[]){
        #echo 'openAccountNotify';
        #$url = 'http://127.0.0.1:9506/';
        $url = "";
        $result = \App\Common\Curl::post($url,$params);
        if(is_array($result) && $result['gcode'] == 200){
            return ['status'=>'success','interval_time'=>10,'result'=>$result];
        }else{
            return ['status'=>'fail','result'=>$result];
        }
    }
}