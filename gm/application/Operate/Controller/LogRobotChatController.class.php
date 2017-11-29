<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2015/7/15
 * Time: 19:12
 */

namespace Operate\Controller;
use Common\Controller\AdminbaseController;
use Think\Exception;

class LogRobotChatController extends AdminbaseController{
    protected $chatlog_model;
    function _initialize(){
        parent::_initialize();
        //$this->chatlog_model = M('log_robot_chat');
	}
    public function index(){

        $where = array();
		$server_id = I('server_id');
		$config = array();
		if($server_id){
			try{
				$config = gm_server_config($server_id);
				$log_robot_chat = M('log_robot_chat',null,$config);
				$count= $log_robot_chat->where($where)->count();
				$page = $this->page($count,20);
				$posts = $log_robot_chat->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
				$posts_json = $log_robot_chat->where($where)->select();
			}catch (Exception $e){
				echo $e->getMessage();
			}
			$this->assign("Page", $page->show('Admin'));
			$this->assign("posts", $posts);
			$this->assign("posts_json", json_encode($posts_json));
			$this->assign("count", $count);

		}

		$this->display();
    }
	/*
     * 添加跑马灯 2015-9-30 dlx
     * **/
    public function add(){
		if(IS_POST){
			writeLog('log_robot_chat_add');
			$data = $_POST;
			$server_id = $data['server_id'];
			if(empty($server_id)){
				$this->error('请选择区服!');
			}
			unset($data['server_id']);

			$filename = $_FILES['csv']['tmp_name'];
			/*
			 * csv 存在的情况下
			 * **/
			if (!empty ($filename)) {
				$handle = fopen($filename, 'r');
				$result = $this->input_csv($handle); //解析csv
				$len_result = count($result);
				if($len_result==0){
					$this->error('没有任何数据!');
				}
				$data_values = '';
				for ($i = 1; $i < $len_result; $i++) { //循环获取各字段值
					$uname = iconv('gb2312', 'utf-8', $result[$i][0]); //中文转码
					$msg = iconv('gb2312', 'utf-8', $result[$i][4]);
					$ujob = $result[$i][1];
					$uvip = $result[$i][2];
					$utitle = $result[$i][3];
					$flag = $result[$i][5];
					$data_values .= "('$uname','$ujob','$uvip','$utitle','$msg','$flag'),";
				}
				$data_values = substr($data_values,0,-1); //去掉最后一个逗号
				fclose($handle); //关闭指针
				try {
					foreach ($server_id as $item) {
						$config = gm_server_config($item);
						M('log_robot_chat', null, $config)->execute("insert into log_robot_chat (uname,ujob,uvip,utitle,msg,flag) values $data_values");
					}
				}catch (Exception $e){
					$this->error('添加失败');
				}
				$this->success('添加成功',U('Pmd/index'));

			}else{
				/*
				 * csv 不存的情况下直接出入数据
				 * **/
				if(empty($data['uname'])) $this->error('请填写玩家名称！');
				if(empty($data['msg'])) $this->error('请填写聊天内容！');
				try{
					/*
					 * 多区服插入
					 * **/
					foreach($server_id as $item){
						$config = gm_server_config($item);
						M('log_robot_chat',null,$config)->add($data);
					}
				}catch (Exception $e){
					$this->error('添加失败');
				}

				$this->success('添加成功',U('Pmd/index'));

			}

		}
		$this->display();
    }
   /*
    * 导入csv
    * **/
	private function input_csv($handle) {
		$out = array ();
		$n = 0;
		while ($data = fgetcsv($handle, 10000)) {
			$num = count($data);
			for ($i = 0; $i < $num; $i++) {
				$out[$n][$i] = $data[$i];
			}
			$n++;
		}
		return $out;
	}
	/*
	 * edit
	 * **/
	public function edit(){
		if(IS_POST){
             $data = $_POST;
			 $id = $data['id'];
			 $server_id = $data['server_id'];
			 if(empty($server_id)) $this->error("区服ID 不能为空!");
			 if(empty($id)) $this->error('ID 不能为空！');
			 writeLog('log_robot_chat_edit');
			 unset($data['server_id'],$data['id']);
			 try{
				 $dbconfig = gm_server_config($server_id);
				 M('log_robot_chat',null,$dbconfig)->where('id='.$id)->save($data);
			 }catch (Exception $e){
				 $this->error($e->getMessage());
			 }
			$this->success('更改成功!');

		}else{
			$server_id = I('server_id');
			$id        = I('id');
			if(empty($server_id) || empty($id)) {
				$this->error('区服ID 或者 ID不存在!');
			}
			$dbconfig = gm_server_config($server_id);
			$posts = M('log_robot_chat',null,$dbconfig)->where("id=".$id)->find();
			$posts['server_id'] = $server_id;
			$this->assign('posts',$posts);
			$this->display();
		}
	}

	/*
	 * delete
	 * **/
	public function delete(){
		$id = I('id');
		$server_id = I('server_id');
		if(empty($server_id) || empty($id)) {
			$this->error('区服ID 或者 ID不存在!');
		}
		try{
			writeLog('log_robot_chat_delete');
			$dbconfig = gm_server_config($server_id);
			$posts = M('log_robot_chat',null,$dbconfig)->where("id=".$id)->delete();
		}catch (Exception $e){
			$this->error($e->getMessage());
		}
		$this->success('删除成功!');
	}
}