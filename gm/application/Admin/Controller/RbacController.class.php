<?php

/* * 
 * 系统权限配置，用户角色管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class RbacController extends AdminbaseController {

    protected $role_model, $auth_access_model;

    function _initialize() {
        parent::_initialize();
        $this->role_model = D("Common/Role");
    }

    /**
     * 角色管理，有add添加，edit编辑，delete删除
     */
    public function index() {
        $data = $this->role_model->order(array("listorder" => "asc", "id" => "desc"))->select();
        $this->assign("roles", $data);
        $this->display();
    }

    /**
     * 添加角色
     */
    public function roleadd() {
        $this->display();
    }
    
    /**
     * 添加角色
     */
    public function roleadd_post() {
    	if (IS_POST) {
    		if ($this->role_model->create()) {
    			if ($this->role_model->add()!==false) {
    				$this->success(L('ADD_SUCCESS'),U("rbac/index"));
    			} else {
    				$this->error(L('ADD_FAIL'));
    			}
    		} else {
    			$this->error($this->role_model->getError());
    		}
    	}
    }

    /**
     * 删除角色
     */
    public function roledelete() {
        $id = intval(I("get.id"));
        if ($id == 1) {
            $this->error(L('SUPER_ADMINISTRATOR_NOT_DELETED'));
        }
        $role_user_model=M("RoleUser");
        $count=$role_user_model->where("role_id=$id")->count();
        if($count){
        	$this->error(L('CHARACTER_ALREADY_HAS_USER'));
        }else{
        	$status = $this->role_model->delete($id);
        	if ($status!==false) {
        		$this->success(L('DELETE_SUCCESS'), U('Rbac/index'));
        	} else {
        		$this->error(L('DELETE_FAIL'));
        	}
        }
        
    }

    /**
     * 编辑角色
     */
    public function roleedit() {
        $id = intval(I("get.id"));
        if ($id == 0) {
            $id = intval(I("post.id"));
        }
        if ($id == 1) {
            $this->error(L('SUPER_ADMINISTRATOR_NOT_EDIT'));
        }
        $data = $this->role_model->where(array("id" => $id))->find();
        if (!$data) {
        	$this->error(L('CHARACTER_DOES_NOT_EXIST'));
        }
        $this->assign("data", $data);
        $this->display();
    }
    
    /**
     * 编辑角色
     */
    public function roleedit_post() {
    	$id = intval(I("get.id"));
    	if ($id == 0) {
    		$id = intval(I("post.id"));
    	}
    	if ($id == 1) {
    		$this->error(L('SUPER_ADMINISTRATOR_NOT_EDIT'));
    	}
    	if (IS_POST) {
    		$data = $this->role_model->create();
    		if ($data) {
    			if ($this->role_model->save($data)!==false) {
    				$this->success(L('CHANGE_SUCCESS'), U('Rbac/index'));
    			} else {
    				$this->error(L('UPDATE_FAILED'));
    			}
    		} else {
    			$this->error($this->role_model->getError());
    		}
    	}
    }

    /**
     * 角色授权
     */
    public function authorize() {
        $this->auth_access_model = D("Common/AuthAccess");
       //角色ID
        $roleid = intval(I("get.id"));
        if (!$roleid) {
        	$this->error(L('PARAMETER_ERROR'));
        }
        import("Tree");
        $menu = new \Tree();
        $menu->icon = array('│ ', '├─ ', '└─ ');
        $menu->nbsp = '&nbsp;&nbsp;&nbsp;';
        $result = $this->initMenu();
        $newmenus=array();
        $priv_data=$this->auth_access_model->where(array("role_id"=>$roleid))->getField("rule_name",true);//获取权限表数据
        foreach ($result as $m){
        	$newmenus[$m['id']]=$m;
        }
        
        foreach ($result as $n => $t) {
        	$result[$n]['checked'] = ($this->_is_checked($t, $roleid, $priv_data)) ? ' checked' : '';
        	$result[$n]['level'] = $this->_get_level($t['id'], $newmenus);
        	$result[$n]['parentid_node'] = ($t['parentid']) ? ' class="child-of-node-' . $t['parentid'] . '"' : '';
        }
        $str = "<tr id='node-\$id' \$parentid_node>
                       <td style='padding-left:30px;'>\$spacer<input type='checkbox' name='menuid[]' value='\$id' level='\$level' \$checked onclick='javascript:checknode(this);'> \$name</td>
	    			</tr>";
        $menu->init($result);
        $categorys = $menu->get_tree(0, $str);
        
        $this->assign("categorys", $categorys);
        $this->assign("roleid", $roleid);
        $this->display();
    }
    
    /**
     * 角色授权
     */
    public function authorize_post() {
    	$this->auth_access_model = D("Common/AuthAccess");
    	if (IS_POST) {
    		$roleid = intval(I("post.roleid"));
    		if(!$roleid){
    			$this->error(L('CHARACTER_DOES_NOT_EXIST'));
    		}
    		if (is_array($_POST['menuid']) && count($_POST['menuid'])>0) {
    			
    			$menu_model=M("Menu");
    			$auth_rule_model=M("AuthRule");
    			$this->auth_access_model->where(array("role_id"=>$roleid,'type'=>'admin_url'))->delete();
    			foreach ($_POST['menuid'] as $menuid) {
    				$menu=$menu_model->where(array("id"=>$menuid))->field("app,model,action")->find();
    				if($menu){
    					$app=$menu['app'];
    					$model=$menu['model'];
    					$action=$menu['action'];
    					$name=strtolower("$app/$model/$action");
    					$this->auth_access_model->add(array("role_id"=>$roleid,"rule_name"=>$name,'type'=>'admin_url'));
    				}
    			}
    
    			$this->success(L('AUTHORIZED_SUCCESS'), U("Rbac/index"));
    		}else{
    			//当没有数据时，清除当前角色授权
    			$this->auth_access_model->where(array("role_id" => $roleid))->delete();
    			$this->error(L('CLEANUP_AUTHORIZATION_SUCCESSFULLY'));
    		}
    	}
    }
    /**
     *  检查指定菜单是否有权限
     * @param array $menu menu表中数组
     * @param int $roleid 需要检查的角色ID
     */
    private function _is_checked($menu, $roleid, $priv_data) {
    	
    	$app=$menu['app'];
    	$model=$menu['model'];
    	$action=$menu['action'];
    	$name=strtolower("$app/$model/$action");
    	if($priv_data){
	    	if (in_array($name, $priv_data)) {
	    		return true;
	    	} else {
	    		return false;
	    	}
    	}else{
    		return false;
    	}
    	
    }

    /**
     * 获取菜单深度
     * @param $id
     * @param $array
     * @param $i
     */
    protected function _get_level($id, $array = array(), $i = 0) {
        
        	if ($array[$id]['parentid']==0 || empty($array[$array[$id]['parentid']]) || $array[$id]['parentid']==$id){
        		return  $i;
        	}else{
        		$i++;
        		return $this->_get_level($array[$id]['parentid'],$array,$i);
        	}
        		
    }
    
    
    public function member(){
    	//TODO 添加角色成员管理
    	
    }

}

