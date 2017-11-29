<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2015/7/15
 * Time: 19:12
 */

namespace Operate\Controller;
use Common\Controller\AdminbaseController;
class ActivityController extends AdminbaseController{
    private $activity_mode;
    private $activityinfo_mode;
    function _initialize(){
        parent::_initialize();
        $this->activity_mode = M('activity');
        $this->activityinfo_mode = M('activity_info');
    }
    public function index(){
		/*
		*直接获取游戏服务器数据*
		*/
        $serverid = I('server_id');
        if(!empty($serverid)){
            $db_config =gm_server_config($serverid);
            $count = M('server_act',null,$db_config)->count();
            $posts = M('server_act',null,$db_config)->select();
            $this->assign('posts',$posts)
                ->assign('count',$count)
                ->assign('serverids',$serverid)
                ->assign('posts_json',json_encode($posts));
        }else{
			/*
			*GM_data 获取数据
			**/
            $where = array();
            $count=$this->activity_mode->where($where)->count();
            $page = $this->page($count,20);
            $posts=$this->activity_mode->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
            if(!empty($posts)){
                $said = '';
                foreach($posts as $item){
                    $said .= $item['said'].",";
                }
                $said = rtrim($said,",");
				$ainfo = $this->activityinfo_mode->query("select*from ".C('DB_PREFIX')."activity_info where said in ($said) and ((startts<unix_timestamp(now()) and endts>unix_timestamp(now())) or (startts<unix_timestamp(now()) and endts is null))");
				if(!empty($ainfo)){
					$newArr = array();
					foreach($ainfo as $servers){
						if(isset($servers['said'])){
							$newArr[$servers['said']][] = gm_server($servers['serverid'])?gm_server($servers['serverid']):$servers['serverid'];
						}else{
							$newArr[$servers['said']] = gm_server($servers['serverid'])?gm_server($servers['serverid']):$servers['serverid'];
						}
					}
					foreach($newArr as $key=>$item){
						$newArr[$key] = implode(",",$item);
					}
				}
				$this->assign('server_info',$newArr);
              
            }
            $this->assign("Page", $page->show('Admin'));
            $this->assign("posts", $posts);
        }
        $this->display();
    }
    /*
     * 导出excel
     * **/
    public function export_excel(){
		    $said = I('get.said');
			if(!isset($said)) $this->error('参数错误!');
            
    }
    public function edit(){
        if(IS_POST){

        }
        $serverid = I('get.serverid',0);
        $said = I('get.said',0);
        if(!isset($serverid) || !isset($said)){
            $this->error('参数错误!');
        }
        $db_config =gm_server_config($serverid);
        $posts = M('server_act',null,$db_config)->where(array('said'=>$said))->find();
        $posts['sid'] = $serverid;
        $this->assign('posts',$posts);
        $this->assign('item_info',getAllItem());
        $this->display();
    }
    /*
     * 新增礼包
     * ***/
    public function add(){
        $this->display();
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
        }
       $this->display();
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
        if (!empty($data['itemid'])) {
            foreach ($data['itemid'] as $k => $v) {
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
        $temp = array_values($temp);
        if(!empty($data['uid']) || $data['uid']=0){
            $type = 2;
        }else{
            $type = 3;
        }

        try{
            //数据-
            $data_gm_server = array(
                'mtitle' => (string)$data['title'],
                'mcontent' =>(string) $data['description'],
                'itemid'  => (int)$temp[0]['itemid'],
                'count'  => (int)$temp[0]['count'],
                'ts'     => time(),
                'itemid1'  => $temp[1]['itemid']?(int)$temp[1]['itemid']:0,
                'count1'  => $temp[1]['count']?(int)$temp[1]['count']:0,
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
                $umail->execute("INSERT INTO umail (uid,mtitle,mcontent,ug,ucoin,itemid,count,ts,itemid1,count1) SELECT uid,'{$data_gm_server[mtitle]}' as mtitle,'{$data_gm_server[mcontent]}' as mcontent,0 as ug ,0 as ucoin, $data_gm_server[itemid] as itemid,$data_gm_server[count] as `count`,UNIX_TIMESTAMP() as `ts`,$data_gm_server[itemid] as `item1`,$data_gm_server[count1] as `count1` from uinfo");
                $gm_uinfo->execute("update uinfo set mail=1");
            }else{
                $uids = explode(",",$data['uid']);
                foreach ($uids as $gm_uid) {
                $data_gm_server['uid'] = $gm_uid;
                $umail->add($data_gm_server);
            }
            $gm_uinfo->where("uid in({$data[uid]})")->setField('mail',1);

            }

        }catch (Exception $e){
            backJs('发送失败!');
            exit;
        }

       //--mail_log 表需求数据-
        $data_gm = array(
            'sid'        =>  $data['sid'],
            'sender'    =>  $_SESSION['ADMIN_ID'],
            'reward'    => json_encode($temp),
            'send_time' => time(),
            'login_ip'  => get_client_ip(),
            'status'    => 1,
            'type'      => $type,
            'title'     => $data['title'],
            'content'  => $data['description'],
            'uid'       => $data['uid'],
        );
        if($this->maillog_model->add($data_gm)){
            redirectJs('发送成功',U('Email/send_email'));
            exit;
        }else{
            redirectJs('发送失败', U('Email/send_email'));
            exit;
        }

    }

    /*
    * ajax-加载数据
    * **/
    public function get_props(){
        $server_id = $_GET['server_id'];

        die(json_encode(get_gm_server_props()));
    }

}