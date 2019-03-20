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
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $action = $this->argument('action');
        if($action == 'start'){
            $workerNum = 3;
            $pool = new Swoole\Process\Pool($workerNum);
            $pool->on("WorkerStart", function ($pool, $workerId) {
                if($workerId == 0){
                    $this->httpStart();
                }
                if($workerId == 1){
                    $this->execTask();
                    sleep(1);
                }
                if($workerId == 2){
                    $this->syncAccount();
                    sleep(600);
                }
            });
            $pool->on("WorkerStop", function ($pool, $workerId) {
                echo "Worker#{$workerId} is stopped\n";
            });
            $pool->start();
        }
    }
    public function execTask(){
        $tasks = \App\Models\Task::limit(1)->where("status","=",'wait')->orderby('id','ASC')->get();
        $c = "\\App\\Services\\Task";
        $c = new $c();
        $chan = new co\Channel(1);
        #$maxLength = count($tasks);
        $index = 0;
        $result = [];
        foreach($tasks as $task){
            #go(function() use ($task,$c,$chan,&$index,&$result){
                $index++;
                $params = json_decode($task->params,true);
                $action = $task->action;
                $result = $c->$action($params);
                $task->status = $result['status'];
                $exec_at = time();
                if(isset($result['interval_time'])){
                    $exec_at += $result['interval_time'];
                }
                $log = @json_decode($task->log,true);
                if(!is_array($log)) $log = [];
                $log[] = ['result'=>$result['result'],'time'=>date("Y-m-d H:i:s",time())];
                $task->log = json_encode($log,JSON_UNESCAPED_UNICODE);
                $task->retry++;
                $task->exec_at = date("Y-m-d H:i:s",$exec_at);
                $task->save();
            #});
        }
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
            $contentType = $request->header['content-type'] ?? '';
            if($contentType == 'application/json'){
                $json = $request->rawContent();
                if($json) $params = json_decode($json,true);
                
            }
            
            $_pathData = array_filter(explode("/",$path_info));
            $pathData = [];
            if($_pathData) foreach($_pathData as $v) $pathData[] = $v;
            $groupName = $pathData[0]??false;
            $controllerName = $pathData[1]??false;
            $actionName = $pathData[2]??false;
            //数据验证
            
            $valideClassName = "App\\Http\\{$groupName}\\Requests\\{$controllerName}\\{$actionName}"; 
            if(class_exists($valideClassName) && $actionName){ 
                $params = $params ?? [];
                $valide = new $valideClassName($params);
                $validator = \Illuminate\Support\Facades\Validator::make($params, $valide->rules(), $valide->messages(), $valide->attributes());
                $failed = $validator->failed();
                $messages = $validator->messages();
                if(count($messages) != 0){
                    $result = [];
                    $swooleResponse->sendJson($result,['code'=>'FORM.VALIDATE','data'=>$messages->toArray()]);
                    return;
                }
            }
            //权限验证
            $valideClassName = "App\\{$groupName}\\Requests\\{$controllerName}\\{$actionName}";
            $checkRoleClassName = "App\\{$groupName}\\Requests\\CheckRole";    //基础接口角色验证
            if(class_exists($valideClassName) && $actionName){ 
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
                        $swooleResponse->sendJson($result,$messages->toArray());
                        return;
                    }
                    if(!class_exists($valideClassName)) break;
                }
                unset($params['permission']);
            }

            //Result
            $controllerName = "App\\Http\\{$groupName}\\Controllers\\{$controllerName}";
            #var_export($controllerName);exit;
            if(class_exists($controllerName)){
                #$request =  Request::capture();
                $controller = app()->make($controllerName);
                $controller->setResponse($swooleResponse);
                $controller->setParams($params);
                //$controller->setUserInfo();
                $_result = $controller->$actionName($request);
                if($_result !== false){
                    $result = $_result;
                    $swooleResponse->sendJson($result);
                }
                return;
            }
            $httpCode = 404;
            $response->status($httpCode);
            #if($swooleResponse->sendFile("status/{$httpCode}.jpeg")) return;

            $response->end($httpCode);
            return;
        });
        $httpServer->on('start', function () {
            $this->test();
        });
        $httpServer->start();
        $this->httpServer = $httpServer;
    }
    private function sendTask(){
        if($this->cache['task_push_lock']) return;
        $this->cache['task_push_lock'] = true;
        $tasks = \App\Models\Task::where("status","wait")->orderby("id","ASC")->limit(10)->get();
        //$this->httpServer->task($tasks);
        #echo $this->httpServer->test;
    }
    private function syncAccount(){
        go(function(){
            $cli = new \Swoole\Coroutine\Http\Client('127.0.0.1', $this->serverConf['httpPort']);
            $cli->set([ 'timeout' => 100]);
            $cli->post("/Channel/Facebook/syncOpenAccount",[]);
            $cli->close();
        });
    }
    private function test(){ 
        $startTime = microtime(true);
        go(function() use ($startTime){
            $cli = new \Swoole\Coroutine\Http\Client('127.0.0.1', $this->serverConf['httpPort']);
            $cli->set([ 'timeout' => 10]);
            #$cli->get("/Manager/role/role_add?loginName=2&password=232");

            $openAccountParams = [
                'apply_id' => 1177,
                'client_id' => '34',
                'user_id' => '1000016',
                'apply_number' => 2,
                'business_license' => 'https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=310278820,3623369202&fm=26&gp=0.png',
                'business_code' => '99999',
                'address_cn' => '上海市',
                'address_en' => 'Shanghai',
                'business_name_cn' => '上海市昆1玉网络有限公司',
//                'business_name_en' => '上海市昆玉网络有限公司',
                'city' => 'Shanghai',
                'state' => 'Shanghai',
                'zip_code' => '000000',
                'contact_email' => 'test@test.com',
                'contact_name' => '测试联系人',
                'timezone_ids' => [42,42],
//                'website' => 'http://www.sinoclick.com',
//                'mobile' => 'http://www.sinoclick.com',
                'mobile' => 13588887777,
                'promotable_urls' => ['http://www.sinoclick.com'],
                'promotable_app_ids' => ['123123','4444'],
                'bind_bm_id' => '111',
                'sub_vertical' => 'TOY_AND_HOBBY',
                'source' => 'SinoClick',
            ];
            #$cli->post("/Channel/Facebook/openAccount",$params);
            $auditParams = [
                'apply_id' => 1177,
                'status' => 'internal_approved',
                'reason' => 'jjjj',
                'sub_vertical' => 'MOBILE_AND_SOCIAL',
            ];
//            $params = [
//                'client_id' => 1,
//                'fields' => 'client_id,vertical,status',
//                'client_type' => 1,
//            ];
//            //同步数据
            $getOeLinkParams = [
                'user_id' => 1000015,
                'client_id' => 1,
            ];
            #$cli->post("/Channel/Facebook/getOeLink",$getOeLinkParams);
            #$cli->post("/Channel/Facebook/getOpenaccountList",$params);
            #$cli->post("/Channel/Facebook/openAccountAudit",$auditParams);
            #$cli->post("/Channel/Facebook/syncOpenAccount",[]);
            #$cli->post("/Channel/Facebook/openAccount",$openAccountParams);
            $cli->post("/Channel/Facebook/syncAllUser",[]);

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