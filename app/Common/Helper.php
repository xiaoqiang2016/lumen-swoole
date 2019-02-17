<?php

namespace App\Common;
use Swoole\Coroutine\Http\Client;
use Swoole\Coroutine as co;
use \Illuminate\Support\Facades\Redis;
class Helper{
    static function runTimeStart() {
        Redis::set('run_time',microtime(true));
    }
    static function runTime(){
        $rt = microtime(true) - Redis::get('run_time');
        $result = PHP_EOL;
        $result.= "=======================".PHP_EOL;
        $result.= $rt.PHP_EOL;
        $result.= "=======================".PHP_EOL;
        echo $result;
    }
}