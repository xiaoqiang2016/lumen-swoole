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

    //添加管理员
    public function userAddManager() {
        $params = $this->getParams();
        return $this->model->userAdd($params,'Admin');
    }

    //添加代理商
    public function userAddAgent() {
        $params = $this->getParams();
        return $this->model->userAdd($params,'Agent');
    }

    //添加DB
    public function userAddBD() {
        $params = $this->getParams();
        return $this->model->userAdd($params,'BD');
    }

    ////添加OP
    public function userAddOP() {
        $params = $this->getParams();
        return $this->model->userAdd($params,'OP');
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