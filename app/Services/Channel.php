<?php

namespace App\Services;
use Swoole;
use app\Models\Client;
class Channel{
	public function getData($id=0){
		$data = [
			1 => 'Facebook',
			3 => 'Google',
		];
		if($id) return $data[$id];
		return $data;
	}
	public function getAdAccountListByClient(Client $client){
		echo $client->id;
		
	}
    public function login(){
        echo 'login';
    }
    public function test(){
        echo 'test';
    }
}