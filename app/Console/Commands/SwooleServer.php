<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Co;
#use Swoole;
class SwooleServer extends Command{
	protected $signature = 'SwooleServer';
    protected $description = 'Swoole Server.';

    private $serverConf = [
    	'httpPort' => 9501,
    ];
    private $app = false;
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
//        echo "************************\n";
//        echo "*                      *\n";
//        echo "*     SwooleServer     *\n";
//        echo "*                      *\n";
//        echo "************************\n";
        $this->initApp();
        $httpServer = new \Swoole\Http\Server("0.0.0.0", $this->serverConf['httpPort']);
        $httpServer->set(array(
            'worker_num' => 6,    //worker process num
            'backlog' => 128,   //listen backlog
            'max_request' => 5000,
            'dispatch_mode'=>1,
            'max_coroutine' => 9999999,
        ));
        $httpServer->on('request', function ($request, $response) {
            $_SERVER = $this->parseRequest($request);
            if(!empty($request->post)){
                $_POST = $request->post;
            }
        	$request_method = $request->server['request_method'];
        	$params = [];
            $controller = 'Main';
            $action = 'index';
//            if($request_method == 'POST'){
//                $post_data = $request->post;
//
//                $controller = $post_data['controller'];
//                $action = $post_data['action'];
//                $params = $post_data['params'];
//                unset($params['controller'],$params['action']);
//            }
            app()->response = $response;
            #echo $controllerName;
            #\App\Common\Helper::runTimeStart();
            try{
                app()->run();
            }catch (Exception $e){

            }


            return;
//            $request = \Laravel\Lumen\Http\Request::capture();
//            $controller = $this->app->make($controllerName);
//            $controller->setResponse($response);
//            $controller->$action($params);
            #$response->end("123");
            #sleep(1);
    		#$response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
		});
        $httpServer->on('start', function () {
            //$this->test();
        });
		$httpServer->start();


		#$this->test();
    }
    private function parseRequest($request){
        $result = $_SERVER;
        $result['REQUEST_METHOD'] = $request->server['request_method'];
        $result['REQUEST_URI'] = $request->server['request_uri'];
        if($result['REQUEST_METHOD'] == 'POST'){
            $result['_POST'] = $request->post;
        }
        return $result;
    }
    private function test(){
        $startTime = microtime(true);
        go(function() use ($startTime){
            $cli = new \Swoole\Coroutine\Http\Client('127.0.0.1', $this->serverConf['httpPort']);
            $cli->set([ 'timeout' => 10]);
            $cli->get("/Channel/getAdAccountList");
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