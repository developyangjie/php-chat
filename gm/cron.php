<?php
header("Content-type: text/html; charset=utf-8");
error_reporting(E_ERROR);
/*
***指定时区***
*start-connect_mysql
*/
/*连接地址**/
//define('DB_HOST', "10.66.150.179", true);//数据库用户名
//define('DB_USERNAME', "root", true);//-数据库连接用户-
//define('DB_PASSWORD', "zXFyPVSKSTTm", true);//-密码
/*连接游戏数据库*/
//define('DB_DATABASESERVER', "zghm_haima_servers", true);
/*连接GM后台数据库*/
//define('DB_DATABASEGM', "zghm_gm", true);

//--本地测试地址--
define('DB_HOST', "127.0.0.1", true);//数据库用户名
define('DB_USERNAME', "root", true);//-数据库连接用户-
define('DB_PASSWORD', "", true);//-密码
/*连接游戏数据库*/
//define('DB_DATABASESERVER', "hyyr_data", true);
/*连接GM后台数据库*/
define('DB_DATABASEGM', "hyyr_data", true);


$db_connect = mysql_pconnect(DB_HOST, DB_USERNAME, DB_PASSWORD) or die("Unable to connect to the MySQL!");

mysql_query("set character set 'utf8'");
mysql_query("set names 'utf8'");

/*
*选择游戏数据库**
**table tb_servers 获取游戏区服数据库配置
**
**/
mysql_select_db(DB_DATABASEGM, $db_connect);

$result = mysql_query("select id,dbhost,dbname,dbuser,dbpass FROM tb_servers");

$arr2 = array();
while ($row = mysql_fetch_assoc($result)) {
    $server_id = $row['id'];
    unset($row['id']);
    $arr2[$server_id] = $row;
}
mysql_free_result($result);
/*
*定义区服数据库配置**
***连接操作***
**/
$db_connect_arr = array();
foreach ($arr2 as $k => $v) {
    $db_connect_arr[$k] = mysql_pconnect($v['dbhost'], $v['dbuser'], $v['dbpass']);
    mysql_query("set character set 'utf8'");
    mysql_query("set names 'utf8'");
}

function mkdirs($dir, $mode = 0777)
{
    if (is_dir($dir) || @mkdir($dir, $mode))
        return TRUE;

    if (!mkdirs(dirname($dir), $mode))
        return FALSE;

    return @mkdir($dir, $mode);
}

ignore_user_abort(true);
set_time_limit(0);
$sidinfo = array();
while (true) {
    try {
        /*
        *syx_chat_log***获取需要发送的公告数据***
        ****between and start_time-end_time****
        ***/
        $temp = array();
        $res = mysql_select_db(DB_DATABASEGM, $db_connect);

        $newResult = array();
        $result = mysql_query("select id,sid,msg,start_time,end_time,interval_time FROM syx_chat_log where start_time<UNIX_TIMESTAMP() and end_time>UNIX_TIMESTAMP() order by id asc,sid asc", $db_connect);
        
        while ($row = mysql_fetch_assoc($result)) {
            $newResult[] = $row;
        }
		//有问题时打开
		//$dir = "log";
		//mkdirs($dir);
		//$fileName = $dir.'/Log_' . date('Y-m-d') . '.log';
		//$file = fopen($fileName, 'a+');
		//fwrite($file, "[time:" . date('Y-m-d H:i:s') . "]; Url:" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ";msg:".json_encode($newResult). "\r\n");
		//fclose($file);
		
		mysql_free_result($result);
        if (!empty($newResult)) {
            foreach ($newResult as $key => $value) {
				//sid 逗号隔开存储
				$sidinfo = explode(',',$value['sid']);
				foreach($sidinfo as $server_ids){
					mysql_select_db($arr2[$server_ids]['dbname'], $db_connect_arr[$server_ids]);
					/*
					*公告插入到对应的游戏区服数据库
					**/
					$sql = "INSERT INTO log_sys_chat (msg,ts) VALUES('" . $value['msg'] . "',UNIX_TIMESTAMP())";
					mysql_query($sql, $db_connect_arr[$server_ids]);
				}
				sleep(intval($value['interval_time']) * 60);
            }
			
        } else {
			sleep(60);
        }


    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

}


?>