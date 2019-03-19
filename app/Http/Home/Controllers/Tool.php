<?php

namespace App\Http\Home\Controllers;
use Swlib\Http\BufferStream;
use Swlib\Http\Exception\HttpExceptionMask;
use Swlib\Saber;
use Swlib\SaberGM;
use Swoole;
class Tool extends Controller
{

    //
    public function curlMulti(array $params){
        $count = count($params);
        $i = 0;
        $result = [];
        $execTime = [];
        $inFuncTime = microtime(true);
        #$count = 11;
        for($i=0;$i<$count;$i++){
            $param = [];
            if(!is_array($params[$i])){
                $param['url'] = $params[$i];
                $param['url'] = parse_url($params[$i]);
            }
            $execTime[$i] = [];
            $execTime[$i]['start'] = microtime(true) - $inFuncTime;
            $cli = new Swoole\Coroutine\Http\Client($param['url']['host'], 443,true);
            $workerNum = 10;

            go(function() use ($param,$i,&$count,&$result,&$execTime,$inFuncTime,&$cli){

                $execTime[$i]['inGo'] = microtime(true) - $inFuncTime;

                $cli->setHeaders([
                    'Host' => $param['url']['host']
                ]);
                $cli->set([ 'timeout' => 10]);
                $cli->get($param['url']['path']."?".$param['url']['query']);
                #echo $cli->body;
                $result[$i] = $cli->body;
                #echo microtime(true) - $inFuncTime;
                #echo PHP_EOL;
                #echo $param['url']['host'].$param['url']['path']."?".$param['url']['query'];
                #echo PHP_EOL;
                $execTime[$i]['finish'] = microtime(true) - $inFuncTime;

                if($count == $i){
                    $cli->close();
                    print_r($execTime);
                    return $this->result($result);
                }
            });
        }
    }
}
