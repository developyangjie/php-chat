<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2015/7/15
 * Time: 19:12
 */

namespace Operate\Controller;
use Common\Controller\AdminbaseController;
class PlayerItemsController extends AdminbaseController{

    function _initialize(){
        parent::_initialize();
	}
    public function index(){
		$server_id = I('server_id');
		$where = array();
		if(IS_POST){
		  writeLog('player_index');
	      $this->userinfo($server_id);
		}
		$this->display();
    }

	/*
    * 删除道具，物品
    * ***/
	public function edits(){
		$server_id   = I('serverid');
		if(IS_POST){
			writeLog('edits');
			$uid          = I('uid');
			$uexp = I('uexp');
			$vip  = I('vip');

			$ug  = I('ug');
			if($ug != null){
				$data["ug"] = $ug;
			}
			$ucoin = I('ucoin');
			if($ucoin != null){
				$data["ucoin"] = $ucoin;
			}

           // $foodcnt = I('foodcnt');

			$db_config = gm_server_config($server_id);
            /**
            *查询cfg_userlv 得到经验所在的等级** 记录
			 */
			if($uexp !=null){
				$data["uexp"] = $uexp;
				$lv = 0;
				$cfg_userlv = M('cfg_userlv',null,$db_config)->where("maxexp>$uexp")->field('lv')->order("maxexp asc")->find();
				if(!empty($cfg_userlv)){
					$ulv = $cfg_userlv['lv'];
					$data["ulv"] = $ulv;
				}
			}

			/*
			 * 查询vip配置表
			 * ***/
			if($vip!=null){
				$data["vip"] = $vip;
				$cfg_vip = M('cfg_vip',null,$db_config)->where("vip=".$vip)->field("pay")->find();
				$vippay = 0;
				if(!empty($cfg_vip)){
					$vippay = $cfg_vip['pay'];
					$data["vippay"] = $vippay;
				}
			}

			/*
			 * 更改道具
			 * ***/
			$temp = array();
			$itemid = I('itemid');
			$count = I('count');
			if (!empty($itemid)) {
				foreach ($itemid as $k => $v) {
					if (isset($temp[$v])) { // 若已经存在 则进行累加
						$temp[$v]['count'] += $count[$k];
					} else {
						$temp[$v] = array(
							'itemid' => $itemid[$k],
							'count' => (int) $count[$k]
						);
					}
				}
			}
			$temp = array_values($temp);
			try {
				if(!empty($temp)){
					foreach($temp as $val){
						M('uitem',null,$db_config)->execute("insert into uitem (uid,itemid,count) values ($uid,$val[itemid],$val[count]) on DUPLICATE KEY update count=".$val['count']);
					}
				}
				/*
				 * 更改 ulv，uexp,vip,vippay,ug,ucoin
				 * ***/
				M("uinfo",null,$db_config)->where("uid=".$uid)->save($data);

			} catch (Exception $e) {
				 $this->error(L('UPDATE_FAILED'));
				  $e->getMessage();

			}

			$this->success(L('CHANGE_SUCCESS'));



		}else{
			$this->userinfo($server_id);

			$this->assign('uid',$_GET['uid']);
			$this->assign('serverid',$server_id);
			$this->display();
		}


	}
	public function update(){
		writeLog('update');
		$cuid = $_GET['cuid'];
		$result  = 1;
		try{
			if(!empty($cuid)){
				$cusermodel = M('cuser',null,DB_CONFIG_PLATFORM);
				$busermodel = M('buser',null,DB_CONFIG_PLATFORM);
				$cusermodel->where("uid = ".$cuid)->delete();
				$busermodel->where("uid = ".$cuid)->delete();
			}
		}catch(Exception $e){
			$result = 0;
			$this->error(L('EMPTY_DATA_FAILED'));
		}
		header('Content-Type:application/json; charset=utf-8');
		exit(json_encode($result));
	}
	private function userinfo($server_id){

		$uid = I('uid');
		if(empty($uid)){
			$this->error(L('UID_NOT_EMPTY'),U('PlayerItems/index'));
		}
		if($server_id){
			$where['uid'] = $uid;
			$where['serverid'] = $server_id;
		}
		$db_config = gm_server_config($server_id);
		/*
         * 得到元宝和铜钱 vip,等级 uexp 经验
         * ***/
		$uinfo = M('uinfo',null,$db_config)->where($where)->field('uname,uid,ucoin,ug,vip,ulv,uexp,cuid')->find();

		if(empty($uinfo)){
			$this->error(L('UID_DOES_NOT_EXIST'));
		}
		/*
         * 得到粮草
         * ***/
		$ushoucai = '';
		//$ushoucai = M('ushoucai',null,$db_config)->where($where)->field('foodcnt')->find();
		/*
         *得到道具物品
         * **/
		$items = M('uitem',null,$db_config)->query("select u.itemid,c.name,u.count from uitem u inner join cfg_item c on u.itemid=c.itemid and u.uid=".$uid);

		if(!empty($items)){
			$newitem['item'] = $items;
		}else{
			$newitem['item'] = null;
		}
		$posts = array_merge($uinfo,$newitem);
		$this->assign("posts", $posts);
		$this->assign("serverid", $server_id);

	}




}