<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Services\UserService;
use App\Models\User;

class Controller extends BaseController
{
    private $response;
    private $userService;
    public $request;
    public function setRequest(Request $request){
        $access_token = $request->cookie(env('USER_LOGIN_KEY'));
        if($access_token){
            
        }
        $this->request = $request;
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
