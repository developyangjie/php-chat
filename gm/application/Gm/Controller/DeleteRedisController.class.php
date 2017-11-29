<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2015/12/22
 * Time: 19:12
 */

namespace Gm\Controller;

use Common\Controller\AdminbaseController;
class DeleteRedisController extends AdminbaseController{
	function _initialize(){
        parent::_initialize();

    }
    /*
     * 用户列表
     * **/
    public function index(){

			$this->display();
	}
	public function delete_redis_del(){
		$keys = $_GET['keys'];
		$sum='0';
		if(strstr($keys, "*")) {
			$key = redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->keys("$keys");
			if (!empty($key)) {
				foreach ($key as $item) {
					$return=redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del($item);
					$sum+=$return;

				}
			}
		} else {
			if (!empty($key)) {
				$key = redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->keys($keys."*");
				foreach ($key as $item) {
					$return=redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del($item);
					$sum+=$return;

				}
			}

		}
		header('Content-Type:application/json; charset=utf-8');
		exit(json_encode($sum));
	}
}