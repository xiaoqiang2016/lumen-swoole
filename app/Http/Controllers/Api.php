<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services as Services;
use SwaggerLume\Generator;
class Api extends Controller
{
    public function docs(){
        config(['swagger-lume.paths.docs'=>config('swagger-lume.paths.annotations')."/../public/api/json"]);
        #$this->channelService->syncAllByUser($this->getLoginUser());
        Generator::generateDocs();
        $file = realpath(config('swagger-lume.paths.docs'))."/api-docs.json";
        \App\Common\Response::sendFile("api/json/api-docs.json");
        return false;
        #config(['test'=>123]);
        #echo config('test');
        #echo 1;
    }
}
