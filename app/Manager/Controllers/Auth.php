<?php

namespace App\Manager\Controllers;
use Illuminate\Http\Request;
class Auth extends Controller
{
//    private $servicesManager;
//    public function __construct(Services\Manager $ServicesManager)
//    {
//        $this->servicesManager = $ServicesManager;
//    }
    //注册
    //public function register(Request $request)
    public function register()
    {
//        $params = $request->all();
        //注册需要
//        $params['eamil'] = '15151654876@qq.com';
        $params['phone'] = '15151654876';
        $params['pwd'] = '123456';
        $params['reg_code'] = 'abcd';//渠道码 可以为空

        $permission = new \App\Manager\Services\Manager();
        return $permission->register($params);
//        $params['status'] = 1;//用户状态
//        echo 2;exit;
        //$loginName = 'leo.qin';
//        $params = $this->getParams();
        //var_export($params);exit;
        $res = ManagerServices::register($params);
        #password
        //接受参数
        #$params = $request->all();
 #Manager::created($params);
        //验证类

    }
}
