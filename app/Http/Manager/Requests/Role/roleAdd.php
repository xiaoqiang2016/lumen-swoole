<?php
namespace App\Http\Manager\Requests\Role;

class roleAdd{

	public function rules(){
		
		return [
			'name' => 'required',
		];
	}
	public function messages(){
		return [
			'name.required' => '名称必填',
		];
	}
	public function attributes(){
		return [
            'name' => '名称',
		];
	}
}