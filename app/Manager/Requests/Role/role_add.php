<?php
namespace App\Manager\Requests\Role;

class role_add{

	public function rules(){
		
		return [
			'loginName' => 'required',
            'password' => 'required'
		];
	}
	public function messages(){
		return [
			
		];
	}
	public function attributes(){
		return [
            'loginName' => '登录名',
            'password' =>'登录密码',
		];
	}
}