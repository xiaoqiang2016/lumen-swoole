<?php

namespace App\Manager\Controllers;
use Illuminate\Http\Request;

class Rules extends Controller {


	//获取用户权限数据 TODO(登录后获取用户ID)
    public function getRules(){
    	//$params = $this->getParams();
    	$params['manager_id'] = 1;
        $permission = new \App\Manager\Services\Rules();
        return $permission->getRules($params);
    }

}