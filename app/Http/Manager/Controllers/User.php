<?php

namespace App\Http\Manager\Controllers;
use Illuminate\Http\Request;

//用户
class User extends Controller {

    public $model;
    public function __construct() {
        $this->model = new \App\Models\Manager();
    }

    //用户列表
    public function list() {
        //登录
        $params['manager_id'] = 1;
        $roles = $this->model->findChildManager($params['manager_id']);
        return $roles;
    }

    //添加用户
    public function userAdd() {
        $params = $this->getParams();
        return $this->model->userAdd($params);
    }

    //给用户分配角色
    public function userAllocation() {
        $params = $this->getParams();
        $params['manager_id'] = 1;
        $params['type'] = 'Admin';
        $service = new \App\Http\Manager\Services\Manager();
        $service->userAllocation($params);
    }

    //删除用户
    public function roleDel() {
        $params = $this->getParams();
        return $this->model->where('id',$params['id'])->update(['status'=>0]);
    }

}