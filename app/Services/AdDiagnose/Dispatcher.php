<?php

namespace App\Services\AdDiagnose;
use Swoole;
use App\Models;
class Dispatcher
{
    private $routers = [
//        'Account' => [
//            ['handle'=>'Status'],
//            ['handle'=>'PageStatus'],
//
//        ],
//        'Campaign' => [
//            ['handle'=>'ObjectiveNum'],
//        ]
//        ,
        'Ad' => [
            #['handle'=>'CtrLow'],
            ['handle'=>'UseCta'],
        ],
    ];
    //调度器
    public function handle(array $params){
        foreach($this->routers as $group=>$router){
            foreach($router as $r){
                $className = "\\App\\Services\\AdDiagnose\\".$group."\\".$r['handle'];
                $class = new $className($params);
                $class->group = $group;
                $class->handle = $r['handle'];
                $class->match();
            }
        }
    }
}