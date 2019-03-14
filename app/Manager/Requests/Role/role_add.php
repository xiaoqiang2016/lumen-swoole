<?php
namespace App\Manager\Requests\Role;

class role_add{


	public function rules(){
		$rules = new \App\Manager\Services\Rules();
		$data = $rules->getRules(['manager_id'=>1],true);

		return [
            'rules' => "in:".$data,
            'password' => 'required'
		];
	}
	public function messages(){
		return [
			'rules.in'=>'抱歉你没有权限'
		];
	}
	public function attributes(){
		return [
            'loginName' => '登录名',
            'password' =>'登录密码',
		];
	}
}