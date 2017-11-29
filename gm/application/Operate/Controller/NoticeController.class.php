<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2015/12/23
 * Time: 19:12
 */

namespace Operate\Controller;
use Common\Controller\AdminbaseController;
use Think\Exception;

class NoticeController extends AdminbaseController{
    function _initialize(){
        parent::_initialize();

    }
    public function index(){
		/*
		*直接获取游戏服务器数据*
		*/
		
        $serverid = I('server_id');
        if(!empty($serverid)){
            $db_config =gm_server_config($serverid);
            $id = I('id');
            $where = array();
            if(!empty($id)){
                $where['id'] = $id;
            }
            $count = M('server_news',null,$db_config)->where($where)->count();
            $posts = M('server_news',null,$db_config)->where($where)->select();
            $this->assign('posts',$posts)
                ->assign('count',$count)
                ->assign('serverids',$serverid)
                ->assign('posts_json',json_encode($posts));
        }
        $this->display();
    }

    public function edit(){
        if(IS_POST){
            writeLog('notice_edit');
            $data = $_POST;
            $data['startts']       = strtotime($data['startts']);
            $data['endts']          = strtotime($data['endts']);
            $serverid = $data['serverid'];
            $id = $data['id'];
            unset($data['serverid'],$data['id']);
            $db_config =gm_server_config($serverid);
            try{
               M('server_news',null,$db_config)->where(array('id'=>$id))->save($data);
            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success('更改成功！');
        }else{
            $serverid = I('get.serverid',0);
            $id = I('get.id',0);
            if(!isset($serverid) || !isset($id)){
                $this->error('参数错误!');
            }
            $db_config =gm_server_config($serverid);
            $posts = M('server_news',null,$db_config)->where(array('id'=>$id))->find();
            $posts['sid'] = $serverid;
            $this->assign('posts',$posts);
            $this->display();
        }


    }
    /*
     * 公告
     * ***/
    public function add(){
        if(IS_POST){
            writeLog('notice_add');
            $data = $_POST;
            $data['startts']       = strtotime($data['startts']);
            $data['endts']          = strtotime($data['endts']);
            $serverid = $data['server_id'];
            if(!isset($serverid)){
                $this->error('请选择服务器!');
            }
            unset($data['server_id']);
            try{
            	foreach ($serverid as $sid)
            	{
            		$newArr=$data['news'];
					redis_instance()->SET('chat_sys_w:'.$sid,serialize($newArr));
            	}
            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success('添加成功!');
        }else{
            $this->display();
        }

    }
}