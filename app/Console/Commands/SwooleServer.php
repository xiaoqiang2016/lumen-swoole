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
    private $app;
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
            $controllerName = "\\App\\Http\\Controllers\\{$controller}";
            $this->app = $this->getApp();
            $this->app->response = $response;
            #echo $controllerName;
            \App\Common\Helper::runTimeStart();
            $this->app->run();
//            $request = \Laravel\Lumen\Http\Request::capture();
//            $controller = $this->app->make($controllerName);
//            $controller->setResponse($response);
//            $controller->$action($params);
            #$response->end("123");
            #sleep(1);
    		#$response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
		});
        $httpServer->on('start', function () {
            $this->test();
        });
//        go(function(){
//            echo '111';
//            Co::sleep(1);
//
//        });
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
            $cli->post("/Channel/getAdAccountList",[]);
            echo PHP_EOL.'Result:'.PHP_EOL;
            $result = $cli->body;
            print_r($result);
            echo PHP_EOL;
            echo 'exec time : ';
            echo (microtime(true) - $startTime);
            $cli->close();
        });
    }
    private function test2(){
        $startTime = microtime(true);
        $token = 'CAAUibl40bIcBAJkRAVZBTk8NkN4U36hrmRpUE4uyR3txrmzmKTybxRSSZBBMz3VNDZABKtXbbbZAqiGUFUz6pmJ0ZA2jbBLrioPEoz4sm1FYjmakfeOKfZCQOnzIZCOyqIZBTaXJaRWC8b0kb1v2lrVUHye9m7uq8F9dCOTJZBuz1Lq61HZCmhsypslk1ZA0aRqjT8ZD';
        $urls = [];
        $url = 'https://graph.facebook.com/v3.0/act_259047808269458/ads?access_token=CAAUibl40bIcBAJkRAVZBTk8NkN4U36hrmRpUE4uyR3txrmzmKTybxRSSZBBMz3VNDZABKtXbbbZAqiGUFUz6pmJ0ZA2jbBLrioPEoz4sm1FYjmakfeOKfZCQOnzIZCOyqIZBTaXJaRWC8b0kb1v2lrVUHye9m7uq8F9dCOTJZBuz1Lq61HZCmhsypslk1ZA0aRqjT8ZD&pretty=0&fields=id%2Cname%2Cstatus%2Caccount_id%2Ccampaign_id%2Cadset_id&limit=25&after=MjM4NDMwMzUzNzAwMjAxOTYZD';
        for($i=0;$i<50;$i++) $urls[] = $url;
          
        go(function() use ($startTime,$urls){
            $cli = new \Swoole\Coroutine\Http\Client('127.0.0.1', 9501);
            #$cli = new \Swoole\Coroutine\Http\Client('lumen-swoole.levy.com', 80);
            $cli->set([ 'timeout' => 10]);
            $params = [];
            $controller = 'Channel';
            $action = 'getCompaignList';
            #$params['params'] = $urls;
            $p = [];
            $p['controller'] = 'Channel';
            $p['action'] = 'getCampaignList';
            $p['params'] = [
                'channel_id' => 'Facebook',
                'account_id' => 'act_1634320873332648',
            ];
            $params[] = $p;
            $p = [];
            $p['controller'] = 'Channel';
            $p['action'] = 'getCampaignList';
            $p['params'] = [
                'channel_id' => 'Facebook',
                'account_id' => 'act_1634320869999315',
            ];
            $params[] = $p;
            #echo "/{$controller}/{$action}";
            $cli->post("/multi",$params);
            echo PHP_EOL.'Result:'.PHP_EOL;
            $result = $cli->body;
            print_r($result);
            echo PHP_EOL;
            echo 'exec time : ';
            echo (microtime(true) - $startTime);
            $cli->close();
        });

    }
    private function getApp(){
        $base_dir = dirname(dirname(dirname(__DIR__)));
        #$_SERVER['REQUEST_METHOD'] = 'GET';
        #$_SERVER['REQUEST_URI'] = 'test1';
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
        return $app;
    }
}