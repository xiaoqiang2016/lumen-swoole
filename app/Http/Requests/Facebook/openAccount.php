<?php
namespace App\Http\Requests\Facebook;
class openAccount{
    public function rules(){
        return [
            'loginName' => 'required',
            'password' => 'required',
        ];
    }
    public function messages(){
        return [];
    }
    public function attributes(){
        return [
            'loginName' => '登录名',
            'password' =>'登录密码',
        ];
    }
}