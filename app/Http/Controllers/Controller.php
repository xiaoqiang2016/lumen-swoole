<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    private $response;
    public function setResponse($response){
        $this->response = $response;
    }
    public function result($result=[]){
        if(is_array($result)){
            $result = json_encode($result);
        }
        if($this->response){
            return $this->response->end($result);
        }
        return $result;
    }
}
