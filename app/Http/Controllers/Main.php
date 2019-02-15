<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services as Services;
class Main extends Controller
{
    public function dispatch($path){
        $path = "User-1006631/".$path;
        $paths = array_filter(explode("/",$path));
        $router = [];
        foreach($paths as $k=>$path){
            $_path =array_filter(explode("-",$path));
            $object = $_path[0];
            $params = $_path[1] ?? false;
            if($k!=count($paths)-1){
               $object = $this->makeObject($object,$params);
            }
        }
    }
    private function makeObject($object,$params){
        $serviceName = "App\\Services\\{$object}";
        $object = new $serviceName;
        print_r($object);
        exit;
    }
}
