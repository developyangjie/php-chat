<?php
/**
 * Created by PhpStorm.
 * User: viki
 * Date: 15/8/26
 * Time: 01:12
 */
use \Workerman\Worker;
use \Workerman\Autoloader;
use \Workerman\Lib\Timer;

require_once __DIR__.'/../../Workerman/Autoloader.php';
Autoloader::setRootPath(__DIR__);

$worker = new Worker();
$worker->name = 'ChatBroadcastWorker';
$worker->count = 1;
$worker->registerAddress = '127.0.0.1:1236';

$worker->onWorkerStart = function($worker) {
    $servers = \Config\Db::$servers;
    $sids = array();
    foreach($servers as $server) {
        $sids[] = $server['sid'];
    }
    Timer::add(2,array('Events', 'broadcast_to_room'),array($sids));
    Timer::add(5,array('Events', 'robot_chat'),array($sids));
};

$worker->onError = function($connection, $code, $msg)
{
    echo "error $code $msg\n";
};

if(!defined('GLOBAL_START')) {
    Worker::runAll();
}