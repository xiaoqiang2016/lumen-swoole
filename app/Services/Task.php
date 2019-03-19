<?php

namespace App\Services;
use Swoole;
use App\Models;
class Task
{
    //开户状态变化
    public function openAccountNotify($params=[]){
        return ['status'=>'success','result'=>[]];
        $url = "http://test.iland-web.meetsocial.cn/faceBook/openAccountFromOE.html";
        $result = \App\Common\Curl::syncPost($url,$params);
        if(is_array($result) && $result['gcode'] == 200){
            return ['status'=>'success','result'=>$result];
        }else{
            return ['status'=>'wait','result'=>$result];
        }
    }
}