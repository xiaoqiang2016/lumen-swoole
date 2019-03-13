<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Co;
#use Swoole;
class SwooleServer extends Command{
	protected $signature = 'http {action}';
    protected $description = 'Swoole Server.';

    private $serverConf = [
    	'httpPort' => 9502,
    ];
    private $app = false;
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $action = $this->argument('action');
        if($action == 'start'){
            $this->start();
        }
    }
    public function start(){
        $this->initApp();
        $httpServer = new \Swoole\Http\Server("0.0.0.0", $this->serverConf['httpPort']);
        $httpServer->set(array(
            'worker_num' => 6,    //worker process num
            'backlog' => 128,   //listen backlog
            'max_request' => 5000,
            'dispatch_mode'=>1,
            'max_coroutine' => 9999999,
        ));
        $httpServer->on('request', function($request, $response){
            if(env('APP_DEBUG') && false){
                $logdir = realpath(__DIR__."/../../../storage/logs/")."/";
                file_put_contents($logdir."sql_facebook.log", "" );
                file_put_contents($logdir."sql_sinoclick.log", "");
                \DB::connection("facebook")->listen(function ($query) use ($logdir) {
                    $sql = str_replace("?", "'%s'", $query->sql);
                    $log = vsprintf($sql, $query->bindings);
                    $log = '[' . date('Y-m-d H:i:s') . '] ('.$query->time.'ms) ' . $log . "\r\n";
                    file_put_contents($logdir."sql_facebook.log", $log ,FILE_APPEND);
                });
                \DB::connection("sinoclick")->listen(function ($query)use ($logdir) {
                    $sql = str_replace("?", "'%s'", $query->sql);
                    $log = vsprintf($sql, $query->bindings);
                    $log = '[' . date('Y-m-d H:i:s') . '] ('.$query->time.'ms) ' . $log . "\r\n";
                    file_put_contents($logdir."sql_sinoclick.log", $log,FILE_APPEND);
                });
                \DB::connection("msdw")->listen(function ($query)use ($logdir) {
                    $sql = str_replace("?", "'%s'", $query->sql);
                    $log = vsprintf($sql, $query->bindings);
                    $log = '[' . date('Y-m-d H:i:s') . '] ('.$query->time.'ms) ' . $log . "\r\n";
                    file_put_contents($logdir."sql_msdw.log", $log,FILE_APPEND);
                });
            }
            $path_info = $request->server['path_info'];
            $swooleResponse = new \App\Common\SwooleResponse($response);
            if($swooleResponse->sendFile($path_info)) return;
            //解析传参
            $params = [];
            $request->server['request_method'] == 'POST' && $requestData = $request->post;
            if($request->server['request_method'] == 'GET'){
                if($quertString = $request->server['query_string']??false){
                    parse_str($quertString,$params);
                }
            }
            $_pathData = array_filter(explode("/",$path_info));
            $pathData = [];
            if($_pathData) foreach($_pathData as $v) $pathData[] = $v;
            $groupName = $pathData[0]??false;
            $controllerName = $pathData[1]??false;
            $actionName = $pathData[2]??false;
            //数据验证
            $valideClassName = "App\\{$groupName}\\Requests\\{$controllerName}\\{$actionName}";
            if(class_exists($valideClassName) && $actionName){
                $valide = new $valideClassName();
                $validator = \Illuminate\Support\Facades\Validator::make($params, $valide->rules(), $valide->messages(), $valide->attributes());
                $failed = $validator->failed();
                $messages = $validator->messages();
                if(count($messages) != 0){
                    $result = [];
                    $result['error'] = ['code'=>'FORM_VALIDATE_FAIL','message'=>$messages->toArray()];
                    $result['result'] = [];
                    $swooleResponse->sendJson($result);
                    return;
                }
            }
            //Result
            $controllerName = 'App\\'.$groupName.'\\Controllers\\'.$controllerName;
            #var_export($controllerName);exit;
            if(class_exists($controllerName)){
                #$request =  Request::capture();
                $controller = app()->make($controllerName);
                $controller->setResponse($swooleResponse);
                $controller->setParams($params);
                $_result = $controller->$actionName($request);
                if($_result !== false){
                    $result = [];
                    $result['error'] = [];
                    $result['result'] = $_result;
                    $swooleResponse->sendJson($result);
                }
                return;
            }
            $httpCode = 404;
            $response->status($httpCode);
            if($swooleResponse->sendFile("status/{$httpCode}.jpeg")) return;
            $response->end($httpCode);
            return;
        });
        $httpServer->on('start', function () {
            $this->test();
        });
        $httpServer->start();
    }
    private function test(){
        $startTime = microtime(true);
        go(function() use ($startTime){
            $cli = new \Swoole\Coroutine\Http\Client('127.0.0.1', $this->serverConf['httpPort']);
            $cli->set([ 'timeout' => 10]);
            $cli->get("/Manager/Adaccount/test");
            echo PHP_EOL.'Result:'.PHP_EOL;
            $result = $cli->body;
            print_r($result);
            echo PHP_EOL;
            echo 'exec time : ';
            echo (microtime(true) - $startTime);
            $cli->close();
        });
    }
    private function initApp(){
        return app();
        $base_dir = dirname(dirname(dirname(__DIR__)));
        $app = new \Laravel\Lumen\Application(
            $base_dir
        );
//        $app->singleton(
//            \Illuminate\Contracts\Debug\ExceptionHandler::class,
//            \App\Exceptions\Handler(1)
//        );

//        $app->singleton(
//            \Illuminate\Contracts\Console\Kernel::class,
//            \App\Console\Kernel::class
//        );
        $app->register(\Illuminate\Redis\RedisServiceProvider::class);
        $app->register(\Abram\Odbc\ODBCServiceProvider::class);
        $app->withFacades();
        $app->withEloquent();
        $app->bind('App\Repositories\Interfaces\Channel',function(){
            return new \App\Repositories\Interfaces\Channel();
        });
        $app->bind('App\Services\Interfaces\Channel',function() use ($app){
            return $app->make('\App\Services\Channel');
        });
        $app->router->group([
            'namespace' => '\App\Http\Controllers',
        ], function ($router) use ($base_dir) {
            require $base_dir.'/routes/web.php';
        });
        $this->app = $app;
    }
    private function getApp(){
        $base_dir = dirname(dirname(dirname(__DIR__)));
        $app = new \Laravel\Lumen\Application(
            $base_dir
        );
        $app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \App\Exceptions\Handler::class
        );

        $app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            \App\Console\Kernel::class
        );
        $app->register(\Illuminate\Redis\RedisServiceProvider::class);
        $app->register(\Abram\Odbc\ODBCServiceProvider::class);
        $app->withFacades();
        $app->withEloquent();
        $app->bind('App\Repositories\Interfaces\Channel',function(){
            return new \App\Repositories\Interfaces\Channel();
        });
        $app->bind('App\Services\Interfaces\Channel',function() use ($app){
            return $app->make('\App\Services\Channel');
        });
        $app->router->group([
            'namespace' => '\App\Http\Controllers',
        ], function ($router) use ($base_dir) {
            require $base_dir.'/routes/web.php';
        });
        $this->app = $app;
        return $this->app;
    }
}