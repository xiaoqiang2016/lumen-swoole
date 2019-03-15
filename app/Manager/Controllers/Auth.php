<?php

namespace App\Manager\Controllers;
use Illuminate\Http\Request;
use App\Manager\Services;
use App\Models\Manager;
class Auth extends Controller
{
    private $servicesManager;
    public function __construct(Services\Manager $ServicesManager)
    {
        $this->servicesManager = $ServicesManager;
    }
    //注册
    //public function register(Request $request)
    public function register()
    {
        echo 2;exit;
        //$loginName = 'leo.qin';
        $params = $this->getParams();
        //var_export($params);exit;
        $res = Manager::register($params);
        #password
        //接受参数
        #$params = $request->all();
 #Manager::created($params);
        //验证类

    }
}
