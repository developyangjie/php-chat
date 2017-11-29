<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2015/12/22
 * Time: 19:12
 */

namespace Operate\Controller;
use Common\Controller\AdminbaseController;
class ClubController extends AdminbaseController{

    function _initialize(){
        parent::_initialize();
    }
    /*
     * ip列表
     * **/
    public function index($serverid = ''){
        if(empty($serverid)){
            $serverid  = I('server_id');
        }
        if(!empty($serverid)){
            $uid = I('uid');
            $cid = I('cid');
            if(!empty($uid)) $where['uid'] = $uid;
            if(!empty($cid)) $where['cid'] = $cid;
            $db_config = gm_server_config($serverid);
            $sys_lt_control = M("uclub",null,$db_config);
            $data=$sys_lt_control->where($where)->field('uid,cid,state,totalscore,exitts,cinum,citype')->select();

        }
        $this->assign('posts',$data)->assign('serverid',$serverid);
        $this->display('index');
    }
    public function edit(){
        $data = $_POST;
        $serverid  = I('get.serverid');
        $uid = I('get.uid');
        $state = $data['state'];
        $cid = $data['cid'];
        $db_config = gm_server_config($serverid);
        $uclub  = M("uclub",null,$db_config);
        if(IS_POST){
            try{
                $result=$uclub->where("uid=$uid and cid = $cid") ->setField('state',$state);

            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success(L('UPDATE_SUCCESS'));
        }
        $id = I('get.uid',0);

        $posts =$uclub->where(array('uid'=>$id))->find();
        $this->assign('posts',$posts);
        $this->display('edit');
    }

}