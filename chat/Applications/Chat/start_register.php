<?php
use \Workerman\Worker;
use \GatewayWorker\Register;

require_once __DIR__ . '/../../Workerman/Autoloader.php';

$register = new Register('text://0.0.0.0:1236');

if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}