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
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        echo "SwooleServer!\n";
        $httpServer = new \Swoole\Http\Server("0.0.0.0", $this->serverConf['httpPort']);
        $httpServer->on('request', function ($request, $response) {
        	$fd = $request['fd'];
        	$method = $request['server']['request_method'];
    		$response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
		});
		$httpServer->start();
    }
}