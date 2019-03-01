<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services as Services;
class User extends Controller
{
    private $channelService;
//    public function __construct(Services\Auth $ServicesChannel)
//    {
//        $this->channelService = $ServicesChannel;
//    }
    /**
     * @OA\Post(
     *     path="/User/login",
     *     tags={"用户"},
     *     summary="登陆",
     *     description="用户登陆接口.",
     *     operationId="createUser",
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="loginName",
     *                     description="登录名",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="登陆密码",
     *                     type="password"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     ),
     * )
     */
    public function login(){
        echo 'login';
//        $params = $request->all();
//        $channel_id = $params['channel_id'];
//        $user = $this->getLoginUser();
//        $client_id = 1;
//        #$result = $this->channelService->adAccount()->getListByClient($client);
//        #return $this->result($result);
    }
}
