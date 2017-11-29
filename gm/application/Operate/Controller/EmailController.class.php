<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2015/7/15
 * Time: 19:12
 */

namespace Operate\Controller;
use Common\Controller\AdminbaseController;
class EmailController extends AdminbaseController{
    function _initialize(){
        parent::_initialize();

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
        $server_id = $data['server_id'];

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

        if(!empty($data['uid']) || $data['uid']=0){
            $type = 2;
        }else{
            $type = 3;
        }
        $ug = intval($data['ug']);
        $ucoin = intval($data['ucoin']);
        try{
            //数据-
            $data_gm_server = array(
                'mtitle' => (string)$data['title'],
                'mcontent' =>(string) $data['description'],
                'itemid'  => (int)$temp[0]['itemid'],
                'system'  => (int)$data['system'],
                'count'  => (int)$temp[0]['count'],
                'ts'     => time(),
                'itemid1'  => $temp[1]['itemid']?(int)$temp[1]['itemid']:0,
                'count1'  => $temp[1]['count']?(int)$temp[1]['count']:0,
                'ug'    =>  $ug,
                'ucoin' =>  $ucoin,
                'partners' => count($partners) > 0 ? implode(",",$partners) : '',
                'equips' => count($equips) > 0 ? implode(",",$equips) : ''
            );

             //---得到配置db
            $db_config = gm_server_config($server_id);
            //-uinfo-表
            $gm_uinfo = M("uinfo",null,$db_config);
            $umail = M("umail",null,$db_config); //邮件表-

            /*
            * 可以发送多区服 uid = 0 为全服发送 逗号隔开为多个uid
            * **/
            if(!preg_match("/^[\d,]*$/",$data['uid'])) {
                $this->error(L('FAIL_UID'),U('Email/send_email'));
            }
                if ($data['uid'] === 0) {
                    $result = $umail->execute("INSERT INTO umail (uid,mtitle,mcontent,ug,ucoin,itemid,count,ts,itemid1,count1,partners,equips,system) SELECT uid,'{$data_gm_server[mtitle]}' as mtitle,'{$data_gm_server[mcontent]}' as mcontent,$ug as ug ,$ucoin as ucoin, '{$data_gm_server[itemid]}' as itemid,'{$data_gm_server[count]}' as `count`,UNIX_TIMESTAMP() as `ts`,'{$data_gm_server[itemid1]}' as `item1`,'{$data_gm_server[count1]}' as `count1` ,'{$data_gm_server[partners]}' as `partners`,'{$data_gm_server[equips]}' as `equips`,'{$data_gm_server[system]}' as `system` from uinfo WHERE serverid=$server_id");
                    // $gm_uinfo->execute("update uinfo set mail=1");
                    if ($result > 0) {

                    }
                } else {
                    $uids = explode(",", $data['uid']);
                    foreach ($uids as $gm_uid) {
                        $data_gm_server['uid'] = $gm_uid;
//                    $data_gm_server['system'] = 1;
                        $umail->add($data_gm_server);
                    }
                    // $gm_uinfo->where("uid in({$data[uid]})")->setField('mail',1);
                }

        }catch (Exception $e){
            $this->error($e->getMessage());
        }
        $this->success(L('SEND_SUCCESS'),U('Email/send_email'));
    }

}