<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services as Services;
class User extends Controller
{
    private $authService;
    public function __construct(Services\Auth $authService)
    {
        $this->authService = $authService;
    }
    public function login(){
        $params = $this->getParams();
        $this->authService->userLogin($params['loginName'],$params['password']);
//        $params = $request->all();
//        $channel_id = $params['channel_id'];
//        $user = $this->getLoginUser();
//        $client_id = 1;
//        #$result = $this->channelService->adAccount()->getListByClient($client);
//        #return $this->result($result);
    }
}
