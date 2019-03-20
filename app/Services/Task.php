<?php

namespace App\Services;
use Swoole;
use App\Models;
class Task
{
    //开户状态变化
    public function openAccountNotify($params=[]){
        $url = env('ISLAND_API')."/faceBook/openAccountFromOE.html";
        $result = \App\Common\Curl::syncPost($url,$params);
        print_r($result);
        if(is_array($result) && $result['gcode'] == 200){
            return ['status'=>'success','result'=>$result];
        }else{
            return ['status'=>'wait','result'=>$result];
        }
    }
    public function openAccountAdAccountNotify($params){
        return ['status'=>'success','result'=>''];
        $url = env('ISLAND_API')."/faceBook/pushAuid.html";
        $result = \App\Common\Curl::syncPost($url,$params);
        if(is_array($result) && $result['gcode'] == 200){
            return ['status'=>'success','result'=>$result];
        }else{
            return ['status'=>'wait','result'=>$result];
        }
    }
}