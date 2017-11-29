<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2015/7/15
 * Time: 19:12
 */

namespace Gm\Controller;
use Common\Controller\AdminbaseController;
class UinfoController extends AdminbaseController{
    private $gm_uinfo_disable;
    function _initialize(){
        parent::_initialize();
        $this->gm_uinfo_disable = M('gm_uinfo_disable');

    }
	/*
	**/
	public function index(){
		$this->userinfo();
		$this->display();
		
	}
	private function userinfo(){
		$server_id = I('server_id');
		$uid = I('uid');
		if(!empty($server_id) && !empty($uid)){
			if(empty($uid)){
				$this->error(L('UID_NOT_EMPTY'),U('PlayerItems/index'));
			}
			if($server_id){
				$where['uid'] = $uid;
				$where['serverid'] = $server_id;
			}
			$db_config = gm_server_config($server_id);
			$uinfo = M('uinfo',null,$db_config)->where($where)->field('uid,uname,forbid,forbidreason,forbidtime')->find();

			if(empty($uinfo)){
				$this->error(L('UID_DOES_NOT_EXIST'));
			}
			$redis=redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->SISMEMBER('chat_user_banned',serialize($server_id."_".$uid));
			$redis2=redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->EXISTS('user_key:'.$uid.':'.$server_id);
			$uinfo['isspeak']=$redis;
			$uinfo['iskick']=$redis2;
			$this->assign("posts", $uinfo);
		}
		
	 }
	/*
	*封停
	*/
	public function disable(){
		if(IS_POST){
			 writeLog('uinfo_disable');
			$time_type    = I('type');
			$forbidtime   = I('forbidtime');
			$forbidreason = I('forbidreason');
			$uid          = I('uid');
			$serverid     = I('serverid');
			
			switch($time_type){
			    case 'hour':
				$time = date("Y-m-d H:i:s",(time()+3600*$forbidtime));
				break;
				case 'min':
				$time = date("Y-m-d H:i:s",(time()+60*$forbidtime));
				break;
				default:
				$time = date("Y-m-d h:i:s",strtotime("+$forbidtime $time_type"));
			    break;
			}
			
			$data = array(
			   'forbid'=>1,
			   'forbidreason'=>disable_uinfo($forbidreason),
			   'forbidtime'=>strtotime($time),
			);
			redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->hSet('blockUserList:'.$serverid,$uid,serialize($data));
			try{
				$db_config = gm_server_config($serverid);
                $uname = M('uinfo',null,$db_config)->where(array('uid'=>$uid,'serverid'=>$serverid))->getField('uname');

				/*
                *记录日志
                */
				$data_disable = array(
					'uid'=>$uid,
					'forbidreason'=>$forbidreason,
					'forbidtime'=>$forbidtime.get_time_date($time_type),
					'serverid'=>$serverid,
					'cts'     =>time(),
					'type'    =>1,
					'uname' =>$uname
				);

				M('uinfo',null,$db_config)->where(array('uid'=>$uid,'serverid'=>$serverid))->save($data);
				$this->gm_uinfo_disable->add($data_disable);

			}catch(Exception $e){
				$this->error($e->getMessage());
			}


			$this->success(L('CHANGE_SUCCESS'));

			
		}else{
			$uid = I('uid');
			$serverid = I('serverid');
			if(!isset($uid) && !isset($serverid)){
				$this->error(L('PARAMETER_ERROR'));
			}
			$this->assign('uid',$uid);
			$this->assign('serverid',$serverid);
			$this->display();
		}
		
		
	}
	/*
	*解封
	*/
	public function enable(){
		if(IS_POST){
			writeLog('uinfo_enable');
			$uid = I('uid');
			$serverid  = I('serverid');
			$forbidreason = I('forbidreason');
			redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->hdel('blockUserList:'.$serverid,$uid);
			try{
			$data = array(
				'forbid'=>0,
				'forbidreason'=>'',
				'forbidtime'=>0,
			);

			
			$db_config = gm_server_config($serverid);
				$uname = M('uinfo',null,$db_config)->where(array('uid'=>$uid,'serverid'=>$serverid))->getField('uname');
						/*
		*记录日志
		*/
				$data_disable = array(
					'uid'=>$uid,
					'forbidreason'=>$forbidreason,
					'serverid'=>$serverid,
					'cts'     =>time(),
					'type'    =>2,
					'uname' =>$uname
			);
			M('uinfo',null,$db_config)->where(array('uid'=>$uid,'serverid'=>$serverid))->save($data);
			$this->gm_uinfo_disable->add($data_disable);
			
			}catch(Exception $e){
			 $this->error($e->getMessage());
			}
			$this->success(L('CHANGE_SUCCESS'));
		}else{
			$uid = I('uid');
			$serverid = I('serverid');
			if(!isset($uid) && !isset($serverid)){
				$this->error(L('PARAMETER_ERROR'));
			}
			$this->assign('uid',$uid);
			$this->assign('serverid',$serverid);
			$this->display();
		}
		
		
	}
	/*
	*禁言
	*/
	public function Gag(){

			$uid = I('uid');
			$serverId  = I('serverid');
			$resute=redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->sadd('chat_user_banned',serialize($serverId."_".$uid));
			if($resute){
				$this->success(L('GAG_SUCCESS'));

			}

	}
	/*
*解禁
*/
	public function Lift_a_ban(){

			$uid = I('uid');
			$serverId  = I('serverid');
			if(redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->srem('chat_user_banned',serialize($serverId."_".$uid))){
				$this->success(L('BAN_SUCCESS'));
			}
		}
	/*
    *踢人
    */
	public function kick_away(){

	$uid = I('uid');
	$serverId  = I('serverid');
	if(redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del('user_key:'.$uid.':'.$serverId)){
		$this->success(L('KICK_AWAY_SUCCESS'));
	}
}

}

