<?php

namespace App\Http\Manager\Controllers;
use Illuminate\Http\Request;
class Auth extends Controller
{
//    public function __construct(Services\Manager $ServicesManager)
//    {
//        $this->servicesManager = $ServicesManager;
//    }
    //注册
    //public function register(Request $request)
    public function register()
    {
        //$params = $request->all();
        //注册需要
//        $params['eamil'] = '15151654876@qq.com';
        $params['phone'] = '15151654876';
        $data['pwd'] = '123456';
        $data['repwd'] = '1234565';
        $params['reg_code'] = 'abcd';//渠道码 可以为空 根据验证吗取id
        //手机发送验证码

        //验证输入的密码
        if($data['pwd'] !== $data['repwd'])  return $this->errorMsg=array('code'=>400,'msg'=>'两次密码不一致');
        //加密密码
        $params['pwd'] = md5(md5($data['pwd']).'sinoclick');
        unset($data);
        print_r($params);
       // $this->register($params);
//        var_export($params['pwd']);


    }
}
