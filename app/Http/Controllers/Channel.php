<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services as Services;
class Channel extends Controller
{
    private $response;
    private $channelService;
    public function __construct(Services\Channel $ServicesChannel)
    {
        $this->channelService = $ServicesChannel;
    }
    /**
     * @Get(
     *     path="/demo",
     *     tags={"演示"},
     *     summary="演示API",
     *     @RequestBody(
     *         @MediaType(
     *             mediaType="application/json",
     *             @Schema(
     *                 required={"name", "age"},
     *                 @Property(property="name", type="string", description="姓名"),
     *                 @Property(property="age", type="integer", description="年龄"),
     *                 @Property(property="gender", type="string", description="性别")
     *             )
     *         )
     *     ),
     *     @Response(
     *         response="200",
     *         description="正常操作响应",
     *         @MediaType(
     *             mediaType="application/json",
     *             @Schema(
     *                 allOf={
     *                     @Schema(ref="#/components/schemas/ApiResponse"),
     *                     @Schema(
     *                         type="object",
     *                         @Property(property="data", ref="#/components/schemas/DemoResp")
     *                     )
     *                 }
     *             )
     *         )
     *     )
     * )
     *
     * @param Request $request
     *
     * @return DemoResp
     */
    public function getAdAccountList(){
        #$this->channelService->syncAllByUser($this->getLoginUser());
        return 1234;
    }
    public function getCampaigns(Request $request){
        $params = $request->all();
        $channel_id = $params['channel_id'];
        $user = $this->getLoginUser();
        $client_id = 1;
        #$result = $this->channelService->adAccount()->getListByClient($client);
        #return $this->result($result);
    }
}
