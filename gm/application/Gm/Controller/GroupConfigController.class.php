<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2015/12/22
 * Time: 19:12
 */

namespace Gm\Controller;
use Common\Controller\AdminbaseController;
class GroupConfigController extends AdminbaseController{
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
		$where = array();
		$gid = I('gid');
		if(!empty($gid)){
			$where['gid'] = $gid;
		}
		if(!empty($serverid)) {


			//---得到配置db

			$db_config = gm_server_config($serverid);
			$cfg_group = M("cfg_group", null, $db_config);
			$data = $cfg_group->where($where)->order("id desc")->select();

		}

		$this->assign('posts', $data)->assign('serverid', $serverid)->assign('gid', $gid);
		$this->display();
	}
	public function edit(){
		$data = $_POST;
		$serverid  = $_GET['serverid'];
		$db_config = gm_server_config($serverid);
		$id = $_GET['id'];
		$cfg_group = M("cfg_group", null, $db_config);
		if(IS_POST){

			try{
				$data=$cfg_group->where(array('id'=>$id))->save($data);
			}catch (Exception $e){
				$this->error($e->getMessage());
			}
			$this->success(L('CHANGE_SUCCESS'));
		}
		$id = I('get.id',0);
		$posts =$cfg_group->where(array('id'=>$id))->find();
		$this->assign('posts',$posts);
		$this->display();


	}
	
	/**
	 *  删除
	 */
	function delete(){
		$serverid  = I('get.serverid');
		$id = I("get.id");
	  if(!empty($serverid)){

        		//---得到配置db
        		$db_config = gm_server_config($serverid);
		  		$cfg_group = M("cfg_group", null, $db_config);
        		if ($cfg_group->where(array("id"=>$id))->delete()!==false){
				
					$this->success(L('DELETE_SUCCESS'));
				} else {
					$this->error(L('DELETE_FAIL'));
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
            writeLog('cfg_group_add');
            $data = $_POST;
//             $version = trim($data['version']);
//             $this->_version_number($version);
			$serverid=$_GET['serverid'];
            try{


					//---得到配置db
					$db_config = gm_server_config($serverid);
					$cfg_group = M("cfg_group", null, $db_config);
					$cfg_group->add($data);
            	}catch (Exception $e){
					$this->error($e->getMessage());
            }
				$this->success(L('ADD_SUCCESS'));
        }else{
				$this->display();
        }

    }

}