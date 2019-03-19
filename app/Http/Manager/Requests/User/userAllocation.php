<?php
namespace App\Http\Manager\Requests\User;
class userAllocation{
	public function rules(){
		return [
            'user_id' => 'required|numeric',
            'role_id' => 'required|numeric',
		];
	}
	public function messages(){
		return [];
	}
	public function attributes(){
		return [
            'user_id' => '用户标识',
            'role_id' =>'角色标识',
		];
	}
}