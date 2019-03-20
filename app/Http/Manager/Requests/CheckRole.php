<?php
namespace App\Http\Manager\Requests;

class CheckRole{

	public function rules(){

		//获取拥有的权限集合
		$rules = new \App\Http\Manager\Services\Rules();
		$hasRules = $rules->getRules(['manager_id'=>1],true);
		$hasRules = $hasRules . ',list,register';

		return [
            'permission' => "in:".$hasRules,
		];
	}
	public function messages(){
		return [
			'permission.in'=>'对不起，您没有该权限'
		];
	}
	public function attributes(){
		return [
            'permission' => '权限',
		];
	}
}