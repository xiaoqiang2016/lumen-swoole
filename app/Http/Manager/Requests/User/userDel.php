<?php
namespace App\Http\Manager\Requests\User;
class userDel{
	public function rules(){
		return [
            'user_id' => 'required',
		];
	}
	public function messages(){
		return [];
	}
	public function attributes(){
		return [
            'user_id' => '用户标识',
		];
	}
}