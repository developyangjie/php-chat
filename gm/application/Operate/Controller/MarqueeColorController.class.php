<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2015/7/15
 * Time: 19:12
 */

namespace Operate\Controller;
use Common\Controller\AdminbaseController;
class MarqueeColorController extends AdminbaseController{
    protected $marqueecolor_model;
    function _initialize(){
        parent::_initialize();
        $this->marqueecolor_model = D('Operate/MarqueeColor');
	}
    public function index(){
		$count=$this->marqueecolor_model->count();
		$page = $this->page($count,20);
		$posts=$this->marqueecolor_model->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign("Page", $page->show('Admin'));
		$this->assign("posts", $posts);
		$this->display();
    }
    //
    public function add(){

        if(IS_POST){
			$data = $_POST;
			if($this->marqueecolor_model->add($data)){
				$this->success('添加成功',U('MarqueeColor/index'));
			}else{
				$this->error('添加失败!');
			}
        }
        $this->display();
    }

	public function edit(){
		if(IS_POST){
			$id = $_POST['id'];
			$data = $_POST;
			unset($data['id']);
			try{
				$this->marqueecolor_model->where("id=".$id)->save($data);
			}catch (Exception $e){
				$this->error($e->getMessage());
			}
			$this->success('更改成功！',U('MarqueeColor/index'));

		}

		$ids = $_GET['id'];
		$posts = $this->marqueecolor_model->where("id=".$ids)->find();
		$this->assign('posts',$posts);
		$this->display();
	}

}