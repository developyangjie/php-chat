<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2015/7/15
 * Time: 19:12
 */

namespace Operate\Controller;
use Common\Controller\AdminbaseController;
class RegularMailController extends AdminbaseController{
    protected $serverlist_model;
    protected $cfg_partner_model;
    protected $cfg_equip_model;
    function _initialize(){
        parent::_initialize();
        $this->serverlist_model = M('server_list',null,DB_CONFIG_PLATFORM);
        $this->cfg_partner_model = M('cfg_partner',null,DB_CONFIG_PLATFORM);
        $this->cfg_equip_model = M('cfg_equip',null,DB_CONFIG_PLATFORM);
	}
    public function get_item_json(){
        $itemType = $_GET['itemType'];
        $serverConfig = M('cfg_item',null,DB_CONFIG_PLATFORM);
        $result = get_gm_server_props($itemType);
        $newArr = array();

        foreach ($result as $k=>$type) {
            $newArr[] = array(
                'Value' => $type['id'],
                'DisplayText' => $type['name']
            );
        }
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode(array(
            'Options' => $newArr,
            'Result' => 'OK'
        )));

    }
    /*
     * Send Email 2015-09-28 dlx
     * **/
    public function send_email(){
        set_time_limit(0);
        if(IS_POST){
            //--写入日志--
            writeLog('send_email');
            $this->sendMessage($_POST);
        }else{
            $this->display();
        }
    }
    /**
     * @demo
     * @param type $parms
     */
    private function sendMessage($parms) {

        $data = $parms;
        $server_id = $data['emailServerIds'];
        //判断是否存在奖品
        $temp = array();
        $partners = array();
        $equips = array();
        if (!empty($data['itemid'])) {
            foreach ($data['itemid'] as $k => $v) {
                if($data['type'][$k] == '16'){
                    $partners[$k] = $data['itemid'][$k];
                }else if($data['type'][$k] == '15'){
                    $equips[$k] = $data['itemid'][$k];
                }else{
                    if (isset($temp[$v])) { // 若已经存在 则进行累加
                        $temp[$v]['count'] += $data['count'][$k];
                    } else {
                        $temp[$v] = array(
                            'itemid' => $data['itemid'][$k],
                            'count' => (int) $data['count'][$k]
                        );
                    }
                }
            }
        }
        $temp = array_values($temp);
        $emailGameDiamond = intval($data['emailGameDiamond']);
        $emailGameCoin = intval($data['emailGameCoin']);
        $taskName = (string)$data['taskName'];
        $emailUid = intval($data['emailUid']);
        $emailUid = explode(",",$emailUid);
        $taskCron = date('s i H d m ?', strtotime($data['taskCron']));
        try{
            //数据-
            $data_gm_server = array(
                'emailTitle' => $data['emailTitle'],
                'emailContent' =>$data['emailContent'],
                'emailUid'  => array(0),
                'emailServerIds'  => $server_id,
                'emailGameDiamond'    =>  $emailGameDiamond,
                'emailGameCoin' =>  $emailGameCoin,
                'emailType' =>(int)$data['emailType'],
                'emailGameItemID'  => (int)$temp[0]['itemid'],
                'emailGameItemCount'  => (int)$temp[0]['count'],
                'emailGameItemID1'  => $temp[1]['itemid']?(int)$temp[1]['itemid']:0,
                'emailGameItemCount1'  => $temp[1]['count']?(int)$temp[1]['count']:0,
                'emailGamePartners' => count($partners) > 0 ? implode(",",$partners) : '',
                'emailGameEquips' => count($equips) > 0 ? implode(",",$equips) : '',
                'taskCron' => $taskCron
            );
            header('Content-Type:application/json; charset=utf-8');
            $data_gm_server = json_encode($data_gm_server,JSON_UNESCAPED_UNICODE);
            $redis2=redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->hexists('emailTask',$taskName);
            if($redis2){
                $this->error(L('KEY_VALUE'));
            }else{
                redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->hSet('emailTask',$taskName,$data_gm_server);
                redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->publish('emailTask','update');
                $this->success(L('SEND_SUCCESS'),U('Email/send_email'));
            }


        }catch (Exception $e){
            $this->error($e->getMessage());
        }
    }
    public function email_list(){

        $list = redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->hgetall('emailTask');
        $result = array();
        foreach($list as $key=>$val){
            $ary = json_decode($val,true);
            $serverNames = array();

            $serverid = implode(',',$ary['emailServerIds']) ;
            if(!empty($serverid)){
                $serverNames = $this->serverlist_model->where("id in ($serverid)")->field('name')->select();
            }
            $serverNames = array_column($serverNames,'name');
            $partner  = $ary['emailGamePartners'];
            $partnerNames=array();
            if(!empty($partner)){
                $partnerNames = $this->cfg_partner_model->where("partnerid in ($partner)")->field('name')->select();
            }
            $partnerNames = array_column($partnerNames,'name');
            $equips  = $ary['emailGameEquips'];
            $equipsNames=array();
            if(!empty($equips)){
                $equipsNames = $this->cfg_equip_model->where("eid in ($equips)")->field('ename')->select();
            }
            $equipsNames = array_column($equipsNames,'ename');
            $ary['emailServerNames'] = implode(',',$serverNames);
            $ary['emailUid'] = implode(',',$ary['emailUid']);
            $ary['emailGamePartners'] = $ary['emailGamePartners'];
            $ary['emailGameEquips'] =$ary['emailGameEquips'];
            $ary['taskName'] = $key;
            $result[] = $ary;
        }
        $this->assign('posts',$result);// 赋值数据集
        $this->display();
    }

    function delete(){
        $key = I("get.key");
        if (redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->hdel('emailTask', $key)!==false) {
            redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->publish('emailTask','update');
            $this->success(L('DELETE_SUCCESS'));
        } else {
            $this->error(L('DELETE_FAIL'));
        }
    }
}