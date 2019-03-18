<?php

namespace App\Manager\Controllers;
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
        $permission = new \App\Manager\Services\Rules();
        return $permission->getRules($params);
   	}

   	//添加菜单
   	public function menuAdd() {
   		$params = $this->getParams();
   		return $this->model->insert($params);
   	}


   	// //删除菜单
   	// public function menuDel() {
   	// 	$params = $this->getParams();
   	// 	return $this->model->where('id',$params['id'])->update(['status'=>0]);
   	// }

    //分配权限
    public function allocation() {
        $params = $this->getParams();
        $access = new \App\Models\Access();
        $access->addAll($params);
    }


}