<?php
namespace App\Common;
use Swoole;
class Curl{
    public static function exec($url){
        $url = parse_url($url);
        if($url['scheme'] == 'https'){
            $cli = new Swoole\Coroutine\Http\Client($url['host'],443,true);
        }else{
            $cli = new Swoole\Coroutine\Http\Client($url['host'],80);
        }
        $cli->setHeaders([
            'Host' => $url['host'],
        ]);
        $cli->set([ 'timeout' => 10]);
        $cli->get($url['path']."?".$url['query']);
        $result = $cli->body;
        $cli->close();
        if(@json_decode($result,true)) $result = json_decode($result,true);
        return $result;
    }
}