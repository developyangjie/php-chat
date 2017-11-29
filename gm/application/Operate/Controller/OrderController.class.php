<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2015/7/15
 * Time: 19:12
 */

namespace Operate\Controller;
use Common\Controller\AdminbaseController;
class OrderController extends AdminbaseController{
    protected $chatlog_model;
    function _initialize(){
        parent::_initialize();
        $this->chatlog_model = D('Operate/ChatLog');
	}
    public function index(){

			$where = array();
		    $server_id = I('server_id');
		    $uid = I('uid');
		    if($uid){
				$where['uid'] = $uid;
			}
		    $start_time = I('start_time');
		    $end_time = I('end_time');
		    if($start_time && $end_time){
              $where['billts'] = array('between',array(strtotime($start_time),strtotime($end_time)+86399));
			}
			if($server_id){
				$dbs = gm_server_config($server_id);
                $itemids = $this->_server_props($server_id);
				$posts = M('zghm_pay_order', null, $dbs)->where($where)->select();
				foreach($posts as &$value){
					$value['item_name'] = $this->_server_props($value['sid'],$value['itemid']);
				}
				$this->assign("posts", $posts);
			}


		$this->display();
    }
	/*
	 * **/
	private function _server_props($server_id=0,$find=''){
		$config = gm_server_config($server_id);
		$itemids = get_gm_server_props($config);
		$newArr = array();
		foreach($itemids as $item){
			$newArr[$item['itemid']] = $item['name'];
		}
		if($find){
			return $newArr[$find];
		}
		return false;
	}
    /*
     * 添加跑马灯 2015-9-30 dlx
     * **/
    public function add(){

        if(IS_POST){
			writeLog('pmd_add');
			redis_instance()->delete("gm_pmd_data_all");
            $data = $_POST;
			if($id = $data['id']){
			    unset($data['id']);
			}
			$server_id = $data['server_id'];
			$start_time = $data['start_time'];
			$end_time 	= $data['end_time'];
			$interval_time	= (int)$data['interval_time'];

			unset($data['server_id'],$data['start_time'],$data['end_time'],$data['interval_time']);
			$newArr = array();
			foreach($data as $key=>$val){
				foreach($val as $k=>$v){
					$newArr[$k][]=$v;
				}
			}
			$code = json_encode(array_values($newArr));
			$msg = preg_replace("#\\\u([0-9a-f]+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $code);

			$chatlog_data = array(
				'sid' => $server_id,
				'msg' => $msg,
				'ctime'=>time(),

				'start_time'=> strtotime($start_time),
				'end_time'  => strtotime($end_time),
				'interval_time'	=> $interval_time,
			);
			if($id){
				$res = $this->chatlog_model->where("id=".$id)->save($chatlog_data);
			}else{
				$res = $this->chatlog_model->add($chatlog_data);
			}

			if($res){
				$this->success($id?'修改成功':'添加成功',U('Pmd/index'));
			}else{
				$this->error($id?'修改成功':'添加成功');
			}

        }
		if($_GET['id']){
           $ids = $_GET['id'];
			$posts = $this->chatlog_model->where("id=".$ids)->find();
		}
		$this->assign('posts',$posts);
        $this->display();
    }



}