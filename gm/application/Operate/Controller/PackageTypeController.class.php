<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2015/7/15
 * Time: 19:12
 */

namespace Operate\Controller;
    use Common\Controller\AdminbaseController;
    use Think\Exception;

CONST PAGE_SIZE = 500;
class PackageTypeController extends AdminbaseController{
    protected $package_type;
    protected  $gm_card_info;
    function _initialize(){
        parent::_initialize();
        $this->package_type = M('gm_code_setting');
        $this->gm_card_info = M('gm_card_info');
	}
    public function index(){
        $this->_list();
        $this->display();
    }
    private function _list(){
        $where = array();
        if(IS_POST){
            if(!empty($_POST['title'])){
                $game_name = $_POST['title'];
                $where['title'] = array('like', '%' . (string)$game_name . '%');
            }
        }

        $count = $this->package_type->where($where)->count();// 查询满足要求的总记录数
        $Page = $this->page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show('Admin');// 分页显示输出
        $list = $this->package_type->where($where)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('lists',$list);// 赋值数据集
        $this->assign('Page',$show);// 赋值分页输出
    }
    /*
     * add
     * ***/
    public function add(){
        if(IS_POST){
            $data = $_POST;
            writeLog('gm_code_add');
            if(empty($data['title'])) $this->error(L('TITLE_NAME_CANNOT_BE_EMPTY'));
            if(empty($data['code_pre'])) $this->error(L('ACTIVITY_CODE_PREFIX_NOT_EMPTY'));
            if(empty($data['code_num'])) $this->error(L('ACTIVITY_PACKS_CODE_NUMBER_NOT_EMPTY'));
            if(empty($data['content'])) $this->error(L('ACTIVITY_PACKS_USE_CANNOT_EMPTY'));
            if(preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$data['code_pre'])) $this->error(L('CARD_NUMBER_PREFIX'));
            if(strlen($data['code_pre']) !=2){
                $this->error(L('PACKAGE_CODE_PREFIX'));
            }

            // 判断是否存在奖品
            $temp = array();
            if (!empty($data['itemid'])) {
                foreach ($data['itemid'] as $k => $v) {
                    if (isset($temp[$v])) { // 若已经存在 则进行累加
                        $temp[$v]['num'] += $data['count'][$k];
                    } else {
                        $temp[$v] = array(
                            'itemid' => $data['itemid'][$k],
                            'num' => (int) $data['count'][$k]
                        );
                    }
                }
            }
            $temp = array_values($temp);
            unset($data['itemid'],$data['count']);
            $data['itemids'] = json_encode($temp);
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);

            try{
               $this->package_type->add($data);
            }catch (Exception $e){
                $this->error(L('ADD_FAIL'));
            }
            $this->success(L('ADD_SUCCESS'),U('PackageType/index'));
        }
        $this->display();
    }
    /*
     * edit
     * */
    public function edit(){
        if(IS_POST){
            $data = $_POST;
            writeLog('gm_code_edit');
            $id  = $data['id'];
            unset($data['id']);
            if(empty($data['title'])) $this->error(L('TITLE_NAME_CANNOT_BE_EMPTY'));
            if(empty($data['code_pre'])) $this->error(L('ACTIVITY_CODE_PREFIX_NOT_EMPTY'));
            if(empty($data['code_num'])) $this->error(L('ACTIVITY_PACKS_CODE_NUMBER_NOT_EMPTY'));
            if(empty($data['content'])) $this->error(L('ACTIVITY_PACKS_USE_CANNOT_EMPTY'));
            if(preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$data['code_pre'])) $this->error(L('CARD_NUMBER_PREFIX'));
            if(strlen($data['code_pre']) !=2){
                $this->error(L('PACKAGE_CODE_PREFIX'));
            }

            // 判断是否存在奖品
            $temp = array();
            if (!empty($data['reward_id'])) {
                foreach ($data['reward_id'] as $k => $v) {
                    if (isset($temp[$v])) { // 若已经存在 则进行累加
                        $temp[$v]['num'] += $data['reward_num'][$k];
                    } else {
                        $temp[$v] = array(
                            'itemid' => $data['reward_id'][$k],
                            'num' => (int) $data['reward_num'][$k]
                        );
                    }
                }
            }
            $temp = array_values($temp);
            unset($data['reward_id'],$data['reward_num']);
            $data['itemids'] = json_encode($temp);
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);

            try{
                $this->package_type->where("id=".$id)->save($data);
            }catch (Exception $e){
                $this->error(L('CHANGE_SUCCESS'));
            }
            $this->success(L('UPDATE_FAILED'),U('PackageType/index'));

        }else{
            $id = I('id');
            $info = $this->package_type->where("id=".$id)->find();
            $info['itemids'] = json_decode($info['itemids'],true);
            $this->assign('posts',$info);
            $this->assign('item_info',getAllItem());
            $this->display();
        }

    }
  /*
    * 分发至服务器
    * ***/
   public function distribution_server(){
        if(IS_POST){
            writeLog('distribution_server');
            $id          = $_POST['id'];
            if(empty($id)){
                redirectJs(L('PARAMETER_ERROR'),U('PackageType/index'));
                exit;
            }
            $result = $this->package_type->where("id=".$id)->find();
            $giftinfo = json_decode($result['itemids'],true);

            $gift = '';
            foreach($giftinfo as $item){
                $gift .= implode("|",$item).',';
            }
            $gift = rtrim($gift,",");
            $number  = intval($result['code_num']); //生数量
            $prefix  = $result['code_pre'];
            $newArr = array();

            try{
                    $serverGift=M('server_giftcode',null,DB_CONFIG_PLATFORM);
                    $serverGift->startTrans();
                    $this->gm_card_info->startTrans();
                    $this->package_type->startTrans();
                    for ($i = 0 ; $i < $number ; $i++ )
                    {
                        $cardid	= $prefix.$this->randString(10);
                        $newArr	=	array(
                            'type'=>$result['id'],
                            'code'	=> $cardid,
                            'gift'=> $gift,
                            'startts'       => $result['start_time'],
                            'endts'       => $result['end_time'],
                        );
                        $data_array[] = "('$result[id]','$cardid','$gift','$result[start_time]','$result[end_time]')";

						$data_list_array[] = "('$cardid','$id')";

                        if(($i+1) % PAGE_SIZE==0 || $number == ($i+1)){
                            $data_values = implode(",",$data_array);
                            $data_list = implode(",",$data_list_array);
                            $serverGift->execute("insert into server_giftcode (type,code,gift,startts,endts) values $data_values");
                            $this->gm_card_info->execute("insert into gm_card_info (cdkey,type_id) values $data_list");
                            $data_array=null;
                            $data_list_array=null;
                        }
                    }
                $this->package_type->execute("update gm_code_setting set status=1 WHERE id = $id");
                $serverGift->commit();
                $this->gm_card_info->commit();
                $this->package_type->commit();
            }catch (\Exception $e){
                $serverGift->rollback();
                $this->gm_card_info->rollback();
                $this->package_type->rollback();
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode(L('FAIL_IN_SEND')));
            }
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode(L('SEND_SUCCESS')));
        }else{
            $newArr = gm_server();
            $this->assign('server',$newArr);
            $this->assign('ids',$_GET['id']);
            $this->display();
        }


    }

    /**
     * 获取随机字符串
     */
    private function randString($length) {
        $mt_string = 'P5kQjR6ShTgU7fVeK3pLM4nNmW8dXcY9bZaAzByCxDwEvFuGtHs2rJq';
        $str = '';
        while ($length--) {
            $str .= $mt_string[mt_rand(0, 54)];
        }
        return $str;
    }
    /**
     *
     * 导出Excel
     */
    public function export_excel(){
        $id = I('id');
        if(empty($id)){
            $this->error(L('PARAMETER_ERROR'));
        }
        $info = $this->gm_card_info->where("type_id=".$id)->select();
        if(empty($info)){
            $this->error(L('NO_DATA_DERIVED_FROM_EXCEL'));
        }
        $str = L('PACKAGE_CODE_SERVER');
        $str = iconv('utf-8','gb2312',$str);
        foreach($info as &$item){
            $item['server_name'] = L('ALL_SERVER');
			$cdkey = iconv('utf-8','gb2312',$item['cdkey']); //中文转码
            $server_name = iconv('utf-8','gb2312',$item['server_name']);
            $str .= $cdkey.",".$server_name."\n"; //用引文逗号分开
        }
        $filename = date('Ymd').'.csv'; //设置文件名
        $this->export_csv($filename,$str); //导出
        exit;


    }
    private function export_csv($filename,$data) {
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $data;
    }
    public function expUser($cardInfo=array()){//导出Excel
        $xlsName  = L('THE_GAME_PACKAGE_CODE');
        $xlsData = $cardInfo;

        $xlsCell  = array(
            array('cdkey',L('PACKAGE_CODE')),
            array('server_name',L('SERVER_NAME'))
         );
       $this->exportExcel($xlsName,$xlsCell,$xlsData);


    }
    private function exportExcel($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gbk', $expTitle);//文件名称
        $fileName = $expTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");

        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
            }
        }

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $result = $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }


}
