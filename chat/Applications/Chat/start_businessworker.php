<?php
use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

require_once __DIR__ . '/../../Workerman/Autoloader.php';
Autoloader::setRootPath(__DIR__);

$worker = new BusinessWorker();

$worker->name = 'ChatBusinessWorker';

$worker->count = 4;

$worker->registerAddress = '127.0.0.1:1236';

if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}