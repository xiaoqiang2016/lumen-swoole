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
    static function getVerticaConn(){
        return \DB::connection('msdw');
        #$dsn = 'Driver=VerticaDSN;Server=47.91.170.6;Port=5433;Database=DB_MSDW;';
        #return odbc_connect($dsn, 'db_dv2', 'Ms@20180607');
    }
    static function GND($offset = 0,$format="Y-m-d H:i:s"){
        return date($format,self::GNT($offset));
    }
    static function GNT($offset = 0){
        return time() + (86400*$offset);
    }
}