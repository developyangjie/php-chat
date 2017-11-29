<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2016/11/7
 * Time: 16:46
 */

namespace Operate\Controller;

use Common\Controller\AdminbaseController;
class ActiveConfigurationController extends AdminbaseController{
    protected $server_act_model;
    function _initialize(){
        parent::_initialize();
        $this->server_act_model = M('server_act',null,DB_CONFIG_PLATFORM);
    }
    public function index(){
        $where = array();
        $name = I('said');
        if(!empty($name)){
            $where['said'] = $name;
        }
        $list = $this->server_act_model->where($where)->select();
        foreach($list as $k=>$value){
            $list[$k]["startts"]=date('Y-m-d H:i',$value["startts"]);
            $list[$k]["endts"]=date('Y-m-d H:i',$value["endts"]);
        }
        $this->assign('posts',$list);// 赋值数据集
        $this->display();
    }
    public function edit(){
        if(IS_POST) {
            $data = $_POST;
            $said = $data['said'];
            $serverid = $data['server_id'];
            $starttime=$data['startts'];
            $endtime=$data['endts'];
            $data['startts'] =strtotime("$starttime") ;
            $data['endts'] =strtotime("$endtime") ;
            try {
                $this->server_act_model->where("said=$said and serverid=$serverid")->save($data);
                $this->delete_redis_keys($serverid);

            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success(L('CHANGE_SUCCESS'));
        }
        $said = $_GET['said'];
        $serverid = $_GET['serverid'];
        $posts =$this->server_act_model->where("said=$said and serverid=$serverid")->find();
        $posts["startts"]=date('Y-m-d H:i',$posts["startts"]);
        $posts["endts"]=date('Y-m-d H:i',$posts["endts"]);
        $this->assign('posts',$posts);
        $this->display();
    }
    public function add(){
        $data = $_POST;
        $serverid = $data['server_id'];
        $starttime=$data['startts'];
        $endtime=$data['endts'];
        $data['serverid']=$serverid;
        unset($data['server_id']);
        $data['startts'] =strtotime("$starttime") ;
        $data['endts'] =strtotime("$endtime") ;
        if(IS_POST){

            try{

                $this->server_act_model->add($data);
                $this->delete_redis_keys($serverid);


            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success(L('ADD_SUCCESS'));
        }
        $this->display();
    }
    public function clears(){
        writeLog('clear');
        $serverid = $_GET['serverid'];
        $result  = 1;
        try{
            if(!empty($serverid)){
                $db_config = gm_server_config($serverid);
                $uinfo_model = M('uinfo',null,$db_config);
                $uinfo = $uinfo_model->where("serverid = ".$serverid)->field('uid')->select();
                $uinfo =  array_column($uinfo,'uid');
                $uids = implode(",",$uinfo);
                if(!empty($uids)){
                    $data = array('xiuluocoin'=>'0');
                    $xiuluocoin=$uinfo_model->where("serverid = ".$serverid)->setField($data);
                    $uxiuluo_control = M('uxiuluo',null,$db_config);
                    $uxiuluoreward_control = M('uxiuluoreward',null,$db_config);

                    $data = $uxiuluo_control->where("uid in($uids)")->delete();
                    $datas = $uxiuluoreward_control->where("uid in($uids)")->delete();
                }else{
                    $this->success(L('CLEAR_SUCCESS'));
                }


            }
        }catch(Exception $e){
            $result = 0;
            $this->error(L('EMPTY_DATA_FAILED'));
        }
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($result));
    }
    public function clearsraidboss(){
        writeLog('clear');
        $serverid = $_GET['serverid'];
        $result  = 1;
        try{
            if(!empty($serverid)){
                $db_config = gm_server_config($serverid);
                $sysclub = M('sysclub',null,$db_config);
                $uinfo_model = M('uinfo',null,$db_config);
                $uclubraidboss  = M('uclubraidboss',null,$db_config);
                $ddta = $sysclub->alias('s')->field('s.cid')->join('uinfo u on s.uid = u.uid','left')->where("u.serverid =". $serverid)->select();
                $ddta = array_column($ddta,'cid');
                $data_cid = implode(",",$ddta);
                $data_cids = $uclubraidboss->where("cid in($data_cid)")->delete();
                $uinfo = $uinfo_model->where("serverid = ".$serverid)->field('uid')->select();
                $uinfo =  array_column($uinfo,'uid');
                $uids = implode(",",$uinfo);
                if(!empty($uids)){
                    $uclub_control = M('uclub',null,$db_config);
                    $uraidboss_control = M('uraidboss',null,$db_config);
                    $uraidbosslog_control = M('uraidbosslog',null,$db_config);
                    $uraidbossreward_control = M('uraidbossreward ',null,$db_config);
                    $data = array('crusade'=>'0');
                    $uclub=$uclub_control->where("uid in($uids)")->setField($data);
                    $data = $uraidboss_control->where("uid in($uids)")->delete();
                    $datas = $uraidbosslog_control->where("uid in($uids)")->delete();
                    $ddatas = $uraidbossreward_control->where("uid in($uids)")->delete();
                }else{
                    $this->success(L('CLEAR_SUCCESS'));
                }


            }
        }catch(Exception $e){
            $result = 0;
            $this->error(L('EMPTY_DATA_FAILED'));
        }
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($result));
    }
    public function accumulate(){
        if(IS_POST){
            $data=$_POST;
            $said = $data['said'];
            $serverid = $data['server_id'];
            $data['serverid']=$serverid;
            unset($data['server_id']);
            //判断是否存在奖品
            $temp = array();
            if (!empty($data['itemid'])) {
                foreach ($data['itemid'] as $k => $v) {
                    if($data['type'][$k] != 16  && $data['type'][$k] != 15 && $data['type'][$k] != 0 ){
                        $data['type'][$k] = 11;
                    }
                    if (isset($temp[$v])) { // 若已经存在 则进行累加
                        $temp[$v]['count'] += $data['count'][$k];
                        $temp[$v]['type'] = $data['type'][$k];
                    } else{
                        $temp[$v] = array(
                            'itemid' => $data['itemid'][$k],
                            'count' => (int) $data['count'][$k],
                            'type' =>$data['type'][$k]
                        );
                    }
                }
            }
            $temp = array_values($temp);
            try{
                //数据-
                $data_gm_server = array(
                    'serverid'=>$serverid,
                    'chongzhi' =>$data['chongzhi'],
                    'item1'  => (int)$temp[0]['itemid'],
                    'count1'  => (int)$temp[0]['count'],
                    'type1'  => (int)$temp[0]['type'],
                    'item2'  => $temp[1]['itemid']?(int)$temp[1]['itemid']:0,
                    'count2'  => $temp[1]['count']?(int)$temp[1]['count']:0,
                    'type2'  => (int)$temp[1]['type'],
                    'item3'  => $temp[2]['itemid']?(int)$temp[2]['itemid']:0,
                    'count3'  => $temp[2]['count']?(int)$temp[2]['count']:0,
                    'type3'  => (int)$temp[2]['type'],
                    'item4'  => $temp[3]['itemid']?(int)$temp[3]['itemid']:0,
                    'count4'  => $temp[3]['count']?(int)$temp[3]['count']:0,
                    'type4'  => (int)$temp[3]['type'],
                );
                $data_gm_xiaofei = array(
                    'serverid'=>$serverid,
                    'xiaofei' =>$data['chongzhi'],
                    'item1'  => (int)$temp[0]['itemid'],
                    'count1'  => (int)$temp[0]['count'],
                    'type1'  => (int)$temp[0]['type'],
                    'item2'  => $temp[1]['itemid']?(int)$temp[1]['itemid']:0,
                    'count2'  => $temp[1]['count']?(int)$temp[1]['count']:0,
                    'type2'  => (int)$temp[1]['type'],
                    'item3'  => $temp[2]['itemid']?(int)$temp[2]['itemid']:0,
                    'count3'  => $temp[2]['count']?(int)$temp[2]['count']:0,
                    'type3'  => (int)$temp[2]['type'],
                    'item4'  => $temp[3]['itemid']?(int)$temp[3]['itemid']:0,
                    'count4'  => $temp[3]['count']?(int)$temp[3]['count']:0,
                    'type4'  => (int)$temp[3]['type'],
                );
                //---得到配置db
                $db_config = gm_server_config($serverid);
                $cfg_chongzhi = M("cfg_chongzhi",null,$db_config); //充值表-
                $cfg_xiaofei = M("cfg_xiaofei",null,$db_config);//消费表

                if($said == 6){
                    $cfg_chongzhi->add($data_gm_server);
                }else if($said == 7){
                    $cfg_xiaofei->add($data_gm_xiaofei);
                }else{
                    $sql = "CREATE TABLE if not EXISTS cfg_weekchongzhi like cfg_chongzhi";
                    $c = M("",null,$db_config)->execute($sql,true);

                        $cfg_xiaofei = M("cfg_weekchongzhi",null,$db_config);
                        $cfg_xiaofei->add($data_gm_server);


                }

            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success(L('ADD_SUCCESS'),U('ActiveConfiguration/accumulate'));
        }
        $this->assign('serverid',$serverid);

        $this->display();
    }
    public function accumulate_list($serverid = ''){
        if(empty($serverid)){
            $serverid  = I('server_id');
        }
        if(!empty($serverid)){
            $db_config = gm_server_config($serverid);
            $cfg_chongzhi = M("cfg_chongzhi",null,$db_config);
            $data=$cfg_chongzhi->where("serverid=$serverid")->select();
        }

        $this->assign('posts',$data)->assign('serverid',$serverid);
        $this->display('accumulate_list');
    }
    public function tired_list($serverid = ''){
        if(empty($serverid)){
            $serverid  = I('server_id');
        }
        if(!empty($serverid)){
            $db_config = gm_server_config($serverid);
            $cfg_xiaofei = M("cfg_xiaofei",null,$db_config);
            $data=$cfg_xiaofei->where("serverid=$serverid")->select();
        }

        $this->assign('posts',$data)->assign('serverid',$serverid);
        $this->display('tired_list');
    }
    public function weekaccumulate_list($serverid = ''){
        if(empty($serverid)){
            $serverid  = I('server_id');
        }
        if(!empty($serverid)){
            $db_config = gm_server_config($serverid);
            $cfg_chongzhi = M("cfg_weekchongzhi",null,$db_config);
            $data=$cfg_chongzhi->where("serverid=$serverid")->select();
        }

        $this->assign('posts',$data)->assign('serverid',$serverid);
        $this->display('weekaccumulate_list');
    }
    function delete_accumulate(){
        $serverid  = I('get.serverid');
        $id = I("get.id");
        if(!empty($serverid)){

            //---得到配置db
            $db_config = gm_server_config($serverid);
            $cfg_chongzhi = M("cfg_chongzhi", null, $db_config);
            if ($cfg_chongzhi->where(array("id"=>$id))->delete()!==false){

                $this->success(L('DELETE_SUCCESS'));
            } else {
                $this->error(L('DELETE_FAIL'));
            }

        }else{
            $this->error(L('DELETE_FAIL'));
        }


    }
    function delete_tired(){
        $serverid  = I('get.serverid');
        $id = I("get.id");
        if(!empty($serverid)){

            //---得到配置db
            $db_config = gm_server_config($serverid);
            $cfg_xiaofei = M("cfg_xiaofei", null, $db_config);
            if ($cfg_xiaofei->where(array("id"=>$id))->delete()!==false){

                $this->success(L('DELETE_SUCCESS'));
            } else {
                $this->error(L('DELETE_FAIL'));
            }

        }else{
            $this->error(L('DELETE_FAIL'));
        }


    }
    function delete_weekaccumulate(){
        $serverid  = I('get.serverid');
        $id = I("get.id");
        if(!empty($serverid)){

            //---得到配置db
            $db_config = gm_server_config($serverid);
            $cfg_chongzhi = M("cfg_weekchongzhi", null, $db_config);
            if ($cfg_chongzhi->where(array("id"=>$id))->delete()!==false){

                $this->success(L('DELETE_SUCCESS'));
            } else {
                $this->error(L('DELETE_FAIL'));
            }

        }else{
            $this->error(L('DELETE_FAIL'));
        }


    }
    public function timedpacks(){
        if(IS_POST){
            $data=$_POST;
            $serverid = $data['server_id'];
            $data['serverid']=$serverid;
            unset($data['server_id']);
            //判断是否存在奖品
            $temp = array();
            if (!empty($data['itemid'])) {
                foreach ($data['itemid'] as $k => $v) {
                    if($data['type'][$k] != 16  && $data['type'][$k] != 15 && $data['type'][$k] != 0 ){
                        $data['type'][$k] = 11;
                    }
                    if (isset($temp[$v])) { // 若已经存在 则进行累加
                        $temp[$v]['count'] += $data['count'][$k];
                        $temp[$v]['type'] = $data['type'][$k];
                    } else{
                        $temp[$v] = array(
                            'itemid' => $data['itemid'][$k],
                            'count' => (int) $data['count'][$k],
                            'type' =>$data['type'][$k]
                        );
                    }
                }
            }
            $temp = array_values($temp);
            try{
                //数据-
                $data_gm_server = array(
                    'money' =>$data['money'],
                    'price' =>$data['price'],
                    'buylimit' =>$data['buylimit'],
                    'item1'  => (int)$temp[0]['itemid'],
                    'count1'  => (int)$temp[0]['count'],
                    'type1'  => (int)$temp[0]['type'],
                    'item2'  => $temp[1]['itemid']?(int)$temp[1]['itemid']:0,
                    'count2'  => $temp[1]['count']?(int)$temp[1]['count']:0,
                    'type2'  => (int)$temp[1]['type'],
                    'item3'  => $temp[2]['itemid']?(int)$temp[2]['itemid']:0,
                    'count3'  => $temp[2]['count']?(int)$temp[2]['count']:0,
                    'type3'  => (int)$temp[2]['type'],
                    'item4'  => $temp[3]['itemid']?(int)$temp[3]['itemid']:0,
                    'count4'  => $temp[3]['count']?(int)$temp[3]['count']:0,
                    'type4'  => (int)$temp[3]['type'],
                );
                //---得到配置db
                $db_config = gm_server_config($serverid);
                $cfg_xianshibao = M("cfg_xianshibao",null,$db_config); //充值表-
                $cfg_xianshibao->add($data_gm_server);
                $this->delete_redis_key();
            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success(L('ADD_SUCCESS'),U('ActiveConfiguration/timedpacks'));
        }
        $this->assign('serverid',$serverid);

        $this->display();
    }
    public function timedpacks_list($serverid = ''){
        if(empty($serverid)){
            $serverid  = I('server_id');
        }
        if(!empty($serverid)){
            $db_config = gm_server_config($serverid);
            $cfg_xianshibao = M("cfg_xianshibao",null,$db_config);
            $data=$cfg_xianshibao->where(1)->select();
        }

        $this->assign('posts',$data)->assign('serverid',$serverid);
        $this->display('timedpacks_list');
    }
    function delete_timedpacks(){
        $serverid  = I('get.serverid');
        $id = I("get.id");
        if(!empty($serverid)){

            //---得到配置db
            $db_config = gm_server_config($serverid);
            $cfg_xianshibao = M("cfg_xianshibao", null, $db_config);
            if ($cfg_xianshibao->where(array("id"=>$id))->delete()){
                $this->delete_redis_key();
                $this->success(L('DELETE_SUCCESS'));
            } else {
                $this->error(L('DELETE_FAIL'));
            }

        }else{
            $this->error(L('DELETE_FAIL'));
        }


    }

    private function delete_redis_key(){
        redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del('cfg_xianshibao');

    }
    private function delete_redis_keys($serverid){
        redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del("server_act_".$serverid);
        redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del("server_act_byid_".$serverid);

    }
}