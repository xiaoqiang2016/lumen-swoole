<?php

namespace App\Http\Home\Controllers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Services\UserService;
use App\Models\User;

class Controller extends BaseController
{
    /**
     *
     * @Info(
     *     version="1.0.0",
     *     title="演示服务",
     *     description="这是演示服务，该文档提供了演示swagger api的功能",
     *     @Contact(
     *         email="mylxsw@aicode.cc",
     *         name="mylxsw"
     *     )
     * )
     *
     * @Server(
     *     url="http://localhost",
     *     description="开发环境",
     * )
     *
     * @Schema(
     *     schema="ApiResponse",
     *     type="object",
     *     description="响应实体，响应结果统一使用该结构1",
     *     title="响应实体",
     *     @Property(
     *         property="error",
     *         type="object",
     *         description="响应代码"
     *     ),
     *     @Property(
     *         property="result",
     *         type="string",
     *         description="响应结果提示"
     *     )
     * )
     *
     *
     * @package App\Http\Controllers
     */
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
