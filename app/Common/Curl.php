<?php

namespace App\Common;
use Swoole\Coroutine\Http\Client;
use Swoole\Coroutine as co;

class Curl{
    static function post($url,$data=[]) {
        return self::send($url,'POST',['postData'=>$data]);
    }
    static function get($url){

        return self::send($url,'GET');
    }

    static function send($url,$method='GET',$params=[]){
        $urlData = parse_url($url);
        $port = $urlData['port'] ?? false;
        $urlData['query'] = isset($urlData['query']) ? "?".$urlData['query'] : '';
        $urlData['path'] = isset($urlData['path']) ? $urlData['path'] : '/';
        $postData = [];
        if(isset($params['postData'])){
            $method = 'post';
            $postData = $params['postData'];
        }
        $cli = false;
        if($urlData['scheme'] == 'https'){
            $cli = new Client($urlData['host'], $port ?: '443', true);
            $cli->setHeaders([
                'ssl_host_name' => $urlData['host'],
            ]);
        }
        if($urlData['scheme'] == 'http'){
            $cli = new Client($urlData['host'], $port ?: '80');
            $cli->setHeaders([
                'Host' => $urlData['host'],
            ]);
        }
        $cli->set([ 'timeout' => 10]);

        $path = $urlData['path'].$urlData['query']??'';
        $method = strtoupper($method);
        #$path = 'v3.2/552758385231841?Status=DISAPPROVED&Payment=EXTENDED_CREDIT&SpendLimit=0.01&Vertical=RETAIL&Subvertical=TOY_AND_HOBBY&access_token=CAAUibl40bIcBAJkRAVZBTk8NkN4U36hrmRpUE4uyR3txrmzmKTybxRSSZBBMz3VNDZABKtXbbbZAqiGUFUz6pmJ0ZA2jbBLrioPEoz4sm1FYjmakfeOKfZCQOnzIZCOyqIZBTaXJaRWC8b0kb1v2lrVUHye9m7uq8F9dCOTJZBuz1Lq61HZCmhsypslk1ZA0aRqjT8ZD';

        if($method == 'POST'){
            $cli->post($path,$postData);
        }
        if($method == 'GET'){
            $cli->get($path);
        }
        $result = $cli->body;
        $cli->close();
        if($r = json_decode($result,true)) $result = $r;

        return $result;
    }
    static function batch($params){
        $result = [];
        if(!$params) return $result;
        $i = 0;
        $chan = new co\Channel();
        foreach($params as $i=>$param){
            go(function() use ($param,$i,$chan){
                $r = self::get($param['url']);
                $chan->push(['index'=>$i,'result'=>$r]);
                #echo $i++."\n";
            });
        }
        while(1){
            $data = $chan->pop();
            $result[$data['index']] = $data['result'];
            if(count($result) == count($params)) break;
        }
        return $result;
    }
    public static function syncGet($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        if($r = json_decode($result,true)) $result = $r;
        return $result;
    }
    public static function syncPost($url,$data=[]){
            //初始化
            $curl = curl_init();
            //设置抓取的url
            curl_setopt($curl, CURLOPT_URL, $url);
            //设置头文件的信息作为数据流输出
            curl_setopt($curl, CURLOPT_HEADER, 0);
            //设置获取的信息以文件流的形式返回，而不是直接输出。
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            //设置post方式提交
            curl_setopt($curl, CURLOPT_POST, 1);
            //设置post数据
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            //执行命令
            $result = curl_exec($curl);
            //关闭URL请求
            curl_close($curl);
            if($r = json_decode($result,true)) $result = $r;
            return $result;
    }
}