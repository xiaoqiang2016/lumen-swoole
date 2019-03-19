<?php

namespace App\Http\Manager\Controllers;
use Illuminate\Http\Request;
use App\Http\Manager\Services as Services;
class Auth extends Controller
{
    public function __construct(Services\Manager $ServicesManager)
    {
        $this->servicesManager = $ServicesManager;
    }
    //注册
    public function register()
    {
        //$params = $request->all();
        $params['phone'] = '15151654876';
        $data['pwd'] = '123456';
        $data['repwd'] = '123456';
        //根据渠道码查推荐人id
        $params['reg_id'] = 1;

        //手机发送验证码

        //验证输入的密码
        if($data['pwd'] !== $data['repwd'])  return $this->errorMsg=array('code'=>400,'msg'=>'两次密码不一致');
        //加密密码
        $params['pwd'] = md5(md5($data['pwd']).'sinoclick');
        $params['type']= 'Agent';
        $params['use_status'] = '未审核';
        unset($data);
        $res = $this->servicesManager->register($params);
        if(!$res) return $this->errorMsg=array('code'=>400,'msg'=>'网络异常，请联系管理员');
        $this->login($params);
        //return $this->errorMsg=array('code'=>200,'msg'=>'注册成功，欢迎使用sinoclick代理商管理平台','token'=>11);

    }
}
