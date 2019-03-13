<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Co;
#use Swoole;
class SwooleServer extends Command{
	protected $signature = 'http {action}';
    protected $description = 'Swoole Server.';
    private $cache;
    private $serverConf = [
    	'httpPort' => 9502,
    ];
    private $app = false;
    private $httpServer;
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
            $controllerName = $pathData[0]??false;
            $actionName = $pathData[1]??false;
            //数据验证
            $valideClassName = "App\\Http\\Requests\\{$controllerName}\\{$actionName}";
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
            $controllerName = 'App\\Http\\Controllers\\'.$controllerName;
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
            //task
            $this->cache['task_push_lock'] = false;
            swoole_timer_tick(1000, function ($timer_id) {
                $this->sendTask();
            });
        });
        $httpServer->on('Task', function (swoole_server $serv, $task_id, $from_id, $data) {
            echo "Tasker进程接收到数据";
            echo "#{$serv->worker_id}\tonTask: [PID={$serv->worker_pid}]: task_id=$task_id, data_len=".strlen($data).".".PHP_EOL;
            $serv->finish($data);
        });
        $httpServer->start();
        $this->httpServer = $httpServer;
    }
    private function sendTask(){
        if($this->cache['task_push_lock']) return;
        $this->cache['task_push_lock'] = true;
        $tasks = \App\Models\Task::where("status","wait")->orderby("id","ASC")->limit(10)->get();
        $this->httpServer->task($tasks);
        #echo $this->httpServer->test;
    }
    private function test(){
        $startTime = microtime(true);
        go(function() use ($startTime){
            $cli = new \Swoole\Coroutine\Http\Client('127.0.0.1', $this->serverConf['httpPort']);
            $cli->set([ 'timeout' => 10]);
            $cli->get("/Facebook/syncOeRequest");
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