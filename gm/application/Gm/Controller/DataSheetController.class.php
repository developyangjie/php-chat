<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2015/7/15
 * Time: 19:12
 */

namespace Gm\Controller;
use Common\Controller\AdminbaseController;
class DataSheetController extends AdminbaseController{
    protected $user_model;
    function _initialize(){
        parent::_initialize();
        $this->user_model = M('users');
    }
    public function add(){
        if(IS_POST){
            var_dump($_POST['tabel_name']);exit;
            $this->impUser();
        }


        $this->display();
    }

    private function impUser(){
        if (!empty($_FILES)) {

            import("@.ORG.UploadFile");
            $config=array(
                'allowExts'=>array('xlsx','xls'),
                'savePath'=>'./data/upload/',
                'saveRule'=>'time',
            );
            $upload = new \UploadFile($config);
            if (!$upload->upload()) {
                $this->error($upload->getErrorMsg());
            } else {
                $info = $upload->getUploadFileInfo();

            }

            vendor("PHPExcel.PHPExcel");
            $file_name=$info[0]['savepath'].$info[0]['savename'];

            $extension = $_FILES['excel']['name'];
            $sd  = explode(".",$extension);
            if( $sd[1] =='xlsx' )
            {
                $objReader = new \PHPExcel_Reader_Excel2007();
            }
            else
            {
                $objReader = new \PHPExcel_Reader_Excel5();

            }
            //  $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load($file_name,$encode='utf-8');
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            // 'cfg_maintask'=>'主线流失任务配置',
            //'cfg_ordinary'=>'普通副本',
            //'cfg_jingyin'=>'精英副本',
            //'cfg_special'=>'特殊副本',
            //'cfg_worldboss'=>'世界boss',
            $tabel_name = $_POST['tabel_name'];
            for($i=3;$i<=$highestRow;$i++)
            {
                if($tabel_name=='cfg_maintask'){
                    $taskid =  $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                    $name= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                    $desc= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                    M('cfg_maintask',null,DB_CONFIG_PLATFORM)->execute("insert into cfg_maintask (taskid,`name`,`desc`) values ($taskid,'$name','$desc') on DUPLICATE KEY update `name`='$name',`desc`='$desc'");

                }else{
                    /*任务关卡**/
                    $type = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                    switch($type){
                        case 1:
                            $oid= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                            $name= $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                            $desc= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                            M('cfg_ordinary',null,DB_CONFIG_PLATFORM)->execute("insert into cfg_ordinary (oid,`name`,`desc`) values ($oid,'$name','$desc') on DUPLICATE KEY update `name`='$name',`desc`='$desc'");
                            break;
                        case 2:
                            $oid= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                            $name= $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                            $desc= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                            M('cfg_jingyin',null,DB_CONFIG_PLATFORM)->execute("insert into cfg_jingyin (oid,`name`,`desc`) values ($oid,'$name','$desc') on DUPLICATE KEY update `name`='$name',`desc`='$desc'");
                            break;
                        case 3:
                            $oid= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                            $name= $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                            $desc= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                            // M('cfg_special',null,DB_CONFIG_PLATFORM)->execute("insert into cfg_special (oid,name,desc) values ($oid,$name,$desc) on DUPLICATE KEY update name=$name,desc=$desc");
                            M('cfg_special',null,DB_CONFIG_PLATFORM)->execute("insert into cfg_special (oid,`name`,`desc`) values ($oid,'$name','$desc') on DUPLICATE KEY update `name`='$name',`desc`='$desc'");
                            break;
                        case 4:
                            $oid= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                            $name= $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                            $desc= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                            M('cfg_worldboss',null,DB_CONFIG_PLATFORM)->execute("insert into cfg_worldboss (oid,`name`,`desc`) values ($oid,'$name','$desc') on DUPLICATE KEY update `name`='$name',`desc`='$desc'");
                            break;
                    }
                }


            }

            $this->success('导入成功！');
        }else
        {
            $this->error("请选择上传的文件");
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
        $this->assign('item_info',getAllItem());
        $this->display();
    }


}