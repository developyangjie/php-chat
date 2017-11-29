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

class PlaysNoticeController extends AdminbaseController{
        protected $servernotice_model;
        protected $serverlist_model;
    function _initialize(){
        parent::_initialize();
        $this->servernotice_model = M('server_notice',null,DB_CONFIG_PLATFORM);
        $this->serverlist_model = M('server_list',null,DB_CONFIG_PLATFORM);


    }
    public function index(){
        $where = array();
        $name = I('name');
        if(!empty($name)){
            $where['name'] = array('like','%'.$name.'%');
        }
        $server_list = $this->serverlist_model->alias('n')->field('n.id,n.name,l.notice,l.switch,l.web')->join('server_notice l ON n.id = l.serverid','left')->where($where)->select();
        $this->assign('posts',$server_list);
        $this->display();
    }

    public function edit(){
        if(IS_POST){
            writeLog('edit');
            $data = $_POST;
            $id = $data['id'];
            $data['serverid'] = $data['id'];
            unset($data['id']);
            try{
                $serverid = $this->servernotice_model->field('serverid')->select();
                $serverids = array_column($serverid,'serverid');
                if(in_array($id,$serverids)){
                    $this->servernotice_model->where(array('serverid'=>$id))->save($data);
                }else{
                    $this->servernotice_model->add($data);
                }

            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success(L('CHANGE_SUCCESS'));
        }
        $id = I('get.id',0);
        $posts =  $this->serverlist_model->alias('n')->field('n.id,n.name,l.notice,l.switch,l.web')->join('server_notice l ON n.id = l.serverid','left')->where(array('id'=>$id))->find();
        $this->assign('posts',$posts);
        $this->display();


    }

}