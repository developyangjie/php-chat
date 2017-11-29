<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2015/12/23
 * Time: 19:12
 */

namespace Gm\Controller;
use Common\Controller\AdminbaseController;

class RechargeResetController extends AdminbaseController{
    protected $serverlist_model;
    function _initialize(){
        parent::_initialize();

    }
    public function index($serverid = ''){
        /*if(empty($serverid)){
            $serverid  = I('server_id');
        }
        if(!empty($serverid)){
            $db_config = gm_server_config($serverid);
            $uchongzhi_control = M("uchongzhi",null,$db_config);
            $uxiaofei_control = M("uxiaofei",null,$db_config);
            $data_uchongzhi=$uchongzhi_control->where()->select();
            $data_uxiaofei=$uxiaofei_control->where()->select();
        }

        $this->assign('posts',$data_uchongzhi)->assign('serverid',$serverid);
        $this->assign('post',$data_uxiaofei)->assign('serverid',$serverid);*/
        $this->display('index');
    }
    public function rechargeReset(){
        writeLog('rechargeReset');
        $serverid = $_GET['serverid'];
        $result  = 1;
        /*try{
            if(!empty($serverid)){
                $db_config = gm_server_config($serverid);
                $uinfo_model = M('uinfo',null,$db_config);
                $uinfo = $uinfo_model->where("serverid = ".$serverid)->field('uid')->select();
                $uinfo =  array_column($uinfo,'uid');
                $uids = implode(",",$uinfo);
                $uchongzhi_control = M('uchongzhi',null,$db_config);
                $data = $uchongzhi_control->where("uid in($uids)")->delete();

            }
        }catch(Exception $e){
            $result = 0;
            $this->error('清空数据失败！');
        }*/
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($result));
    }
    public function consumeReset(){
        /*writeLog('consumeReset');
        $serverid = $_GET['serverid'];
        $result  = 1;
        try{
            if(!empty($serverid)){
                $db_config = gm_server_config($serverid);
                $uinfo_model = M('uinfo',null,$db_config);
                $uinfo = $uinfo_model->where("serverid = ".$serverid)->field('uid')->select();
                $uinfo =  array_column($uinfo,'uid');
                $uids = implode(",",$uinfo);
                $uxiaofei_control = M('uxiaofei',null,$db_config);
                $data = $uxiaofei_control->where("uid in ($uids)")->delete();

            }
        }catch(Exception $e){
            $result = 0;
            $this->error('清空数据失败！');
        }
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($result));*/
    }

}