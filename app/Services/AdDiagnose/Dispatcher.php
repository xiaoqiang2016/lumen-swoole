<?php

namespace App\Services\AdDiagnose;
use Swoole;
use App\Models;
class Dispatcher
{
    private $routers = [
        'Account' => [
            ['class'=>'Status'],
        ],
        #'Campaign'
    ];
    //调度器
    public function handle(array $params){
        foreach($this->routers as $group=>$router){
            foreach($router as $r){
                $className = "\\App\\Services\\AdDiagnose\\".$group."\\".$r['class'];
                $class = new $className($params);
                $class->group = $group;
                $class->handle = $r['class'];
                $class->match();
            }
        }
    }
}