<?php

namespace App\Http\Manager\Controllers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis as Redis;
use App\Models;
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
    public $errorMsg=array();
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
    public function login ($params)
    {
        //查询id
        $model = new Models\Manager();
        $ret = $model->where('phone', '=', $params['phone'])->first()->toArray();
        //验证密码
        if( md5(md5($params['pwd']).'sinoclick') !== $ret['pwd']) return $this->errorMsg=array('code'=>400,'msg'=>'账号/密码不正确');
        if($ret['use_status'] == 99) return $this->errorMsg=array('code'=>400,'msg'=>'账号被禁用，请联系客服');
//        print_r($ret);
//        $data = array('user_id'=>$userId);
        return $this->setLoginUser($ret);
        //return $user;
    }
    //
    public function setLoginUser($params)
    {
        $token = md5(microtime(true,rand(1000,999999)));
        Redis::set("accessToken:{$token}",$params,86400);
        return $token;
    }
}
