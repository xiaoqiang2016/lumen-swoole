<?php
namespace App\Manager\Requests;

class CheckRole{

	public function rules(){

		//获取拥有的权限集合
		$rules = new \App\Manager\Services\Rules();
		$hasRules = $rules->getRules(['manager_id'=>1],true);

		return [
            'rules' => "in:".$hasRules,
            //'permission' => "in:1,2,3",
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