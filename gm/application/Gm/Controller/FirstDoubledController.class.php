<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2015/12/23
 * Time: 19:12
 */

namespace Gm\Controller;
use Common\Controller\AdminbaseController;

class FirstDoubledController extends AdminbaseController{
    protected $serverlist_model;
    function _initialize(){
        parent::_initialize();

    }
    public function index($serverid = ''){
        if(empty($serverid)){
            $serverid  = I('server_id');
        }
        if(!empty($serverid)){
            $db_config = gm_server_config($serverid);
            $urechargetype_control = M("urechargetype",null,$db_config);
            $data_urechargetype=$urechargetype_control->where(1)->select();

        }
        $this->assign('posts',$data_urechargetype)->assign('serverid',$serverid);
        $this->display('index');
    }
    public function chargeReset(){
        writeLog('chargeReset');
        $serverid = $_GET['serverid'];
        $result  = 1;
        try{
            if(!empty($serverid)){
                $db_config = gm_server_config($serverid);
                $uinfo_model = M('uinfo',null,$db_config);
                $uinfo = $uinfo_model->where("serverid = ".$serverid)->field('uid')->select();
                $uinfo =  array_column($uinfo,'uid');
                $uids = implode(",",$uinfo);
                $urechargetype_control = M('urechargetype',null,$db_config);
                $data = $urechargetype_control->where("uid in($uids)")->delete();

            }
        }catch(Exception $e){
            $result = 0;
            $this->error(L('EMPTY_DATA_FAILED'));
        }
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($result));
    }

}