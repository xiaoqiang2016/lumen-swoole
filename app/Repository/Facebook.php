<?php

namespace App\Repository;
use Swoole;
class Facebook
{
    private $token = 'CAAUibl40bIcBAJkRAVZBTk8NkN4U36hrmRpUE4uyR3txrmzmKTybxRSSZBBMz3VNDZABKtXbbbZAqiGUFUz6pmJ0ZA2jbBLrioPEoz4sm1FYjmakfeOKfZCQOnzIZCOyqIZBTaXJaRWC8b0kb1v2lrVUHye9m7uq8F9dCOTJZBuz1Lq61HZCmhsypslk1ZA0aRqjT8ZD';
    private $version = '3.2';
    public $account_id;
    public function getCampaigns(\Closure $callback){
        $limit = 9999;

        $query = "act_{$this->account_id}?fields=campaigns.limit({$limit}){name,status,objective}";
        $this->send([$query],function($response) use ($callback){
            $result = [];
            if($response) foreach($response as $r){
                foreach($r['campaigns']['data'] as $c) {
                    $result[] = $c;
                }
            }
            $callback($result);
        });
    }
    private function send($params,\Closure $callback){
        $count = count($params);
        $i = 0;
        $result = [];
        $execTime = [];

        $inFuncTime = microtime(true);
        #$count = 11;
        for($i=0;$i<$count;$i++){
            $param = [];
            if(!is_array($params[$i])){
                $url = "https://graph.facebook.com/v{$this->version}/".$params[$i];
                #$url = str_replace("{","%7B",$url);
                #$url = str_replace("{","%7B",$url);
                $param['url'] = parse_url($url);
            }
            $execTime[$i] = [];
            $execTime[$i]['start'] = microtime(true) - $inFuncTime;
            $cli = new Swoole\Coroutine\Http\Client($param['url']['host'], 443,true);
            go(function() use ($param,$i,&$count,&$result,&$execTime,$inFuncTime,&$cli,$callback){

                $execTime[$i]['inGo'] = microtime(true) - $inFuncTime;

                $cli->setHeaders([
                    'Host' => $param['url']['host']
                ]);
                $cli->set([ 'timeout' => 10]);
                $path = $param['url']['path']."?".$this->parseUrl($param['url']['query'])."&access_token=".$this->token;
                $cli->get($path);
                #echo $cli->body;
                $r = $cli->body;
                if($r = json_decode($r,true)){
                    $result[$i] = $r;
                }else{
                    $result[$i] = $cli->body;
                }

//                echo microtime(true) - $inFuncTime;
//                echo PHP_EOL;
//                echo $param['url']['host'].$path;
//                echo PHP_EOL;

                $execTime[$i]['finish'] = microtime(true) - $inFuncTime;
                if($count == $i+1){
                    $cli->close();
                    $callback($result);
                    #return $this->result($result);
                }
            });
        }
    }
    private function parseUrl($url){
        $url = str_replace('{','%7B',$url);
        $url = str_replace('}','%7D',$url);
        $url = str_replace(',','%2C',$url);
        return $url;
    }
}