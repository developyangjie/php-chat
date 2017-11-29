<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2015/12/22
 * Time: 19:12
 */

namespace Gm\Controller;
use Common\Controller\AdminbaseController;
class ServerStateController extends AdminbaseController{
	protected $server_state;
    function _initialize(){
        parent::_initialize();
		$this->server_state = M('server_state',null,DB_CONFIG_PLATFORM);
    }
    /*
     * 用户列表
     * **/
    public function index(){
		$where = array();
		$type = I('type');
		if(!empty($type)){
			$where['type'] = array('like','%'.$type.'%');
		}
		$server_list = $this->server_state->where($where)->order("clientver desc")->select();
		$this->assign('post',$server_list);
		$this->display();
	}
	
	/**
	 *  删除
	 */
	public function delete(){
		if(isset($_GET['clientver'])){
			writeLog('delete');

			$id = I('get.clientver');
			$type = I('get.type');
			try{
				$this->server_state->where(array('clientver'=>$id,'type'=>$type))->delete();
				$this->delete_redis_key();
			}catch (Exception $e){
				$this->error($e->getMessage());
			}
			$this->success(L('DELETE_SUCCESS'));
		}
	}
	
    /*
    * add
    * **/
	public function add(){
		if(IS_POST){
			writeLog('adds');
			$data = $_POST;
			try{
				$this->server_state->add($data);
				$this->delete_redis_key();
			}catch (Exception $e){
				$this->error($e->getMessage());
			}
			$this->success(L('ADD_SUCCESS'));
		}
		$this->display();
	}

	public function edit(){
		if(IS_POST){
			writeLog('edits');
			$data = $_POST;
			$clientver = $data['clientver'];
			$type = $data['type'];
			try{
				$this->server_state->where(array('clientver'=>$clientver,'type'=>$type))->save($data);
				$this->delete_redis_key();
			}catch (Exception $e){
				$this->error($e->getMessage());
			}
			$this->success(L('CHANGE_SUCCESS'));
		}
		$id = I('get.clientver',0);
		$type = I('get.type');
		$posts =  $this->server_state->where(array('clientver'=>$id,'type'=>$type))->find();
		$this->assign('posts',$posts);
		$this->display();
	}
	private function delete_redis_key(){
		redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del('server_state');
	}
}