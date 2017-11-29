<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2016/01/15
 * Time: 19:12
 */

namespace Gm\Controller;
use Common\Controller\AdminbaseController;
class UinfoLogController extends AdminbaseController{
    private $gm_uinfo_disable;
    function _initialize(){
        parent::_initialize();
        $this->gm_uinfo_disable = M('gm_uinfo_disable');

    }
	public function index(){
		$where = array();
		$serverid  = I('server_id');
		$uid   = I('uid');
		$type  = I('type');
		$sts   = I('start_time');
		$ets   = I('end_time');
		if(!empty($serverid)) $where['serverid'] = $serverid;
		if(isset($uid) && $uid>0) $where['uid'] = $uid;
		if(!empty($type)) $where['type'] = $type;
		if($sts>0) $where['cts'] = array('egt',strtotime($sts));
		if($ets>0) $where['cts'] = array('lt',strtotime($ets)+86400);
		
		$count = $this->gm_uinfo_disable->where($where)->count();
		$Page = $this->page($count,20);
		$show = $Page->show('Admin');

		$list = $this->gm_uinfo_disable->where($where)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('lists',$list);
		$this->assign('Page',$show);
		$this->display();
		
	}
	
	
}