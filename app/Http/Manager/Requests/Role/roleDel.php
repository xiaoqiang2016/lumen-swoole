<?php
namespace App\Http\Manager\Requests\Role;

class roleDel{

	public function rules(){
		
		return [
			'id' => 'required'
		];
	}
	public function messages(){
		return [
			'id.required' => '标识必填'
		];
	}
	public function attributes(){
		return [
            'id' => 'ID',
		];
	}
}