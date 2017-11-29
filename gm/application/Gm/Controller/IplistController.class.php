<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2015/12/22
 * Time: 19:12
 */

namespace Gm\Controller;
use Common\Controller\AdminbaseController;
class IplistController extends AdminbaseController{
    function _initialize(){
        parent::_initialize();
    }
    /*
     * ip列表
     * **/
    public function index($serverid = null,$type = null){

        $serverid  = empty($serverid) ? I('server_id') : $serverid;
		$type  =empty($type) ?  I('type') :$type;
        if(!empty($serverid)&&!empty($type)){
        	if($type==1)
        	{
            	//---得到配置db
	            $db_config = gm_server_config($serverid);
	            $gm_uinfo = M("ipwhitetable",null,$db_config);
	            $data=$gm_uinfo->where("server_id = $serverid")->select();
        	}
        	else if($type==2)
        	{	
        		//---得到配置db
        		$db_config = gm_server_config($serverid);
        		$gm_uinfo = M("ipblacktable",null,$db_config);
        		$data=$gm_uinfo->where("server_id = $serverid")->select();
        		
        	}

        }

        $this->assign('posts',$data)->assign('type',$type);
		$this->display("index");
	}
	
	/**
	 *  删除
	 */
	function delete(){
			$serverid = I('server_id');
			$type = I('type');
			$ips = I('ips');
				$db_config = gm_server_config($serverid);
				$gm_uinfo = M("ipwhitetable", null, $db_config);
				$ipblacktable = M("ipblacktable", null, $db_config);
				if ($ips) {
					$ipss = implode(',', $ips);
					$where['ip'] = array('in', $ipss);
					$where['server_id'] = $serverid;
					if($type == 1){
						if ($gm_uinfo->where($where)->delete()) {
							foreach ($ips as $value) {
								redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->SREM('ipwhitetable:' . $serverid, $value);
							}
							A('Gm/Iplist')->index($serverid, $type);

						} else {
							$this->error(L('DELETE_FAIL'));
						}
					}elseif($type == 2){
						if ($ipblacktable->where($where)->delete()) {
							foreach ($ips as $value) {
								redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->SREM('ipblacktable:' . $serverid, $value);
							}
							A('Gm/Iplist')->index($serverid, $type);

						} else {
							$this->error(L('DELETE_FAIL'));
						}
					}

				}
	}
	
    /*
    * add
    * **/
    public function add(){
        if(IS_POST){
            writeLog('iplist_add');
            $data = $_POST;
//             $version = trim($data['version']);
//             $this->_version_number($version);
            try{
            	$ip=$data['ip'];
            	$serverid=$data['server_id'];
            	$type=$data['type'];
            	if($type==1)
            	{
            		//---得到配置db
//             		$db_config = gm_server_config($serverid);
//             		$gm_uinfo = M("ipwhitetable",null,$db_config);
//             		$data=$gm_uinfo->where()->select();
            		$db_config = gm_server_config($serverid);
            		$gm_uinfo = M("ipwhitetable",null,$db_config);
					if($gm_uinfo->add($data)){
						redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del('ipwhitetable:'.$serverid);
					}
					$data=$gm_uinfo->where("server_id = $serverid")->select();
					$ips = array_column($data, 'ip');
					foreach($ips as $value){
						redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->sadd('ipwhitetable:'.$serverid,$value);
					}
            	}
            	else if($type==2)
            	{
            		//---得到配置db
					$db_config = gm_server_config($serverid);
            		$gm_uinfo = M("ipblacktable",null,$db_config);
					if($gm_uinfo->add($data)){
						redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del('ipblacktable:'.$serverid);
					}
					$data=$gm_uinfo->where("server_id = $serverid")->select();
					$ips = array_column($data, 'ip');
					foreach($ips as $value){
						redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->sadd('ipblacktable:'.$serverid,$value);
					}

            	
            	}
            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success(L('ADD_SUCCESS'));
        }else{
            $this->display();
        }

    }

}