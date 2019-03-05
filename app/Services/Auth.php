<?php

namespace App\Services;
use Swoole;
use App\Models;
class Auth
{
    public function userLogin($loginName,$password){
        $loginName = '457995985@qq.com';
        $userModel = new Models\User();
        $r = $userModel->where("loginname","=",$loginName)->get(['id']);
        #print_r($r->toArray());
        print_r($r->toArray());
        echo 123;
    }
}