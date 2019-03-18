<?php

namespace App\Http\Channel\Controllers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Services\UserService;
use App\Models\User;

class Controller extends BaseController
{
    public $response;
    private $userService;
    public $request;
    public function setRequest(Request $request){
        $access_token = $request->cookie(env('USER_LOGIN_KEY'));
        if($access_token){

        }
        $this->request = $request;
    }
    public function setResponse($response){
        $this->response = $response;
    }
    public function setParams($params){
        $this->params = $params;
    }
    public function getParams(){
        return $this->params;
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
    public function getClient(){
        $client = new Client();
        $client->id = 1;
        return $client;
    }
    public function getLoginUser(){
        $user = new User();
        $user->id = 1006631;
        return $user;
    }
}
