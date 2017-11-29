<?php
use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

require_once __DIR__ . '/../../Workerman/Autoloader.php';
Autoloader::setRootPath(__DIR__);

$web = new WebServer("http://0.0.0.0:55252");
$web->name = 'WebServer';
$web->count = 1;
$web->addRoot('www.your_domain.com', __DIR__.'/Web');

if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}