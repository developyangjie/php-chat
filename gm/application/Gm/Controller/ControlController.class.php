<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2015/12/22
 * Time: 19:12
 */

namespace Gm\Controller;
use Common\Controller\AdminbaseController;
class ControlController extends AdminbaseController{
    protected $serverlist_model;
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
            $db_config = gm_server_config($serverid);
            $sys_lt_control = M("sys_lt_control",null,$db_config);
            $data=$sys_lt_control->where(1)->select();
        }

        $this->assign('posts',$data)->assign('serverid',$serverid);
        $this->display('index');
    }

    public function open()
    {
        $serverid  = $_GET['serverid'];
        $pid = $_GET['ip'];
        $switch = $_GET['switch'];
        $db_config = gm_server_config($serverid);
        $sys_lt_control = M("sys_lt_control",null,$db_config);
        $data=$sys_lt_control->where('pid = '.$pid)->setField('switch', $switch);
        $this->index($serverid);
    }

    public function sysit_edit(){
        $data = $_POST;
        $serverid  = $_GET['serverid'];
        $db_config = gm_server_config($serverid);
        $pid = $_GET['pid'];
        unset($data['pid']);
        $sys_lt_control = M("sys_lt_control",null,$db_config);
        if(IS_POST){

            try{
                $data=$sys_lt_control->where(array('pid'=>$pid))->save($data);
            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success(L('CHANGE_SUCCESS'));
        }
        $pid = I('get.pid',0);
        $posts =$sys_lt_control->where(array('pid'=>$pid))->find();
        $this->assign('posts',$posts);
        $this->display();


    }
    public function update_sysit(){
        $data = $_POST;
        $serverid  = $_GET['serverid'];
        $db_config = gm_server_config($serverid);
        $pid = $_GET['pid'];
        $sys_lt_control = M("sys_lt_control",null,$db_config);
        $data=$sys_lt_control->where(array('pid'=>$pid))->setField('num','0');
        $this->index($serverid);
    }
    public function oftenopen($serverid = ''){
        if(empty($serverid)){
            $serverid  = I('server_id');
        }
        if(!empty($serverid)){
            $db_config = gm_server_config($serverid);
            $sys_pt_control = M("sys_pt_control",null,$db_config);
            $data=$sys_pt_control->where(1)->select();
        }

        $this->assign('posts',$data)->assign('serverid',$serverid);
        $this->display('oftenopen');
    }
    public function syspt_edit(){
        $data = $_POST;
        $serverid  = $_GET['serverid'];
        $db_config = gm_server_config($serverid);
        $onedrawcount = $_GET['onedrawcount'];
        $sys_pt_control = M("sys_pt_control",null,$db_config);
        if(IS_POST){

            try{
                $data=$sys_pt_control->where(array('onedrawcount'=>$onedrawcount))->save($data);
            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success(L('CHANGE_SUCCESS'));
        }
        $onedrawcount = I('get.onedrawcount',0);
        $posts =$sys_pt_control->where(array('onedrawcount'=>$onedrawcount))->find();
        $this->assign('posts',$posts);
        $this->display();


    }
    public function update_udrawforpay(){
        $data = $_POST;
        $serverid  = $_GET['serverid'];
        $db_config = gm_server_config($serverid);
        $udrawforpay  = M("udrawforpay",null,$db_config);
        $data = array('pay'=>'0','paydraw'=>'0');
        $result=$udrawforpay->where(1) ->setField($data);

        if($result){
            echo L('UPDATE_SUCCESS');
        }else{
            echo L('NOT_UPDATE_ANY_DATA');
        }
        $this->specialsummon($serverid);
    }
    public function specialsummon($serverid = ''){
        if(empty($serverid)){
            $serverid  = I('server_id');
        }
        if(!empty($serverid)){
            $db_config = gm_server_config($serverid);
            $sys_dt_control = M("sys_dt_control",null,$db_config);
            $sys_pay_control = M("sys_pay_control",null,$db_config);
            $udrawforpay = M("udrawforpay", null, $db_config);
            $data=$sys_dt_control->where(1)->select();
            $datas = $sys_pay_control->where(1)->select();

            $data_update = $udrawforpay->select();
        }

        $this->assign('posts',$data)->assign('serverid',$serverid);
        $this->assign('post',$datas)->assign('serverid',$serverid);
        $this->assign('postd',$data_update)->assign('serverid',$serverid);
        $this->display('specialsummon');
    }

    public function switchoff()
    {
        $serverid  = $_POST['serverid'];
        $switch = $_POST['switch'];
        $db_config = gm_server_config($serverid);
        $sys_pay_control = M("sys_pay_control",null,$db_config);
        $data=$sys_pay_control->where(1)->setField('switch', $switch);
        $result = 0;
        if($data > 0){
            $result = 1;
        }
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($result));
    }
    public function sysdt_edit(){
        $data = $_POST;
        $serverid  = $_GET['serverid'];
        $db_config = gm_server_config($serverid);
        $id = $_GET['id'];
        $sys_dt_control = M("sys_dt_control",null,$db_config);
        if(IS_POST){

            try{
                $data=$sys_dt_control->where(array('id'=>$id))->save($data);
            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success(L('CHANGE_SUCCESS'));
        }
        $id = I('get.id',0);
        $posts =$sys_dt_control->where(array('id'=>$id))->find();
        $this->assign('posts',$posts);
        $this->display();


    }
    public function cardwarehouse($serverid = ''){
        if(empty($serverid)){
            $serverid  = I('server_id');
        }
        if(!empty($serverid)){
            $db_config = gm_server_config($serverid);
            $sys_gt_control = M("sys_gt_control",null,$db_config);
            $data=$sys_gt_control->where(1)->select();
        }

        $this->assign('posts',$data)->assign('serverid',$serverid);
        $this->display('cardwarehouse');
    }
    public function sysgt_add(){
        if(IS_POST){
            $data = $_POST;
            $serverid=$data['server_id'];
            if(empty($serverid)){
                $serverid  = I('server_id');
            }
            if(!empty($serverid)){
                $db_config = gm_server_config($serverid);
                $sys_gt_control = M("sys_gt_control",null,$db_config);
                writeLog('sysgt_add');
                try{

                    $sys_gt_control->add($data);
                }catch (Exception $e){
                    $this->error($e->getMessage());
                }
                $this->success(L('ADD_SUCCESS'));
            }
        }
        $this->display();
    }
    public function sysgt_edit(){
        $data = $_POST;
        $serverid  = $_GET['serverid'];
        $db_config = gm_server_config($serverid);
        $pid = $_GET['pid'];
        unset($data['pid']);
        $sys_gt_control = M("sys_gt_control",null,$db_config);
        if(IS_POST){

            try{
                $data=$sys_gt_control->where(array('pid'=>$pid))->save($data);
            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success(L('CHANGE_SUCCESS'));
        }
        $pid = I('get.pid',0);
        $posts =$sys_gt_control->where(array('pid'=>$pid))->find();
        $this->assign('posts',$posts);
        $this->display();


    }
    function sysgt_delete(){
        $serverid  = I('get.serverid');
        $pid = I("get.pid");

        if(!empty($serverid)){
                //---得到配置db
                $db_config = gm_server_config($serverid);
                $gm_uinfo = M("sys_gt_control",null,$db_config);
            try{
                $gm_uinfo->where(array("pid"=>$pid))->delete();
            }catch(Exception $e){
                $this->error($e->getMessage());
            }
            $this->success(L('DELETE_SUCCESS'));

        }


    }
    public function update_sysgt(){
        $data = $_POST;
        $serverid  = $_GET['serverid'];
        $db_config = gm_server_config($serverid);
        $pid = $_GET['pid'];
        $sys_gt_control = M("sys_gt_control",null,$db_config);
        $data=$sys_gt_control->where(array('pid'=>$pid))->setField('num','0');
        $this->cardwarehouse($serverid);
    }
    /*
     * 验证客户端版本号
     * **/
    private function _version_number($version=''){
        if(empty($version))  $this->error('请填写客户端版本号!');
        if(!preg_match('/^.{3}$/',$version)) $this->error('请填写正确的客户端版本号!');
        if($version<1.0) $this->error('客户端本不能小于1.0!');
    }

}