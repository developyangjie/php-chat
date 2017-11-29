<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2015/7/15
 * Time: 19:12
 */

namespace Gm\Controller;
use Common\Controller\AdminbaseController;
class UserController extends AdminbaseController{
   // protected $gameitems_model;
    function _initialize(){
        parent::_initialize();

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

           if (!empty($data)) {
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
               }else{
                   $this->error('道具不能为空!');
               }
                $temp = array_values($temp);
                $uids = explode(",",$data['uid']);

               try{
                   $db_config = gm_server_config($data['server_id']);
                   foreach($uids as $info){
                       foreach($temp as $val){
                         M('uitem',null,$db_config)->execute("insert into uitem (uid,itemid,count) values ($info,$val[itemid],$val[count]) on DUPLICATE KEY update count=count+$val[count]");
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


}