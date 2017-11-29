<?php

/**
 * 验证码处理
 */
namespace Api\Controller;
use Think\Controller;
class CronController extends Controller {

    public function index() {
    	$result = get_pmd_data();
		set_time_limit(0);
		$temp = array();
		while (true) {
			foreach ($result as $key => $val) {
				if ($val['start_time'] <= time() && $val['end_time'] > time()) {
					$db_config = gm_server_config($val['sid']);
					$interval_time = $val['interval_time'];
					M('log_sys_chat', null, $db_config)->add(array('msg' => $val['msg'], 'ts' => time()));
					if(isset($temp[$val['sid']])){
						sleep($val['interval_time']*60);
					}else{
						sleep($val['interval_time']*60);
					}

				}
			}

		}

	}
    

}

