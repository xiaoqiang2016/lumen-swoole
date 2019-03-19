<?php
namespace App\Http\Manager\Requests\Auth;
class register
{
	public function rules(){
		return [
            'phone'   => 'required',
//            'password' => 'required',
//            'code' => 'required',
		];
	}
	public function messages(){
		return [
		    'phone.required'=>'请输入登陆名',
//		    'phone.mobile'=>'请输入正确的电话号码',
//		    'password.required'=>'请输入密码',
        ];
	}
	public function attributes(){
		return [
            'phone' => '登录名',
//            'password' =>'登录密码',
//            'code' =>'手机验证码',
		];
	}
}