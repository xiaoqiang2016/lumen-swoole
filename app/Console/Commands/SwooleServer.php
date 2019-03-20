<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis as Redis;
use Co;
use Swoole;
class SwooleServer extends Command{
	protected $signature = 'http {action}';
    protected $description = 'Swoole Server.';
    private $cache = ['test'=>0];
    private $serverConf = [
    	'httpPort' => 9506,
        'serverPort' => 9507
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

            system("pkill -f php");
            $this->httpStart();
            $this->serverStart();
        }
    }
    public function serverStart(){

        $server = new Swoole\Server('0.0.0.0', $this->serverConf['serverPort'], SWOOLE_BASE, SWOOLE_SOCK_TCP);
        $server->set(array(
            'worker_num' => 4,
            'task_worker_num' => 10,
        ));
        $server->on('Receive', function($serv, $fd, $reactor_id, $data) {
            $tasks = \App\Models\Task::limit(10)->orderby('id','ASC')->get();
            foreach($tasks as $task){
                $serv->task($task, 0);
            }
        });
        $server->on('Task', function ($serv, $task_id, $from_id, $data) {
            #echo "Tasker进程接收到数据";
            #echo "#{$serv->worker_id}\tonTask: [PID={$serv->worker_pid}]: task_id=$task_id, data_len=".strlen($data).".".PHP_EOL;
            $c = "\\App\\Services\\Task";
            $c = new $c;
            $a = $data->action;
            $params = json_decode($data->params,true);
            $c->$a($params);
            $serv->finish($data);
        });
        $server->on('Finish', function ($serv, $task_id, $data) {
            #echo "Task#$task_id finished, data_len=".strlen($data).PHP_EOL;
        });
        $server->on('WorkerStart', function ($serv,$worker_id) {
            if($worker_id === 0){
                swoole_timer_tick(1000, function(){
                    $client = new \Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
                    if (!$client->connect('127.0.0.1', $this->serverConf['serverPort'], 0.5))
                    {
                        exit("connect failed. Error: {$client->errCode}\n");
                    }
                    $client->send("hello world\n");
                    $client->close();
                });
            }
        });
        $server->start();

    }
    public function httpStart(){
        $this->initApp();
        $httpServer = new \Swoole\Http\Server("0.0.0.0", $this->serverConf['httpPort']);
        $httpServer->set(array(
            'worker_num' => 6,    //worker process num
            'backlog' => 128,   //listen backlog
            'max_request' => 5000,
            'dispatch_mode'=>1,
            'max_coroutine' => 9999999,
            //'task_worker_num' => 1000,
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
            //取token
            //$token = $_SERVER['HTTP_TOKEN'];
            //$user = Redis::get('accessToken:'.$token)??0;
            //解析传参
            $params = [];
            $request->server['request_method'] == 'POST' && $params = $request->post;
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
            print_r($_pathData);
            return;
            //数据验证
            $valideClassName = "App\\Http\\{$groupName}\\Requests\\{$controllerName}\\{$actionName}";
            $checkRoleClassName = "App\\Http\\{$groupName}\\Requests\\CheckRole";    //基础接口角色验证
            if(class_exists($checkRoleClassName) && $actionName){
                //基础权限验证
                $params['permission'] = $actionName;
                for($i = 1; $i <= 2; $i++) {
                    switch ($i) {
                        case 1:
                            $valide = new $checkRoleClassName();
                            break;
                        default:
                            $valide = new $valideClassName();
                            break;
                    }
                    $validator = \Illuminate\Support\Facades\Validator::make($params, $valide->rules(), $valide->messages(), $valide->attributes());
                    $failed = $validator->fails();
                    $messages = $validator->messages();
                    if($failed) {
                        $result = [];
                        $result['code'] = 400;
                        $result['message'] = $messages->toArray();
                        $result['result'] = [];
                        $swooleResponse->sendJson($result);
                        return;
                    }
                    if(!class_exists($valideClassName)) break;
                }
                unset($params['permission']);
            }

            //Result
            $controllerName = 'App\\Http\\'.$groupName.'\\Controllers\\'.$controllerName;
            #var_export($controllerName);exit;
            if(class_exists($controllerName)){
                #$request =  Request::capture();
                $controller = app()->make($controllerName);
                $controller->setResponse($swooleResponse);
                $controller->setParams($params);
                //$controller->setUserInfo();
                $_result = $controller->$actionName($request);
                if($_result !== false){
                    $result = [];
                    $result['code'] = 200;
                    $result['message'] = [];
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
            //$this->test();
        });
        $httpServer->start();
<<<<<<< HEAD
        
=======
        $this->httpServer = $httpServer;
    }
    private function sendTask(){
        if($this->cache['task_push_lock']) return;
        $this->cache['task_push_lock'] = true;
        $tasks = \App\Models\Task::where("status","wait")->orderby("id","ASC")->limit(10)->get();
        //$this->httpServer->task($tasks);
        #echo $this->httpServer->test;
>>>>>>> 972953ec416f8500361ff165e8fa78f764e4f569
    }
    private function test(){
        $startTime = microtime(true);
        go(function() use ($startTime){
            $cli = new \Swoole\Coroutine\Http\Client('127.0.0.1', $this->serverConf['httpPort']);
            $cli->set([ 'timeout' => 10]);
<<<<<<< HEAD
            $cli->get("/task");


=======
            //$cli->post("/Manager/menu/list",[['name'=>'代理商','memo'=>'备注']]);
//            $cli->post("/Manager/Auth/register?",[['name'=>'代理商','memo'=>'备注']]);
            $cli->get("/Manager/Auth/register?phone='15151654876'");
>>>>>>> 972953ec416f8500361ff165e8fa78f764e4f569
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