<?php

namespace App\Http\Home\Controllers;
use Illuminate\Http\Request;

class Permissions extends Controller {


	//获取用户权限数据
    public function getPermissions(){
    	//$params = $this->getParams();
    	$params['manager_id'] = 1;
        $permission = new \App\Http\Services\Permissions();
        return $permission->getPermissions($params);
    }
}
