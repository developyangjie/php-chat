<?php
namespace Config;

class Store
{
    const DRIVER_FILE = 1;
    const DRIVER_MC = 2;
    const DRIVER_REDIS = 3;

    public static $driver = self::DRIVER_REDIS;
    
    public static $gateway = array(
        '172.27.0.18:6379',
    );
    
    public static $room = array(
        '172.27.0.18:6379',
    );
    
    public static $user = array(
        '172.27.0.18:6379'
    );

    public static $password = 'opbt2aepn4AetGdw';
    
    public static $storePath = '';
}

Store::$storePath = sys_get_temp_dir().'/chatserver/';
