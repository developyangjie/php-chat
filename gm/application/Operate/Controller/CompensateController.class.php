<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2015/7/15
 * Time: 19:12
 */

namespace Operate\Controller;
use Common\Controller\AdminbaseController;
class CompensateController extends AdminbaseController{
    function _initialize(){
        parent::_initialize();

	}
    /*
     * Send Email 2015-09-28 dlx
     * **/
    public function re_compensate(){
        set_time_limit(0);
        if(IS_POST){
            //--写入日志--
            writeLog('re_compensate');
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

        if(!empty($data['uid']) || $data['uid']=0){
            $type = 2;
        }else{
            $type = 3;
        }
        $ug = intval($data['ug']);

        try{
            //数据-
            $data_gm_server = array(
                'mtitle' => (string)$data['title'],
                'mcontent' =>(string) $data['description'],
                'ts'     => time(),
                'ug'    =>  $ug,
            );

             //---得到配置db
            $db_config = gm_server_config($server_id);
            //-uinfo-表
            $gm_uinfo = M("uinfo",null,$db_config);
            $umail = M("umail",null,$db_config); //邮件表-

            /*
            * 可以发送多区服 uid = 0 为全服发送 逗号隔开为多个uid
            * **/
            if($data['uid']==0){
                $result = $umail->execute("INSERT INTO umail (uid,mtitle,mcontent,ug,ts,system) SELECT uid,'{$data_gm_server[mtitle]}' as mtitle,'{$data_gm_server[mcontent]}' as mcontent,$ug as ug ,UNIX_TIMESTAMP() as `ts`,$data_gm_server[count1] as `count1` ,1 as system from uinfo");
               // $gm_uinfo->execute("update uinfo set mail=1");
                if($result > 0){

                }
            }else{
                $uids = explode(",",$data['uid']);
                foreach ($uids as $gm_uid) {
                    $data_gm_server['uid'] = $gm_uid;
                    $data_gm_server['system'] = 1;
                    $umail->add($data_gm_server);
                }
                // $gm_uinfo->where("uid in({$data[uid]})")->setField('mail',1);
            }

        }catch (Exception $e){
            $this->error($e->getMessage());
        }
        $this->success('发送成功',U('Compensate/re_compensate'));
    }

}