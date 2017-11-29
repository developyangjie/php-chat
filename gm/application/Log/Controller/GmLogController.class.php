<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2015/7/15
 * Time: 19:12
 */

namespace Log\Controller;
use Common\Controller\AdminbaseController;
class GmLogController extends AdminbaseController{
    protected $gmlog_model;
    function _initialize(){
        parent::_initialize();
        //$this->gameitems_model = D('Game/GameItems');
		$this->gmlog_model = D('Log/GmLog');
    }
    public function index(){

        $where=array();
        $action =I("action");
        if(!empty($action)){
            $where['action']=$action;
        }
        $start_time = I('start_time');
        $end_time = I('end_time');
        if($start_time && $end_time){
            $where['actiontime'] = array('between',array($start_time,date("Y-m-d",(strtotime($end_time)+86400))));
        }
        $count=$this->gmlog_model->where($where)->count();
        $page = $this->page($count, 20);
        $comments=$this->gmlog_model
            ->where($where)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("actiontime DESC")
            ->select();
        $this->assign("comments",$comments);
        $this->assign("Page", $page->show('Admin'));

		
		$this->display();
    }
	
	/*
	*添加道具
	*/
	public function add_props(){
		if(IS_POST){
            $data = $_POST;
            // 判断是否存在道具
            $temp = array();
            if (!empty($data['reward_id'])) {
                foreach ($data['reward_id'] as $k => $v) {
                    if (isset($temp[$v])) { // 若已经存在 则进行累加
                        $temp[$v]['count'] += (int)$data['reward_num'][$k];
                    } else {
                        $temp[$v] = array(
                            'itemid' => (int)$data['reward_id'][$k],
                            'count' => (int) $data['reward_num'][$k],
                            'uid'  => (int) $data['uid'],
                        );
                    }
                }
            }
            $db_config = gm_server_config($data['server_id']);

           try{
                foreach($temp as $item){
                    M('uitem',null,$db_config)->execute("insert into uitem (uid,itemid,count) values ($item[uid],$item[itemid],$item[count]) on DUPLICATE KEY update count=count+$item[count]");
                }
            }catch (Exception $e){
                $this->error("增加失败");
            }
            $this->success("增加成功",U('User/index',array('name'=>$data['uid'])));

			
		}else{
			$uid = I('uid');
			$server_id = I('server_id');
            if(empty($uid) || empty($server_id)) $this->error('UID或者server_id为空');
			$list = getAllItem();
			$this->assign('props_list',$list);
			$this->display();	
			
		}
		
	}
    /*
     * 批量发送道具
     * **/
    public function batch_send_props(){

       if(IS_POST){
           //--写入日志--
            writeLog('batch_send_props');
            if(empty($_POST['uid'])){
                $this->error('UID不能为空!');
            }
            $data = $_POST;
           if (!empty($data['data'])) {
               $newArr = array();
               foreach($data['data'] as $ids=>$item){
                  $newArr[$ids] = explode("-",$item);
               }
               $uids = explode(",",$data['uid']);
               $server_id = $data['server_id'];
               $db_config = gm_server_config($server_id);
               try{
                   foreach($uids as $temp){
                       foreach($newArr as $val){
                           M('uitem',null,$db_config)->execute("insert into uitem (uid,itemid,count) values ($temp,$val[0],$val[1]) on DUPLICATE KEY update count=count+$val[1]");
                       }
                   }

               }catch (Exception $e){
                   redirectJs("增加失败",U('User/batch_send_props'));
               }
               redirectJs("增加成功",U('User/batch_send_props'));
            }else{
               $this->error('道具不能为空');
           }
       }

       $this->display();
    }
    /*
     * ajax-加载数据
     * **/
    public function get_props(){
        $server_id = $_GET['server_id'];
        die(json_encode(getAllItem()));
    }


    protected function _list(){
        $where = array();
        $ge_gid = I('get.ge_gid');
        if(!empty($ge_gid)){
            $where['ge_gid'] = $ge_gid;
        }
        $count = $this->gameitems_model->where($where)->count();// 查询满足要求的总记录数
        $Page = $this->page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show('Admin');// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //$list = $this->gameitems_model->alias('g')->join(C('DB_PREFIX')."game_info i on g.ge_gid = i.gi_id",'LEFT')->field('g.*,i.*')->where($where)->order("g.ge_id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('gid',$ge_gid);
        $this->assign('lists',$list);// 赋值数据集
        $this->assign('Page',$show);// 赋值分页输出
    }
    public function edit(){
        $id = I('get.ge_id',0);
        $post = $this->gameitems_model->where("ge_id = $id")->find();
        if(!empty($post)){
            $this->assign('post',$post);
            $this->display();
        }else{
            $this->error("数据错误！");
        }
    }
    public function edit_post(){
        if(IS_POST){
            $gameitems= I("post.post");
            $result = $this->gameitems_model->save($gameitems);
            if($result !== false){
                $this->success("保存成功！");
            }else{
                $this->error("保存失败！");
            }
        }
    }
    public function delete(){
        if(isset($_GET['ge_id'])){
            $id = intval(I('get.ge_id'));
            if($this->gameitems_model->where("ge_id = $id")->delete()){
                $this->success("删除成功！");
            }else{
                $this->error("删除失败！");
            }
        }
        if(isset($_POST['ids'])){
            $ids = join(',',$_POST['ids']);
            if($this->gameitems_model->where("ge_id in ($ids)")->delete()){
                $this->success("删除成功！");
            }else{
                $this->error("删除失败！");
            }
        }
    }

    public function add(){
        $this->assign('gid',I('get.gid'));
        $this->display();
    }

    public function add_post(){
        if(IS_POST){
            $data = I('post.post');
            $result = $this->gameitems_model->add($data);
            if($result){
                $this->success("添加成功！道具ID为 $result");
            }else{
                $this->error('添加失败！');
            }
        }
    }
}