<?php

namespace App\Http\Manager\Controllers;
use Illuminate\Http\Request;

//权限列表
class Access extends Controller {

    public function list(){
    	//登录
       	$params['manager_id'] = 1;
        $permission = new \App\Manager\Services\Rules();
        return $permission->getRules($params);
    }

}