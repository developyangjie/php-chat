<?php
use \Workerman\Worker;
use \GatewayWorker\Gateway;
use \Workerman\Autoloader;

require_once __DIR__ . '/../../Workerman/Autoloader.php';
Autoloader::setRootPath(__DIR__);

$gateway = new Gateway("Websocket://0.0.0.0:8383");

$gateway->name = 'ChatGateway';

$gateway->count = 8;

$gateway->lanIp = '127.0.0.1';

$gateway->startPort = 4000;

$gateway->pingInterval = 10;

$gateway->pingData = '{"type":"ping"}';

$gateway->registerAddress = '127.0.0.1:1236';

if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}