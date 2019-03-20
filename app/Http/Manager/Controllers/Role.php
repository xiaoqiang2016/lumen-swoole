<?php

namespace App\Http\Manager\Controllers;
use Illuminate\Http\Request;

//角色管理
class Role extends Controller {

	public $model;

	public function __construct() {
		$this->model = new \App\Models\Role();
	}

    //角色列表
   	public function list() {
   		//登录
        $params['manager_id'] = 1;
        $service = new \App\Http\Manager\Services\Role();
   		$roles = $service->findChildRole($params);
   		return $roles;
   	}

   	//添加角色
   	public function roleAdd() {
   		$params = $this->getParams();
   		$params['create_manager_id'] = 1;    //登录用户manager_id
   		$service = new \App\Http\Manager\Services\Role();
        $service->roleAdd($params);
   	}

   	//编辑角色
   	public function roleEdit() {
   		$params = $this->getParams();
   		return $this->model->where('id',$params['id'])->update(['name'=>$params['name']]);
   	}

   	//删除角色
   	public function roleDel() {
   		$params = $this->getParams();
   		return $this->model->where('id',$params['id'])->update(['status'=>0]);
   	}

    //分配权限
    public function allocation() {
        $params = $this->getParams();
        $access = new \App\Models\Access();
        $access->addAll($params);
    }

}