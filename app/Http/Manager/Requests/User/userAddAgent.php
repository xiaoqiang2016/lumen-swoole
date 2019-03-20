<?php
namespace App\Http\Manager\Requests\User;
class userAddAgent{
	public function rules(){
		return [
            'name' => 'required',
            'phone' => 'required',
            'pwd' => 'required',
            'type' => 'required',
            'parent_id'=>'required|numeric'
		];
	}
	public function messages(){
		return [
			'name.required'=>'名称必填',
			'phone.required'=>'手机必填',
			'pwd.required'=>'密码必填',
			'type.required'=>'类型必填',
			'parent_id.required'=>'必填',

		];
	}
	public function attributes(){
		return [
            'name' => '用户名',
            'phone' => '手机',
            'pwd' =>'登录密码',
            'type' =>'类型',
            'parent_id'=>'父级'
		];
	}
}