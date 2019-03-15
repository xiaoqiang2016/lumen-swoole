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
        $port = $urlData['port'] ?? 80;
        $urlData['query'] = isset($urlData['query']) ? "?".$urlData['query'] : '';
        $urlData['path'] = isset($urlData['path']) ? $urlData['path'] : '/';
        $postData = [];
        if(isset($params['postData'])){
            $method = 'post';
            $postData = $params['postData'];
        }
        $cli = false;
        if($urlData['scheme'] == 'https'){
            $cli = new Client($urlData['host'], $port, true);
        }
        if($urlData['scheme'] == 'http'){
            $cli = new Client($urlData['host'], $port);
        }
        $cli->set([ 'timeout' => 10]);
        $cli->setHeaders([
            'Host' => $urlData['host']
        ]);
        $path = $urlData['path'].$urlData['query']??'';
        $method = strtoupper($method);
        if($method == 'POST'){
            $cli->post($path,$postData);
        }
        if($method == 'GET'){
            $cli->get($path);
        }
        $result = $cli->body;
        #echo "ERRORCODE:".$cli->statusCode.PHP_EOL;
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
}