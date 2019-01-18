<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
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
        #$this->init();
        echo "SwooleServer!\n";
        $httpServer = new \Swoole\Http\Server("127.0.0.1", $this->serverConf['httpPort']);
        $httpServer->on('request', function ($request, $response) {
        	#$fd = $request['fd'];
        	#$method = $request['server']['request_method'];
            $controller = 'Test';
            $action = 'test';
            $params = [];
            $controllerName = "\\App\\Http\\Controllers\\{$controller}";

            #echo $controllerName;
            #$this->app->run();
            if($request->server['request_method'] == 'POST'){
                $params = $request->post;
            }
            $controller = new $controllerName();
            $controller->$action($params);
    		$response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
		});
        $httpServer->on('start', function () {
            $this->test();
        });
		$httpServer->start();
		$this->test();
    }
    private function test(){
        $startTime = microtime(true);
        go(function() use ($startTime){
            $cli = new \Swoole\Coroutine\Http\Client('127.0.0.1', 9501);
            $cli->setHeaders([
                'Host' => "localhost",
                "User-Agent" => 'Chrome/49.0.2587.3',
                'Accept' => 'text/html,application/xhtml+xml,application/xml',
                'Accept-Encoding' => 'gzip',
            ]);
            $cli->set([ 'timeout' => 1]);
            $cli->post('/',['test'=>123]);
            #echo $cli->body;
            echo 'exec time : ';
            echo (microtime(true) - $startTime);
            $cli->close();
        });

    }
    private function init(){
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
        $app->router->group([
            'namespace' => '\App\Http\Controllers',
        ], function ($router) use ($base_dir) {
            require $base_dir.'/routes/web.php';
        });

        #$app->run();
        $this->app = $app;
    }
}