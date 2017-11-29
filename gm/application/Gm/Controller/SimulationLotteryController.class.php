<?php
/**
 * Created by PhpStorm.
 * User: denglixing
 * Date: 2015/12/23
 * Time: 19:12
 */

namespace Gm\Controller;
use Common\Controller\AdminbaseController;

class SimulationLotteryController extends AdminbaseController{
    protected $serverlist_model;
    function _initialize(){
        parent::_initialize();
        $this->serverlist_model = M('server_list',null,DB_CONFIG_PLATFORM);

    }
    public function index(){
        $this->display('index');
    }
    public function sim(){
        writeLog('sim');
        $where = array();
        $serverid = $_GET['id'];
        if(!empty($serverid)){
            $where['id'] = $serverid;
        }
        $type = $_GET['type'];
        $frequency = $_GET['frequency'];
        $cardbase = $_GET['cardbase'];

        try{
            if(!empty($serverid) && !empty($frequency)){
                $server_list =$this->serverlist_model->where($where)->field('ip')->find();
                $new= implode('',$server_list);
                if(strstr($new,"json-gateway.php")){
                    $url = $new."/platform/drawpartner.php?drawid=$type&time=$frequency";
                    $newurl = str_replace("json-gateway.php/","","$url");
                    if(!empty($cardbase)){
                        $newurl = $newurl."&gid=$cardbase";
                    }
                }else{
                    $newurl = $new."platform/drawpartner.php?drawid=$type&time=$frequency";
                    if(!empty($cardbase)){
                        $newurl = $newurl."&gid=$cardbase";
                    }
                }

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $newurl);

                // 设置header
                curl_setopt($curl, CURLOPT_HEADER, 0);

                // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                // 运行cURL，请求网页
                $data = curl_exec($curl);

                // 关闭URL请求
                curl_close($curl);

                // 显示获得的数据
                $res = json_decode($data,true);
                if($res[0] ==0){
                    $this->error(L('SIMULATION_FAILURE'));
                }


            }
        }catch(Exception $e){
            $this->error(L('PARAMETER_ERROR'));
        }
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($res));
    }

}