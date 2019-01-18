<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
#use Swoole;

class TestCommand extends Command{
	protected $signature = 'TestCommand';
    protected $description = '测试.';

    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $this->init();
        return;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1:9501');
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);

        $params = [];
        for($i=0;$i<100;$i++){
            $params[] = 'https://www.baidu.com/';
        }
        $postData = [
            'action' => 'multi',
            'method' => 'curl',
            'params' => $params,
        ];
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
        $data = curl_exec($curl);
        curl_close($curl);
        print_r($data);
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

        $app->run();
    }
}