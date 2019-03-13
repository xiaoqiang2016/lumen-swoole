<?php

namespace App\Services\AdDiagnose;
use Swoole;
use App\Models;
class Dispatcher
{
    private $routers = [
        'Account' => [
            ['handle'=>'Status'],
            ['handle'=>'PageStatus'], //同步主页可能有问题
            ['handle'=>'ObjectiveNum'],
        ],
        'Campaign' => [
            ['handle'=>'AbTesting'],
            ['handle'=>'UseCbo'],
        ]
        ,
        'Ad' => [
            ['handle'=>'CtrLow'],
            ['handle'=>'CpmHigh'],
            ['handle'=>'CpaHigh'],
            ['handle'=>'UseCta'],

        ],
        'Set' => [
            ['handle'=>'LinkClickBidAmount'],
            ['handle'=>'ConversionsBidAmount'],
            //['handle'=>'ConversionsDailyBudget'],
            ['handle'=>'PlatformsPosition']
        ]
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