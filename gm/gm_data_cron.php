<?php

	header("Content-type: text/html; charset=utf-8");

	/*

	***5分钟**更新一下monitor�?

	****sid�?�?代表所有的区服-

	*start-mysql_pconnect

	*/

	$db_connect = mysql_pconnect("127.0.0.1","root","") or die("Unable to connect to the MySQL!");

	mysql_query("set character set 'utf8'");

    mysql_query("set names 'utf8'");

    

	/*

	*选择游戏数据�?*

	**table tb_servers 获取游戏区服数据库配�?
	**

	**/

	mysql_select_db("sggj_app1",$db_connect);

	

	$result = mysql_query("select id,dbhost,dbname,dbuser,dbpass FROM tb_servers");

	
      

	while($row=mysql_fetch_assoc($result)){

		$server_id = $row['id'];

		unset($row['id']);

		$arr2[$server_id]=$row;

	}

	

	/*

	*定义区服数据库配�?*

	***连接操作***

	**/

	$db_connect_arr = array();

	foreach($arr2 as $k=>$v){

		$db_connect_arr[$k] = mysql_connect($v['dbhost'],$v['dbuser'],$v['dbpass']) or die("Unable to connect to the MySQL!");

	}

	

	ignore_user_abort(true);

	set_time_limit(0);

	while (true) {

		try {

			/*

			*syx_chat_log***获取需要发送的公告数据***

			****开始时间，结束时间 在当前范围内****

			***/

			$temp = array();

			mysql_select_db('zghm_gm',mysql_instance());

			$newResult=array();

			

			$result=mysql_query("select id,sid,msg,start_time,end_time,interval_time FROM syx_chat_log where start_time<UNIX_TIMESTAMP() and end_time>UNIX_TIMESTAMP() order by id asc,sid asc");

			while($row=mysql_fetch_assoc($result)){

				$newResult[$row['sid']][]=$row;

			}

			

			

			if(!empty($newResult)){

				/*

				*拼接一个三维数�?
				*目的多区服一起发�?**

				*/

				foreach($newResult as $key =>$value){

					foreach($value as $k=>$v){

						$arr3[$k][$key]=$v;

					}

				}

				

				foreach ($arr3 as $key => $val) {

					foreach($val as $k=>$temp){

						/*

						*连接游戏区服数据�?**

						***/

						mysql_select_db($arr2[$temp['sid']]['dbname'], $db_connect_arr[$temp['sid']]);

						/*

						*公告插入到对应的游戏区服数据�?
						**/

						mysql_query("INSERT INTO log_sys_chat (msg,ts) VALUES('{$temp['msg']}',UNIX_TIMESTAMP())");

						

					    /*

						*执行完一�?以上一条的休眠时间***

						**/

						if($k==1){

						sleep(intval($temp['interval_time']) * 60);

						}

					}

					

				}

				

			}else{

				sleep(60);

			}

			



		} catch (Exception $e) {

			echo $e->getMessage() . "\n";

		}

		

	}

	

		

?>