<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2015/12/22
 * Time: 19:12
 */

namespace Gm\Controller;
use Common\Controller\AdminbaseController;
class ServerController extends AdminbaseController{
    protected $serverlist_model;
	protected $server_patch_model;
    function _initialize(){
        parent::_initialize();
        $this->serverlist_model = M('server_list',null,DB_CONFIG_PLATFORM);
		$this->server_patch_model = M('server_patch',null,DB_CONFIG_PLATFORM);
    }
    /*
     * 服务器列表
     * **/
    public function index(){

        $where = array();
        $name = I('name');
        if(!empty($name)){
            $where['name'] = array('like','%'.$name.'%');
        }
        $server_list = $this->serverlist_model->where($where)->order("id asc")->select();
        $this->assign('posts',$server_list);
		$this->display();
	}
    /*
    * add
    * **/
    public function add(){
        if(IS_POST){
            writeLog('server_add');
            $data = $_POST;
            $version = trim($data['version']);
			$this->delete_redis_key();
            try{
				$this->serverlist_model->add($data);
            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success(L('ADD_SUCCESS'));
        }else{
            $this->display();
        }

    }
	
    public function merge(){
		$this->error(L('TEMPORARILY_UNAVAILABLE'));
    	if(IS_POST)
    	{
    		
    	    writeLog('server_add');
            $data = $_POST;
            try{
            	$gid=intval($data['mergeserver']);
            	$servername=$data['mergename'];
            	if(!isset($gid))
            	{
            		$this->error('合并后服务器id未填写');
            	}
            	$severlist=$this->serverlist_model;
            	$sid1=intval($data['server1']);
            	$sid2=intval($data['server2']);
            	if(isset($sid1)&&isset($sid2))
            	{
            		//**********************主服务器
	            	//---得到配置db
	            	$db_config = gm_server_config($sid1);
	            	//-uinfo-表
	            	$gm_uinfo = M("uinfo",null,$db_config);
            		//修改主表
	            	$gm_uinfo->execute("update uinfo set serverid=$gid where serverid=$sid1");
	            	//删除每个服单独功能表
	            	$map['id']=$sid1;
	            	$data=$severlist->where($map)->select();
// 	            	var_dump($data);
	            	$ip=$data[0]['ip'];
	            	$dbhost=$data[0]['dbhost'];
	            	$dbname=$data[0]['dbname'];
	            	$dbuser=$data[0]['dbuser'];
	            	$dbpass=$data[0]['dbpass'];
	            	//---得到配置db
	            	$conn=mysql_connect($dbhost,$dbuser,$dbpass) or die("error connecting") ; //连接数据库
	            	mysql_query("set names 'utf8'"); 
	            	mysql_select_db($dbname); //打开数据库
	            	$oldtablename='upvp_'.$sid1;
	            	$newtablename='upvp_'.$gid;
	            	$oldtablename2='upartnerarena_'.$sid1;
	            	$newtablename2='upartnerarena_'.$gid;
	            	$sql="delete from $oldtablename";
	            	mysql_query($sql,$conn);
	            	$sql="ALTER TABLE $oldtablename RENAME TO $newtablename";
	            	mysql_query($sql,$conn);
	            	$sql="delete from $oldtablename2";
	            	mysql_query($sql,$conn);
	            	$sql="ALTER TABLE $oldtablename2 RENAME TO $newtablename2";
	            	mysql_query($sql,$conn);	            	
	            	//修改服务器列表
	            	$severlist->execute("update server_list set id=$gid,name='$servername' where id=$sid1");
	            	
	            	
	            	//************************被合并服务器**************************
            		//---得到配置db
            		$db_config = gm_server_config($sid2);
            		//-uinfo-表
            		$gm_uinfo2 = M("uinfo",null,$db_config);      	
            		$gm_uinfo2->execute("update uinfo set serverid=$gid where serverid=$sid2");
            		//删除每个服单独功能表
            		$map['id']=$sid2;
	            	$data=$severlist->where($map)->select();
            		$ip=$data['ip'];
	            	$dbhost=$data[0]['dbhost'];
	            	$dbname=$data[0]['dbname'];
	            	$dbuser=$data[0]['dbuser'];
	            	$dbpass=$data[0]['dbpass'];
            		//---得到配置db
            		$conn=mysql_connect($dbhost,$dbuser,$dbpass) or die("error connecting") ; //连接数据库
            		mysql_query("set names 'utf8'");
            		mysql_select_db($dbname); //打开数据库
            		$oldtablename='upvp_'.$sid2;
            		$oldtablename2='upartnerarena_'.$sid2;
            		
            		$sql="DROP TABLE $oldtablename";                                                                                                              
            		mysql_query($sql,$conn);
            		$sql="DROP TABLE $oldtablename2";
            		mysql_query($sql,$conn);

            		$severlist->execute("delete from server_list where id=$sid2");
            	}
            	
				 $Url=C('url');
            	 $post_string ="";
            	 $ch = curl_init ();
            	 curl_setopt ( $ch, CURLOPT_URL, $Url );
            	 curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
            	 curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_string );
            	 curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
            	 curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 0 );
            	 curl_setopt ( $ch, CURLOPT_TIMEOUT, 30 );
            	 $result = curl_exec ( $ch );
            	 $res=json_decode($result);
            	                                                                                  	
            	redirectJs("合并成功",U('Server/merge'));

            	
            }catch (Exception $e){
                $this->error($e->getMessage());
                $this->error('合并失败');
            }
    	}
    	else 
    	{
    		$this->display();
    	}
    	
    }
    /*
     * edit
     * **/
    public function edit(){
        if(IS_POST){
            writeLog('server_edit');
            $data = $_POST;
            $id = $data['id'];
            $version = trim($data['version']);
            $old_version = $data['old_version'];
            unset($data['id'],$data['old_version']);
			$this->delete_redis_key();
            try{
				if($old_version != $version){
					$this->serverlist_model->where(1) ->setField("version","$version");
				}else{
					$this->serverlist_model->where(array('id'=>$id))->save($data);
				}


            }catch (Exception $e){
                $this->error($e->getMessage());
            }
            $this->success(L('CHANGE_SUCCESS'));
        }
        $id = I('get.id',0);
        $posts =  $this->serverlist_model->where(array('id'=>$id))->find();
        $this->assign('posts',$posts);
        $this->display();
    }
	public function server_patch(){

		$where = array();
		$version = I('version');
		if(!empty($version)){
			$where['version'] = array('like','%'.$version.'%');
		}
		$server_list = $this->server_patch_model->where($where)->order("id desc")->select();
		$this->assign('post',$server_list);
		$this->display('server_patch');
	}
	public function edits(){
		if(IS_POST){
			writeLog('edits');
			$data = $_POST;
			$id = $data['id'];
			$version = trim($data['version']);
			$old_version = $data['old_version'];
			unset($data['id'],$data['old_version']);

			if($version != $old_version){
				if($version<$old_version){
					$this->error(L('CURRENT_VERSION_NUMBER'));
				}
			}
			try{
				$this->server_patch_model->where(array('id'=>$id))->save($data);
			}catch (Exception $e){
				$this->error($e->getMessage());
			}
			$this->success(L('CHANGE_SUCCESS'));
		}
		$id = I('get.id',0);
		$posts =  $this->server_patch_model->where(array('id'=>$id))->find();
		$this->assign('posts',$posts);
		$this->display();
	}
	public function adds_patch(){
		if(IS_POST){
			writeLog('adds');
			$data = $_POST;
			try{
				$this->server_patch_model->add($data);
			}catch (Exception $e){
				$this->error($e->getMessage());
			}
			$this->success(L('ADD_SUCCESS'));
		}
		$this->display();
	}
	public function delete(){
		if(isset($_GET['id'])){
			writeLog('delete');

			$id = intval(I('get.id'));
			try{
				$this->server_patch_model->where(array('id'=>$id))->delete();
			}catch (Exception $e){
				$this->error($e->getMessage());
			}
			$this->success(L('DELETE_SUCCESS'));
		}
	}

	private function delete_redis_key(){
		redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del('serverList');
		redis_instance(C("REDIS_HOST"), C("REDIS_PORT"), C("REDIS_AUTH"))->del('serverListByIdAsc');
	}
}