<?php

namespace App\Http\Manager\Controllers;
use Illuminate\Http\Request;

//权限 菜单管理
class Menu extends Controller {

	public $model;

	public function __construct() {
		$this->model = new \App\Models\Menu();
	}

    //权限列表
   	public function list() {
   		//登录
       	$params['manager_id'] = 1;
        $params['type'] = 'Admin';
        $permission = new \App\Manager\Services\Rules();
        return $permission->getRules($params);
   	}

   	//添加菜单
   	public function menuAdd() {
   		$params = $this->getParams();
   		return $this->model->insert($params);
   	}



}