<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2015/12/23
 * Time: 19:12
 */

namespace Gm\Controller;
use Common\Controller\AdminbaseController;
use Think\Exception;

class ServerNoticeController extends AdminbaseController{
        protected $serverlist_model;
    function _initialize(){
        parent::_initialize();
        $this->serverlist_model = M('server_list',null,DB_CONFIG_PLATFORM);

    }
    public function index(){
        $where = array();
        $name = I('name');
        if(!empty($name)){
            $where['name'] = array('like','%'.$name.'%');
        }
        $server_list = $this->serverlist_model->where($where)->select();
        $this->assign('posts',$server_list);
        $this->display();
    }

    public function edit(){
        if(IS_POST){
            writeLog('server_edit');
            $data = $_POST;
            $id = $data['id'];
            unset($data['id']);
            try{
                $this->serverlist_model->where(array('id'=>$id))->save($data);
                $this->delete_redis_key();
            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success(L('CHANGE_SUCCESS'));
        }
        $id = I('get.id',0);
        $posts =  $this->serverlist_model->where(array('id'=>$id))->find();
        $this->assign('posts',$posts);
        $this->display();


    }
    private function delete_redis_key(){
        redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del('serverList');
        redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del('serverListByIdAsc');
    }

}