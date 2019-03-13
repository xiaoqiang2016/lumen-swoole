<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class Permissions extends Controller {


	//获取用户权限数据
    public function getPermissions(){

    	//$params = $this->getParams();

    	$params['manager_id'] = 2;
        $permission = new \App\Http\Services\Permissions();
        $permission->getPermissions($params);
    }
}
