<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
use Illuminate\Http\Request;
$router->get('/', function () use ($router) {
    return $router->app->version().'!!';
});
$router->get('/{controller:[A-Za-z]+}/{action:[A-Za-z]+}', function ($controller='default',$action='_404') use ($router) {
    $request =  Request::capture();
    $controller = ucwords(strtolower($controller));
    $params = $request->all();
    dd($_SERVER);
    exit;
    unset($params['s']);//会附带额外的s参数，unset掉
    //数据验证
    $valideClassName = "App\\Http\\Requests\\{$controller}\\{$action}";
//    $valide = new $valideClassName();
//    $validator = Validator::make($params, $valide->rules(), $valide->messages(), $valide->attributes());
//    $failed = $validator->failed();
//    $messages = $validator->messages();
//    if(count($messages) != 0){
//        echo json_encode($messages->toArray(),JSON_UNESCAPED_UNICODE);
//        exit;
//    }
    //执行逻辑
    $controllerName = 'App\\Http\\Controllers\\'.$controller;
    $controller  = $router->app->make($controllerName);
    $result = $controller->$action($request);
    print_r($result);
});
$router->post('/{controller:[A-Za-z]+}/{action:[A-Za-z]+}', function ($controller='default',$action='_404') use ($router) {
    $request =  Request::capture();
    $controller = ucwords(strtolower($controller));
    $params = $request->all();
    unset($params['s']);//会附带额外的s参数，unset掉
    //执行逻辑
    $controllerName = 'App\\Http\\Controllers\\'.$controller;

    $controller  = $router->app->make($controllerName);
    if($router->app->response){
        $controller->setResponse($router->app->response);
    }
    $result = $controller->$action($request);

});
$router->post('/multi', function () use ($router) {
    $request =  Request::capture();
    #$controller = ucwords(strtolower($controller));
    $requests = $request->all();
    $count = count($requests);
    $i = 0;
    $execCount = 0;
    $result = [];
    for($i=0;$i<$count;$i++){
        $request = $requests[$i];
        go(function() use ($request,$router,$i,$count,&$execCount,&$result){
            $controller = $request['controller'];
            $action = $request['action'];
            $params = $request['params'];
            $controllerName = 'App\\Http\\Controllers\\'.$controller;
            $controller  = $router->app->make($controllerName);
            $r = $controller->$action($params);
            $result[$i] = $r;
            $execCount++;
            if($execCount == $count){
                $router->app->response->end(json_encode($result));
            }
        });
    }
    return;
    unset($params['s']);//会附带额外的s参数，unset掉
    //执行逻辑



    if($router->app->response){
        $controller->setResponse($router->app->response);
    }


});
$router->get('/test', 'Test@test');
