<?php
namespace App\Manager\Requests\Role;

class roleEdit{

	public function rules(){
		
		return [
			'guid' => 'required'
		];
	}
	public function messages(){
		return [
			'guid.required' => '标识必填'
		];
	}
	public function attributes(){
		return [
            'guid' => 'ID',
		];
	}
}