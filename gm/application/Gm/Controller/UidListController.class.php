<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2015/12/22
 * Time: 19:12
 */

namespace Gm\Controller;
use Common\Controller\AdminbaseController;
class UidListController extends AdminbaseController{
    function _initialize(){
        parent::_initialize();
    }
    /*
     * 用户列表
     * **/
    public function index(){
		if(empty($serverid)){
			$serverid  = I('server_id');
		}
		if(!empty($serverid)) {


			//---得到配置db
			$db_config = gm_server_config($serverid);
			$uidtable = M("uidtable", null, $db_config);
			$data = $uidtable->where(1)->select();


		}
		$this->assign('posts', $data)->assign('serverid', $serverid);
		$this->display();
	}
	
	/**
	 *  删除
	 */
	function delete(){
		$serverid  = I('get.serverid');
		$uid = I("get.uid");
		redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del('uidtable:'.$serverid);
	  if(!empty($serverid)){

        		//---得到配置db
        		$db_config = gm_server_config($serverid);
		  		$uidtable = M("uidtable",null,$db_config);
        		if ($uidtable->where(array("uid"=>$uid))->delete()!==false){
				
					$this->success(L('DELETE_SUCCESS'));
				} else {
					$this->error(L('DELETE_FAIL'));
				}
			  $data=$uidtable->where(1)->select();
			  $ips = array_column($data, 'uid');
			  foreach($ips as $value){
				  redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->sadd('uidtable:'.$serverid,$value);
			  }

        }else{
        	$this->error(L('DELETE_FAIL'));
        }
        

	}
	
    /*
    * add
    * **/
    public function add(){
        if(IS_POST){
            writeLog('uidlist_add');
            $data = $_POST;
//             $version = trim($data['version']);
//             $this->_version_number($version);
            try{
					$uid=$data['uid'];
					$serverid=$data['server_id'];
				redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del('uidtable:'.$serverid);
					//---得到配置db
					$db_config = gm_server_config($serverid);
					$uidtable = M("uidtable",null,$db_config);
					$uidtable->execute("insert into uidtable(uid) values('$uid')");
            	}catch (Exception $e){
					$this->error($e->getMessage());
            }
			$data=$uidtable->where(1)->select();
			$ips = array_column($data, 'uid');
			foreach($ips as $value){
				redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->sadd('uidtable:'.$serverid,$value);
			}
				$this->success(L('ADD_SUCCESS'));
        }else{
				$this->display();
        }

    }

}