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
$router->get('{path:.*}', function ($path) use ($router) {
    if(\App\Common\Response::sendFile($path)) return;
    $pathData = explode("/",$path);
    $controllerName = $pathData[0];
    $actionName = $pathData[1];
    //数据验证
    $valideClassName = "App\\Http\\Requests\\{$controllerName}\\{$actionName}";
    if(class_exists($valideClassName)){
        $valide = new $valideClassName();
        $validator = Validator::make($params, $valide->rules(), $valide->messages(), $valide->attributes());
	    $failed = $validator->failed();
        $messages = $validator->messages();
	    if(count($messages) != 0){
            \App\Common\Response::sendJson($messages);
		    #echo json_encode($messages->toArray(),JSON_UNESCAPED_UNICODE);
		    return;
	    } 
    }
    //执行逻辑
    $controllerName = 'App\\Http\\Controllers\\'.$controllerName;
    if(class_exists($controllerName)){
        $request =  Request::capture();
        $controller = $router->app->make($controllerName);
        
        $controller->setRequest($request);
        $result = $controller->$actionName($request);
        if($result !== false){
            app()->response->end($result);
        }
        #
    }
    return;
});
$router->get('/{controller:[A-Za-z]+}/{action:[A-Za-z]+}', function ($controller='default',$action='_404') use ($router) {

    $request =  Request::capture();
    $controller = ucwords(strtolower($controller));
    $params = $request->all();
    unset($params['s']);//会附带额外的s参数，unset掉
    //执行逻辑
    $controllerName = 'App\\Http\\Controllers\\'.$controller;

    $controller  = $router->app->make($controllerName);

    $controller->setRequest($request);
    $result = $controller->$action($request);
    if(env('APP_DEBUG')){
        $logdir = __DIR__."/../storage/logs/";
        file_put_contents($logdir."sql_facebook.log", "" );
        file_put_contents($logdir."sql_sinoclick.log", "");
        DB::connection("facebook")->listen(function ($query) use ($logdir) {
            $sql = str_replace("?", "'%s'", $query->sql);
            $log = vsprintf($sql, $query->bindings);
            $log = '[' . date('Y-m-d H:i:s') . '] ('.$query->time.'ms) ' . $log . "\r\n";

            file_put_contents($logdir."sql_facebook.log", $log ,FILE_APPEND);
        });
        DB::connection("sinoclick")->listen(function ($query)use ($logdir) {
            $sql = str_replace("?", "'%s'", $query->sql);
            $log = vsprintf($sql, $query->bindings);
            $log = '[' . date('Y-m-d H:i:s') . '] ('.$query->time.'ms) ' . $log . "\r\n";
            file_put_contents($logdir."sql_sinoclick.log", $log,FILE_APPEND);
        });
        DB::connection("msdw")->listen(function ($query)use ($logdir) {
            $sql = str_replace("?", "'%s'", $query->sql);
            $log = vsprintf($sql, $query->bindings);
            $log = '[' . date('Y-m-d H:i:s') . '] ('.$query->time.'ms) ' . $log . "\r\n";
            file_put_contents($logdir."sql_msdw.log", $log,FILE_APPEND);
        });
    }

    print_r($result);
    if($router->app->response){
        $router->app->response->end($result);
        return;
        #$controller->setResponse($router->app->response);
    }
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
        #$controller->setResponse($router->app->response);
    }
    $controller->setRequest($request);
    if(env('APP_DEBUG')){
        $logdir = __DIR__."/../storage/logs/";
        file_put_contents($logdir."sql_facebook.log", "" );
        file_put_contents($logdir."sql_sinoclick.log", "");
        DB::connection("facebook")->listen(function ($query) use ($logdir) {
            $sql = str_replace("?", "'%s'", $query->sql);
            $log = vsprintf($sql, $query->bindings);
            $log = '[' . date('Y-m-d H:i:s') . '] ('.$query->time.'ms) ' . $log . "\r\n";

            file_put_contents($logdir."sql_facebook.log", $log ,FILE_APPEND);
        });
        DB::connection("sinoclick")->listen(function ($query)use ($logdir) {
            $sql = str_replace("?", "'%s'", $query->sql);
            $log = vsprintf($sql, $query->bindings);
            $log = '[' . date('Y-m-d H:i:s') . '] ('.$query->time.'ms) ' . $log . "\r\n";
            file_put_contents($logdir."sql_sinoclick.log", $log,FILE_APPEND);
        });
        DB::connection("msdw")->listen(function ($query)use ($logdir) {
            $sql = str_replace("?", "'%s'", $query->sql);
            $log = vsprintf($sql, $query->bindings);
            $log = '[' . date('Y-m-d H:i:s') . '] ('.$query->time.'ms) ' . $log . "\r\n";
            file_put_contents($logdir."sql_msdw.log", $log,FILE_APPEND);
        });
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
