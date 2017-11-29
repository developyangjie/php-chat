<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2015/7/15
 * Time: 19:12
 */

namespace Operate\Controller;
use Common\Controller\AdminbaseController;
use Think\Exception;

class PmdController extends AdminbaseController{
    protected $chatlog_model;
	protected $serverlist_model;
    function _initialize(){
        parent::_initialize();
        $this->chatlog_model = D('Operate/ChatLog');
		$this->serverlist_model = M('server_list',null,DB_CONFIG_PLATFORM);
	}
    public function index(){
		/*$server_id = I('server_id');
		$posts=redis_instance()->get('chat_sys_w:'.$server_id);
// 		$page = $this->page($count,20);
// 		$this->assign("Page", $page->show('Admin'));
		if(isset($posts))
		{
			$this->assign("posts",  (Array)json_decode($posts))->assign("sid",$server_id);
		}*/
		$this->display('add');
    }
    /*
     * 添加跑马灯 2015-9-30 dlx
     * **/
    public function add(){

        if(IS_POST){
            writeLog('notice_add');
            $data = $_POST;
            $serverid = $data['server_id'];
            if(!isset($serverid)){
                $this->error(L('PLEASE_SELECT_SERVER'));
            }
            unset($data['server_id']);
            $serverList = array();
			foreach ($serverid as $sid)
			{
				$server = $this->serverlist_model->where(array('id'=>$sid))->find();
				$serverList[$sid] = $server;
				if(empty($server['chatredisip'] || $server['chatredisport'])){
					$this->error('服务器('.$sid.')Redis配置错误！');
				}
			}
			foreach ($serverList as $sid=>$server){
				redis_instance($server['chatredisip'],$server['chatredisport'],$server['chatredisauth'])->LPUSH('chat_sys_w:'.$sid, $data['pmd_text']);
			}
            $this->success(L('SEND_SUCCESS'));
        }else{
            $this->display();
        }

    
    }
	/*
	 * edit
	 * **/
	public function edit(){
		if(IS_POST){
			writeLog('pmd_edit');
			$this->_data();
		}
		$ids = I('get.id',0);
		if(empty($ids)) $this->error('参数错误！');
		$posts = $this->chatlog_model->where("id =".$ids)->find();
		$this->assign('posts',$posts);
		$this->display('add');
	}

	private function _data(){
		$data = $_POST;
		if($id = $data['id']){
			unset($data['id']);
		}
		if(empty($data['server_id'])) $this->error('请选择区服');
		$server_id = implode(',',$data['server_id']);

		$start_time = $data['start_time'];
		$end_time 	= $data['end_time'];

		$interval_time	= (int)$data['interval_time'];

		//---当修改页面本身有内容时-
		$old_content =$data['old_content'];
		$old_size = $data['old_size'];
		$old_rgb_value = $data['old_rgb_value'];
		if($old_content && $old_size && $old_rgb_value) {
			$content_data = array(
				'content' => $old_content,
				'size' => $old_size,
				'rgb_value' => $old_rgb_value
			);
		}
		if($data['content'] && $data['size'] && $data['rgb_value']){
			$param = array(
				'content'=>$data['content'],
				'size'=>$data['size'],
				'rgb_value'=>$data['rgb_value']
			);
		}

		$newArr = array();
		if(empty($content_data)){
			foreach($param as $key=>$val){
				foreach($val as $k=>$v){
					$newArr[$k][]=$v;
				}
			}

		}elseif(!empty($content_data) && !empty($param)){
			$new_data['content'] = array_merge($content_data['content'],$param['content']);
			$new_data['size'] = array_merge($content_data['size'],$param['size']);
			$new_data['rgb_value'] = array_merge($content_data['rgb_value'],$param['rgb_value']);
			foreach($new_data as $key=>$val){
				foreach($val as $k=>$v){
					$newArr[$k][]=$v;
				}
			}
		}else{
			foreach($content_data as $key=>$val){
				foreach($val as $k=>$v){
					$newArr[$k][]=$v;
				}
			}
		}

		foreach($newArr as $key=>&$item){
			$item[1] = intval($item[1]);
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
		try{
			if($id){
				$this->chatlog_model->where("id =".$id)->save($chatlog_data);
			}else{
				$this->chatlog_model->add($chatlog_data);
			}
		}catch (Exception $e){
			$this->error($id?'修改失败':'添加失败');
		}

		$this->success($id?'修改成功':'添加成功',U('Pmd/index'));

	}

   /*
    * 批量修改间隔时间
    * **/
	public function edit_int_time(){
		if(IS_POST){
			writeLog('edit_int_time');
			$interval_time = $_REQUEST['interval_time'];
			if(empty($interval_time) || $interval_time==0){
				die(json_encode(array('status'=>'error','data'=>'请填写正确的时间!')));
			}
			try {
				$this->chatlog_model->execute("update ".C('DB_PREFIX')."chat_log set interval_time=".$interval_time);
			} catch (Exception $e) {
				echo $e->getMessage();
				die(json_encode(array('status'=>'error','data'=>'更改失败!')));
			}
            die(json_encode(array('status'=>'success','data'=>'更改成功!')));


		}else{
			$this->display();
		}

	}
	/*
	 * 批量更改结束时间
	 * ***/
	public function edit_more_end_time(){
		if(IS_POST){
			writeLog('edit_more_end_time');
			$end_time = $_POST['end_time'];
			if(empty($end_time)){
				$this->error('时间不能为空！');
			}
			try{
				$this->chatlog_model->execute("update ".C('DB_PREFIX')."chat_log set end_time=".strtotime($end_time));
			}catch (Exception $e){
				$this->error($e->getMessage());
			}
			$this->success('更改成功');
		}else{
			$this->display();
		}

	}
	/*
	 * delete
	 * **/
	public function delete(){
		$id = I('id');
		if(empty($id)){
			$this->error('参数错误！');
		}
		try{
			writeLog('pmd_delete');
			$this->chatlog_model->where("id=".$id)->delete();
		}catch (Exception $e){
			$this->error($e->getMessage());
		}
		$this->success('删除成功!');
	}
}