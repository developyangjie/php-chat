<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose
 */
use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Store;

class Events
{
    public static $time_step_1 = 0;
    public static $time_step_2 = 0;

    public static $resTrie;

    const USER_TYPE_GM = 'gm';
    const USER_TYPE_PLAYER = 'player';
    const USER_TYPE_ROBOT = 'robot';
    const USER_TYPE_SYSTEM = 'system';

    const CHANNEL_TYPE_WORLD = 'w';
    const CHANNEL_TYPE_CLUB = 'c';
    const CHANNEL_TYPE_MARQUEE = 'm';
    const CHANNEL_TYPE_USER = 'u';

    const MESSAGE_SAVE_LENGTH_WORLD = 100;
    const MESSAGE_SAVE_LENGTH_CLUB = 100;
    const MESSAGE_SAVE_LENGTH_USER = 100;

    const USER_NAME_SYSTEM = '系统';
    const USER_NAME_GM = 'GM';
    const MESSAGE_TIME = 5;      //聊天消息时间限制 暂时只有世界聊天有
    const TIMEOUT_PROMPT = '勇者大人，5秒内只能发送一次对话哦';

    public static function onWorkerStart($bussinessWorder) {
        echo "WorkerStart\n";
        self::$resTrie = trie_filter_load(__dir__.'/blackword.tree');
        echo "BlackWord Inited\n";
    }

    public static function onWorkerStop($businessWorker)
    {
        echo "BlackWrod Free\n";
        trie_filter_free(self::$resTrie);
        echo "WorkerStop\n";
    }

    public static function word_filter($strContent) {
        $arrRet = trie_filter_search_all(self::$resTrie,$strContent);
        $dirtyWords = array();
        foreach($arrRet as $v) {
            $dirtyWords[] = substr($strContent,$v[0],$v[1]);
        }
        $BLACK_WORD_REPLACE = array('(╬▼皿▼）',
            '\("▔□▔)/',
            '↖(￣▽￣")',
            '(づ￣3￣)づ',
            '(*￣▽￣)y',
            'o(≧口≦)o',
            '(￣ε￣*)/',
            '(ㄒoㄒ)/~~',
            '(￣ε￣* )',
            '<(￣︶￣)>',
            'o(≧v≦)o',
            '╮(╯3╰)╭',
            '罒ω罒',
            '(◕ω＜)☆',
            'Σ(っ °Д °;)っ ',
            'ㄟ( ▔, ▔ )ㄏ  ',
            '╮(╯▽╰)╭',
            'o(*￣▽￣*)ブ',
            '(＠_＠;)?',
            '(o゜▽゜)o ',
            '☆"o((>ω< ))o"',
            '0v0',
            '(<ゝω·)☆',
            'w(ﾟДﾟ)w',
            '(ノへ￣、)',
            '(￣_,￣ )',
            '(๑•̀ㅂ•́)و✧',
            'Σ( ° △ °|||)︴',
            '(～￣(OO)￣)ブ',
            '凸(艹皿艹 )',
            '(* ￣3)(ε￣ *)',
            '(*￣rǒ￣)',
            'φ(≧ω≦*)♪',
            '┗|｀O′|┛ 嗷~~',
            '♪(^∇^*)',
            '╰(*°▽°*)╯',
            'o(*≧▽≦)ツ┏━┓',
            '（○｀ 3′○）',
            'o(*^＠^*)o',
            '(#`O′)',
            '(°ー°〃)',
            '○|￣|_ =3',
            'o(￣ヘ￣o＃)',
            '（＝。＝）',
            '~~( ﹁ ﹁ ) ~~~',
            '(ーー゛)',
            '(ー`´ー)',
            '(#`O′)',
            'o(一︿一+)o',
            'o(≧口≦)o',
            'ㄟ( ▔, ▔ )ㄏ',
            '(o_ _)ﾉ',
            '(⊙﹏⊙)',
            '(ˉ▽￣～) 切~~',
            '（＊￣（エ）￣）',
            '┑(￣Д ￣)┍',
            '(＠_＠;)',
            '━┳━　━┳━',
            '(☆´益`)c',
            '（´Д`）',
            '┗( T﹏T )┛',
            '(。﹏。*)',
            'o( =•ω•= )m',
            '≡ω≡',
            '(*￣(エ)￣)',
            '(✿◡‿◡)',
            '(*/ω＼*)',
            '┭┮﹏┭┮',
            'ヾ(￣▽￣)Bye~Bye~',
            '( ﹁ ﹁ ) ~→',
            'Ψ(￣∀￣)Ψ',
            '✧(≖ ◡ ≖✿)',
            '━━(￣ー￣*|||━━',
            'ヽ(*。>Д<)o゜',
            '┌(。Д。)┐',
            '○|￣|_',
            'o(￣▽￣)ｄ',
            '(；′⌒`)',
            'X﹏X',
            '*<|:-) ',
            '(๑•̀ㅂ•́)و✧',
            'ヾ(≧▽≦*)o',
            'o(*≧▽≦)ツ',
            '(o゜▽゜)o☆[BINGO!]',
            '～(￣▽￣～)(～￣▽￣)～',
            '<(￣︶￣)>',
            '嗯~ o(*￣▽￣*)o',
            '︿(￣︶￣)︿',
            'φ(゜▽゜*)♪',
            '╰(￣▽￣)╭',
            'o(￣▽￣)ｄ',
            '(｡･∀･)ﾉﾞ',
            'ヾ(≧∇≦*)ゝ',
            '（゜▽＾*））',
            '(*^▽^*)',
            'ヽ(✿ﾟ▽ﾟ)ノ',
            '(( へ(へ´∀`)へ',
            '^O^',
            '╰(*°▽°*)╯',
            '(*^▽^*)',
            '(๑´ㅂ`๑)',
            '(≧∀≦)ゞ',
            '(๑¯∀¯๑)',
            'o(*￣︶￣*)o',
            '<(*￣▽￣*)/',
            'ε(*´･∀･｀)зﾞ',
            '(^&^)/',
            '(～￣▽￣)～',
            '︿(￣︶￣)︿',
            '梗',
            'GG',
            '我已经是条咸鱼了',
            '你这样是活不过3集的',
            'Niceboat',
            '-1s',
            '醒醒该去打猎了',
            '无限大的梦想之后是什么也没有的世界',
            '快来搞死那激萌的萝莉',
            '非战斗人员请迅速撤离',
            '区区敏感词',
            '都是时辰的错',
            'asdfghjkl;’',
            '已经没什么好怕的了',
            '抽完这一发我就金盆洗手了',
            '玄不救非 ',
            '氪不改命',
            '警察叔叔，就是这个人',
            '我不做人啦！',
            'Giligili eye (o゜▽゜)o☆☆',
            '(<ゝω·) ~ kira☆',
            '你們……有沒有……',
            '我敬你是条汉子',
            '爱你哦',
            '学医救不了中国人',
            'MDZZ',
            '有基佬脱我裤链',
            '在这停顿',
            '50已到',
            '玩游戏就是要赢',
            '欧皇请收下我的膝盖',
            '冻住不洗澡',
            '天降。。。啊。。。',
            '你介意我和你一同探究孟德尔定律吗',
            '平面几何与立体几何性质是有差异的',
            '我倾向于轴心一方的意大利',
            '滑铁卢一战拿破仑为什么不动用空军',
            '你知道列克星敦对于美国独立的意义吗',
            '经济学中对消费的分类，除了自给性消费还有什么',
            '你熟悉安培定则吗',
            '乙醇制乙烯时温度计的位置不能大意',
            '我听说秘鲁西海岸东南信风已经持续了三个月',
            '我知道如何正确熄灭酒精灯',
            '我们承诺不率先使用因果律武器',
            '海带缠潜艇',
            '雾霾防导弹',
            '那就神作了');
        return str_replace($dirtyWords,$BLACK_WORD_REPLACE[rand(0,count($BLACK_WORD_REPLACE)-1)],$strContent);
    }

    /**
     * 有消息时
     * @param int $client_id
     * @param string $message
     */
    public static function onMessage($client_id, $message)
    {
        // debug
//        echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id session:" . json_encode($_SESSION, JSON_UNESCAPED_UNICODE) . " onMessage:" . $message . "\n";
        // 客户端传递的是json数据
        if (!!trim($message)) {
            $message_data = json_decode($message, true);
            if (!is_array($message_data) || empty($message_data) || !isset($message_data['type'])) {
                return;
            }
            $message_type = $message_data['type'];
            // 根据类型执行不同的业务
            switch ($message_type) {
                // 客户端回应服务端的心跳
                case 'pong':
                    $sessionArr = Gateway::getSession($client_id);
                    if (!empty($sessionArr) && isset($sessionArr['uid']) && isset($sessionArr['sid']) && isset($sessionArr['utype'])) {
                        $utype = $sessionArr['utype'];
                        if ($utype != self::USER_TYPE_GM) {
                            self::set_user_online($sessionArr['sid'], $sessionArr['uid']);
                        }
                    }
                    break;
                case 'gmlogin':
                case 're_gmlogin':
                    // sid,uid,token
                    $required_params = array('sid', 'uid', 'token');
                    foreach ($required_params as $rp) {
                        if (!isset($message_data[$rp])) {
                            return;
                        }
                    }
                    $sid = $message_data['sid'];
                    $uid = $message_data['uid'];
                    $token = $message_data['token'];
                    $uname = isset($message_data['uname']) ? trim(strip_tags($message_data['uname'])) : '';

                    $suid = self::get_suid($sid, $uid);
                    $gm_config = self::get_gm_config($uid);
                    if ($gm_config) {
                        if ($gm_config['password'] == $token) {
                            // 把用户信息加入session中
                            $sessionArr = array(
                                'sid' => $sid,
                                'uid' => $uid,
                                'uname' => $uname != '' ? $uname : $gm_config['name'],
                                'uinfo' => $gm_config['uinfo'],
                                'utype' => self::USER_TYPE_GM
                            );
                            Gateway::setSession($client_id, $sessionArr);

                            $onlines = Gateway::getClientIdByUid($suid);
                            if (!empty($onlines)) {
                                foreach ($onlines as $online) {
                                    Gateway::closeClient($online);
                                }
                            }
                            Gateway::bindUid($client_id, $suid);
                            // 加入GM频道
                            Gateway::joinGroup($client_id, 'gm');
                            // 加入世界频道
                            $world_room_id = self::get_world_room_id($sid);
                            $big_world_room_id = self::get_big_world_room_id($sid);
                            Gateway::joinGroup($client_id, $world_room_id);
                            Gateway::joinGroup($client_id, $big_world_room_id);
                            // 获取全服在线用户
                            $clients_list = Gateway::getClientInfoByGroup($big_world_room_id);
                            $clients_name_list = array();
                            $banned_uids = self::get_banned_uids();
                            foreach ($clients_list as $tmp_client_id => $item) {
                                $tmp_info = array($item['sid'], $item['uid'], $item['uname'], $item['utype']);
                                if (in_array(self::get_suid($item['sid'], $item['uid']), $banned_uids)) {
                                    $tmp_info[] = 1;
                                } else {
                                    $tmp_info[] = 0;
                                }
                                $clients_name_list[] = $tmp_info;
                            }
                            // 发送在线用户列表
                            $new_message = array(
                                'type' => $message_type,
                                'time' => date('Y-m-d H:i:s'),
                                'client_list' => $clients_name_list,
                                'robot_status' => self::get_robot_status($sid)
                            );
                            Gateway::sendToCurrentClient(json_encode($new_message, JSON_UNESCAPED_UNICODE));
                        }
                    }
                    break;
                // 客户端登录 message格式: {type:login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室
                case 'login':
                case 're_login':
                    // 检测必要参数:sid,uid,token,uname,uinfo
                    $required_params = array('sid', 'uid', 'token', 'uname', 'uinfo');
                    foreach ($required_params as $rp) {
                        if (!isset($message_data[$rp])) {
                            return;
                        }
                    }
                    $sid = $message_data['sid'];
                    $uid = $message_data['uid'];
                    $token = $message_data['token'];
                    $uname = $message_data['uname'];
                    $uinfo = $message_data['uinfo'];

                    $suid = self::get_suid($sid, $uid);

                    // token验证
                    $save_token = self::get_user_key($sid, $uid);
//                echo 'token_send:' . $token . ',token_save:' . $save_token . "\n";
                    if (false !== $save_token && $save_token != $token) {
                        echo 'Auth fail.' . json_encode($_SERVER) . "\n";
                        return;
                    }
                    // 把用户信息加入session中
                    $sessionArr = array(
                        'sid' => $sid,
                        'uid' => $uid,
                        'uname' => $uname,
                        'uinfo' => $uinfo,
                        'utype' => self::USER_TYPE_PLAYER
                    );
                    Gateway::setSession($client_id, $sessionArr);
                    // 关闭已有的连接
                    $onlines = Gateway::getClientIdByUid($suid);
                    if (!empty($onlines)) {
                        foreach ($onlines as $online) {
                            Gateway::closeClient($online);
                        }
                    }
                    Gateway::bindUid($client_id, $suid);
                    // 存储到区服世界房间的客户端列表
                    $world_room_id = self::get_world_room_id($sid);
                    $big_world_room_id = self::get_big_world_room_id($sid);
                    Gateway::joinGroup($client_id, $world_room_id);
                    Gateway::joinGroup($client_id, $big_world_room_id);
                    // 存储到区服工会房间的客户端列表
                    $cid = isset($uinfo['ucid']) ? $uinfo['ucid'] : 0;
                    if ($cid) {
                        $club_room_id = self::get_club_room_id($sid, $cid);
                        Gateway::joinGroup($client_id, $club_room_id);
                    }
                    // 设置在线状态
                    self::set_user_online($sid, $uid);
                    // 获取离线好友消息情况
                    $friends_offline_message = self::get_private_chat_message($sid, $uid);
                    // 返回结果
                    $new_message = array(
                        'type' => $message_type,
                        'time' => date('Y-m-d H:i:s'),
                        'data' => $friends_offline_message
                    );
                    Gateway::sendToCurrentClient(json_encode($new_message, JSON_UNESCAPED_UNICODE));
                    break;
                // 客户端发言 message: {type:say, to_client_id:xx, content:xx}
                case 'say':
                    $sessionArr = Gateway::getSession($client_id);
                    if (empty($sessionArr) || !isset($sessionArr['uid']) || !isset($sessionArr['sid'])) {
                        echo 'Not login:' . $message . '->' . json_encode($_SERVER) . "\n";
                        Gateway::closeCurrentClient();
                        return;
                    }
                    // 获取会话信息
                    $sid = $sessionArr['sid'];
                    $uid = $sessionArr['uid'];
                    $uname = $sessionArr['uname'];
                    $uinfo = $sessionArr['uinfo'];
                    $utype = $sessionArr['utype'];

                    $suid = self::get_suid($sid, $uid);

                    // 检测参数:to_channel,to_uid,content
                    $required_params = array('to_channel', 'to_uid', 'content');
                    foreach ($required_params as $rp) {
                        if (!isset($message_data[$rp])) {
                            return;
                        }
                    }
                    // 目标频道
                    $to_channel = $message_data['to_channel'];
                    $to_uid = $message_data['to_uid'];
                    $content = trim(strip_tags($message_data['content']));
                    $content = self::word_filter($content);
                    if ($content == '') {
                        return;
                    }

                    // 判定目标频道
                    if (!in_array($to_channel, array(self::CHANNEL_TYPE_WORLD, self::CHANNEL_TYPE_CLUB, self::CHANNEL_TYPE_USER, self::CHANNEL_TYPE_MARQUEE))) {
                        return;
                    }

                    // 判定禁言状态
                    $isban = self::get_is_banned($suid);
                    if ($isban) {
                        $gm_config = self::get_gm_config('gm');
                        $new_message = array(
                            'type' => 'say',
                            'from_sid' => $sid,
                            'from_sname' => self::get_server_name($sid),
                            'from_uid' => 0,
                            'from_uname' => self::USER_NAME_GM,
                            'from_utype' => self::USER_TYPE_GM,
                            'from_uinfo' => $gm_config['uinfo'],
                            'to_channel' => $to_channel,
                            'to_uid' => $uid,
                            'content' => '채팅을 이용하실 수 없습니다. 고객센터에 문의해주세요!',
                            'time' => date('Y-m-d H:i:s'),
                            'from_picindex' => isset($message_data['picindex'])?$message_data['picindex']:12001
                        );
                        Gateway::sendToCurrentClient(json_encode($new_message, JSON_UNESCAPED_UNICODE));
                    } else {
                        //如果世界消息 判断时间 10秒一次
                        if($to_channel == self::CHANNEL_TYPE_WORLD && self::checkMessageTime('w', $sid, $uid)) {
                            $gm_config = self::get_gm_config('gm');
                            $new_message = array(
                                'type' => 'say',
                                'from_sid' => $sid,
                                'from_sname' => self::get_server_name($sid),
                                'from_uid' => 0,
                                'from_uname' => self::USER_NAME_GM,
                                'from_utype' => self::USER_TYPE_GM,
                                'from_uinfo' => $gm_config['uinfo'],
                                'to_channel' => $to_channel,
                                'to_uid' => $uid,
                                'content' => self::TIMEOUT_PROMPT,
                                'time' => date('Y-m-d H:i:s'),
                                'from_picindex' => isset($message_data['picindex'])?$message_data['picindex']:12001
                            );
                            Gateway::sendToCurrentClient(json_encode($new_message, JSON_UNESCAPED_UNICODE));
                            return;
                        }
                        // 世界和工会
                        $new_message = array(
                            'type' => 'say',
                            'from_sid' => $sid,
                            'from_sname' => self::get_server_name($sid),
                            'from_uid' => $uid,
                            'from_uname' => $uname,
                            'from_utype' => $utype,
                            'from_uinfo' => $uinfo,
                            'to_channel' => $to_channel,
                            'to_uid' => $to_uid,
                            'content' => $content,
                            'time' => date('Y-m-d H:i:s'),
                            'from_picindex' => isset($message_data['picindex'])?$message_data['picindex']:12001
                        );
                        $cid = isset($uinfo['ucid']) ? $uinfo['ucid'] : 0;

                        if ($to_channel == self::CHANNEL_TYPE_USER) {
                            if ($to_uid > 0) {
                                $suid = self::get_suid($sid, $to_uid);
                                $isOnline = Gateway::isUidOnline($suid);
                                if ($isOnline) {
                                    Gateway::sendToUid($suid, json_encode($new_message, JSON_UNESCAPED_UNICODE));
                                    // 在线消息保存
                                    self::save_message($sid, $new_message, 0);
                                } else {
                                    // 离线消息
                                    self::save_message($sid, $new_message, 1);
                                }
                                Gateway::sendToCurrentClient(json_encode($new_message, JSON_UNESCAPED_UNICODE));
                            }
                        } else {
                            $room_id = '';
                            if ($to_channel == self::CHANNEL_TYPE_WORLD || $to_channel == self::CHANNEL_TYPE_MARQUEE) {
                                $room_id = self::get_big_world_room_id($sid);
                            } elseif ($to_channel == self::CHANNEL_TYPE_CLUB) {
                                // 检测公会ID一致性
                                if ($cid > 0 && $to_uid > 0 && $to_uid == $cid) {
                                    $room_id = self::get_club_room_id($sid, $cid);
                                }
                            }
                            if ($room_id) {
                                Gateway::sendToGroup($room_id, json_encode($new_message, JSON_UNESCAPED_UNICODE));
                                // 保存消息
                                self::save_message($sid, $new_message);
                            }
                        }
                    }
                    break;
                case 'getofflinemessage':
                    // 非法请求
                    $sessionArr = Gateway::getSession($client_id);
                    if (empty($sessionArr) || !isset($sessionArr['uid']) || !isset($sessionArr['sid'])) {
                        echo 'Not login:' . $message . '->' . json_encode($_SERVER) . "\n";
                        Gateway::closeCurrentClient();
                        return;
                    }

                    $sid = $sessionArr['sid'];
                    $uid = $sessionArr['uid'];

                    // 检测参数:to_channel,to_uid,page,pagesize
                    // 检测参数
                    $required_params = array('to_channel', 'to_uid', 'page', 'pagesize');
                    foreach ($required_params as $rp) {
                        if (!isset($message_data[$rp])) {
                            return;
                        }
                    }
                    $to_channel = $message_data['to_channel'];
                    $to_uid = $message_data['to_uid'];
                    $page = $message_data['page'];
                    $pagesize = $message_data['pagesize'];

                    // 检测公会ID一致性
                    if ($to_channel == self::CHANNEL_TYPE_CLUB) {
                        $uinfo = $sessionArr['uinfo'];
                        $cid = isset($uinfo['ucid']) ? $uinfo['ucid'] : 0;
                        if ($to_uid < 0 || $to_uid != $cid) {
                            return;
                        }
                    }
                    $data = self::get_message($sid, $uid, $to_channel, $to_uid, $page, $pagesize);
                    $res = array();
                    if (!empty($data)) {
                        foreach ($data as $msg) {
                            $res[] = json_decode($msg, true);
                        }
                    }
                    $new_message = array(
                        'type' => $message_type,
                        'data' => $res
                    );
                    Gateway::sendToCurrentClient(json_encode($new_message, JSON_UNESCAPED_UNICODE));
                    break;
                case 'setuinfo':
                    // 非法请求
                    $sessionArr = Gateway::getSession($client_id);
                    if (empty($sessionArr) || !isset($sessionArr['uid'])) {
                        echo 'Not login:' . $message . '->' . json_encode($_SERVER) . "\n";
                        Gateway::closeCurrentClient();
                        return;
                    }
                    // 检测必要参数
                    $required_params = array('uinfo');
                    foreach ($required_params as $rp) {
                        if (!isset($message_data[$rp])) {
                            return;
                        }
                    }
                    // 获取参数
                    $uinfo = $message_data['uinfo'];

                    // 获取会话信息
                    $sid = $sessionArr['sid'];
                    $uid = $sessionArr['uid'];
                    $uname = $sessionArr['uname'];
                    $uinfoOld = $sessionArr['uinfo'];
                    if (isset($uinfoOld['ucid']) && isset($uinfo['ucid'])) {
                        $cid = $uinfoOld['ucid'];
                        // 判定公会ID是否有变化
                        if ($cid != $uinfo['ucid']) {
                            if ($cid) {
                                $room_id = self::get_club_room_id($sid, $cid);
                                Gateway::leaveGroup($client_id, $room_id);
                            }
                            $cid = $uinfo['ucid'];
                            if ($cid) {
                                $room_id = self::get_club_room_id($sid, $cid);
                                Gateway::joinGroup($client_id, $room_id);
                            }
                        }
                    }
                    // 替换现有信息
                    Gateway::updateSession($client_id, array('uinfo' => $uinfo));
                    break;
                case 'getlist':
                    // 非法请求
                    $sessionArr = Gateway::getSession($client_id);
                    if (empty($sessionArr) || !isset($sessionArr['uid']) || !isset($sessionArr['sid']) || !isset($sessionArr['utype'])) {
                        echo 'Not login:' . $message . '->' . json_encode($_SERVER) . "\n";
                        Gateway::closeCurrentClient();
                        return;
                    }
                    $uid = $sessionArr['uid'];
                    $utype = $sessionArr['utype'];

                    if ($utype == self::USER_TYPE_GM) {
                        $sid = $sessionArr['sid'];
                        // 获取全服在线用户
                        $clients_list = Gateway::getClientInfoByGroup(self::get_big_world_room_id($sid));
                        $clients_name_list = array();
                        $banned_uids = self::get_banned_uids();
                        foreach ($clients_list as $tmp_client_id => $item) {
                            $tmp_info = array($item['sid'], $item['uid'], $item['uname'], $item['utype']);
                            if (in_array(self::get_suid($item['sid'], $item['uid']), $banned_uids)) {
                                $tmp_info[] = 1;
                            } else {
                                $tmp_info[] = 0;
                            }
                            $clients_name_list[] = $tmp_info;
                        }
                        $new_message = array(
                            'type' => $message_type,
                            'client_list' => $clients_name_list
                        );
                        Gateway::sendToCurrentClient(json_encode($new_message, JSON_UNESCAPED_UNICODE));
                    }
                    break;
                case 'getbannedlist':
                    // 非法请求
                    $sessionArr = Gateway::getSession($client_id);
                    if (empty($sessionArr) || !isset($sessionArr['uid']) || !isset($sessionArr['utype'])) {
                        echo 'Not login:' . $message . '->' . json_encode($_SERVER) . "\n";
                        Gateway::closeCurrentClient();
                        return;
                    }
                    $uid = $sessionArr['uid'];
                    $utype = $sessionArr['utype'];

                    if ($utype == self::USER_TYPE_GM) {
                        $banned_uids = self::get_banned_uids();
                        $new_message = array(
                            'type' => $message_type,
                            'banned_list' => $banned_uids
                        );
                        Gateway::sendToCurrentClient(json_encode($new_message, JSON_UNESCAPED_UNICODE));
                    }
                    break;
                case 'setrobot':
                    // 非法请求
                    $sessionArr = Gateway::getSession($client_id);
                    if (empty($sessionArr) || !isset($sessionArr['uid']) || !isset($sessionArr['sid']) || !isset($sessionArr['utype'])) {
                        echo 'Not login:' . $message . '->' . json_encode($_SERVER) . "\n";
                        Gateway::closeCurrentClient();
                        return;
                    }
                    $utype = $sessionArr['utype'];
                    if ($utype == self::USER_TYPE_GM) {
                        // 检测必要参数:status
                        // 检测必要参数
                        $required_params = array('status');
                        foreach ($required_params as $rp) {
                            if (!isset($message_data[$rp])) {
                                return;
                            }
                        }
                        $status = intval($message_data['status']);

                        $sid = $sessionArr['sid'];

                        self::set_robot_status($sid, $status);
                        $new_message = array(
                            'type' => $message_type,
                            'robot_status' => self::get_robot_status($sid)
                        );
                        Gateway::sendToCurrentClient(json_encode($new_message, JSON_UNESCAPED_UNICODE));
                    }
                    break;
                case 'setbanned':
                    // 非法请求
                    $sessionArr = Gateway::getSession($client_id);
                    if (empty($sessionArr) || !isset($sessionArr['uid']) || !isset($sessionArr['utype'])) {
                        echo 'Not login:' . $message . '->' . json_encode($_SERVER) . "\n";
                        Gateway::closeCurrentClient();
                        return;
                    }
                    $utype = $sessionArr['utype'];
                    // 检测必要参数
                    $required_params = array('to_uid', 'banned');
                    foreach ($required_params as $rp) {
                        if (!isset($message_data[$rp])) {
                            return;
                        }
                    }
                    $banned = $message_data['banned'];
                    $to_uid = $message_data['to_uid'];

                    if ($utype == self::USER_TYPE_GM) {
                        self::set_banned($to_uid, $banned);
                        $new_message = array(
                            'type' => $message_type
                        );
                        Gateway::sendToCurrentClient(json_encode($new_message, JSON_UNESCAPED_UNICODE));
                    }
                    break;
            }
        }
    }

    /**
     * 当客户端断开连接时
     * @param integer $client_id 客户端id
     */
    public static function onClose($client_id)
    {
//        echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";
        $sessionArr = Gateway::getSession($client_id);
        if (!empty($sessionArr) && isset($sessionArr['uid']) && isset($sessionArr['sid'])) {
            $sid = $sessionArr['sid'];
            $uid = $sessionArr['uid'];
            // 设置在线状态
            self::del_user_online($sid, $uid);
        }
    }

    public static function get_suid($sid, $uid)
    {
        return $sid . '_' . $uid;
    }

    public static function get_sid_uid_from_suid($suid)
    {
        return explode('_', $suid);
    }

    public static function get_big_world_room_id($sid)
    {
        $key = 'w_' . self::get_big_world_id($sid);
        return $key;
    }

    public static function get_big_world_id($sid)
    {
        $big_sid = 0;
        if (intval($sid) > 1000) {
            $big_sid = intval(intval(($sid) / 1000));
        }
        return $big_sid;
    }

    public static function get_world_room_id($sid)
    {
        return 'w_' . $sid;
    }

    public static function get_club_room_id($sid, $cid)
    {
        return 'c_' . $sid . '_' . $cid;
    }

    public static function get_user_online_key($sid, $uid)
    {
        return 'game_online:' . $sid . ':' . $uid;
    }

    public static function get_robot_status_key($sid)
    {
        $key = 'robot_status:' . self::get_big_world_id($sid);
        return $key;
    }

    public static function set_user_online($sid, $uid)
    {
        $key = self::get_user_online_key($sid, $uid);
        $store = Store::instance('user');
        $res = $store->setEx($key, 30, 1);
        return $res;
    }

    public static function del_user_online($sid, $uid)
    {
        $key = self::get_user_online_key($sid, $uid);
        $store = Store::instance('user');
        $res = $store->delete($key);
        return $res;
    }

    public static function get_robot_status($sid)
    {
        $store = Store::instance('user');
        $key = self::get_robot_status_key($sid);
        $status = $store->get($key);
        return $status;
    }

    public static function get_robot_status_by_bigsid($big_sid) {
        $store = Store::instance('user');
        $status = $store->get('robot_status:'.$big_sid);
        return $status;
    }

    public static function set_robot_status($sid, $status)
    {
        $store = Store::instance('user');
        $key = self::get_robot_status_key($sid);
        return $store->set($key, $status);
    }

    /**
     * 获取GM配置
     * @param $id
     * @return bool
     */
    public static function get_gm_config($id)
    {
        if (isset(\Config\GM::$$id)) {
            return \Config\GM::$$id;
        }
        return false;
    }

    /**
     * 获取服务器名称
     * @param $sid
     * @return bool
     */
    public static function get_server_name($sid)
    {
        $server_list = \Config\Db::$servers;
        $sname = '未知';
        if(is_array($server_list)) {
            foreach ($server_list as $sinfo) {
                if($sinfo['sid'] == $sid) {
                    $sname = $sinfo['sname'];
                    break;
                }
            }
        }
        return $sname;
    }

    /*
     * 获取用户登录验证Key
     */
    public static function get_user_key($sid, $uid)
    {
        $key = 'user_key:' . $sid . ':' . $uid;
        $store = Store::instance('user');
        $uidkey = $store->get($key);
        return $uidkey;
    }

    public static function get_banned_uids()
    {
        $bannuids = array();
        try {
            $store = Store::instance('user');
            $bannuids = $store->sMembers('chat_user_banned');
        } catch (Exception $e) {

        }
        return $bannuids;
    }

    public static function get_is_banned($suid)
    {
        $banned = false;
        try {
            $store = Store::instance('user');
            $banned = $store->sIsMember('chat_user_banned', $suid);
        } catch (Exception $e) {

        }
        return $banned;
    }

    public static function set_banned($suid, $flag = 1)
    {
        try {
            $store = Store::instance('user');
            if ($flag) {
                $store->sAdd('chat_user_banned', $suid);
            } else {
                $store->sRem('chat_user_banned', $suid);
            }
        } catch (Exception $e) {

        }
        return true;
    }

    public static function save_message($sid, $message, $offline = 0)
    {
        /**
         * $new_message = array(
         * 'type' => 'say',
         * 'from_uid' => $uid,
         * 'from_uname' => $uname,
         * 'from_utype' => $utype,
         * 'from_uinfo' => $uinfo,
         * 'to_channel' => $to_channel,
         * 'to_uid' => $to_uid,
         * 'content' => $message_data['content'],
         * 'time' => date('Y-m-d H:i:s')
         * );
         */
        try {
            $message['from_sid'] = $sid;
            $store = Store::instance('user');
            $to_channel = $message['to_channel'];
            // 保存世界频道/公会频道消息
            if ($to_channel == self::CHANNEL_TYPE_WORLD || $to_channel == self::CHANNEL_TYPE_CLUB) {
                $key = '';
                if ($to_channel == self::CHANNEL_TYPE_WORLD) {
                    $key = 'chat_w:' . self::get_big_world_id($sid);
                    $store->set('message_'.$to_channel.'_'.$sid.'_'.$message['from_uid'], 1);
                    $store->expire('message_'.$to_channel.'_'.$sid.'_'.$message['from_uid'], self::MESSAGE_TIME);
                } elseif ($to_channel == self::CHANNEL_TYPE_CLUB) {
                    $key = 'chat_c:' . $sid . ':' . $message['to_uid'];
                }
                $store->lPush($key, json_encode($message, JSON_UNESCAPED_UNICODE));
            } // 保存私聊信息
            elseif ($to_channel == self::CHANNEL_TYPE_USER) {
                $from_uid = $message['from_uid'];
                $to_uid = $message['to_uid'];
                $key = 'chat_u:' . $sid . ':' . $to_uid . ':' . $from_uid;
                $store->lPush($key, json_encode($message, JSON_UNESCAPED_UNICODE));
                // 移除超出长度的消息
                if ($store->lSize($key) > self::MESSAGE_SAVE_LENGTH_USER) {
                    $store->lTrim($key, 0, self::MESSAGE_SAVE_LENGTH_USER - 1);
                }
                $key = 'chat_u:' . $sid . ':' . $to_uid;
                $store->hSet($key, $from_uid, $offline);
            }
        } catch (Exception $e) {

        }
    }

    public static function get_message($sid, $uid, $to_channel, $to_uid, $page, $pagesize)
    {
        $data = false;
        try {
            $store = Store::instance('user');
            $key = '';
            if ($to_channel == self::CHANNEL_TYPE_WORLD) {
                $key = 'chat_w:' . self::get_big_world_id($sid);
                if ($store->lSize($key) > self::MESSAGE_SAVE_LENGTH_WORLD) {
                    $store->lTrim($key, 0, self::MESSAGE_SAVE_LENGTH_WORLD - 1);
                }
            } elseif ($to_channel == self::CHANNEL_TYPE_CLUB) {
                $key = 'chat_c:' . $sid . ':' . $to_uid;
                if ($store->lSize($key) > self::MESSAGE_SAVE_LENGTH_CLUB) {
                    $store->lTrim($key, 0, self::MESSAGE_SAVE_LENGTH_CLUB - 1);
                }
            } elseif ($to_channel == self::CHANNEL_TYPE_USER) {
                $key = 'chat_u:' . $sid . ':' . $uid . ':' . $to_uid;
                $store->hSet('chat_u:' . $sid . ':' . $uid, $to_uid, 0);
            }
            $data = $store->lRange($key, ($page - 1) * $pagesize, $page * $pagesize - 1);
        } catch (Exception $e) {

        }
        return $data;
    }

    public static function get_private_chat_message($sid, $uid)
    {
        $data = false;
        try {
            $store = Store::instance('user');
            $key = 'chat_u:' . $sid . ':' . $uid;
            $data = $store->hGetAll($key);
        } catch (Exception $e) {

        }
        return $data;
    }

    public static function broadcast_to_room($sids)
    {
        echo '[定时任务]:broadcast_to_room:' . date('Y-m-d H:m:s', time()) . "\n";
        $store = Store::instance('user');

        foreach ($sids as $sid) {
            try {
                // 世界消息
                $key = 'chat_sys_w:' . $sid;
                if ($store->exists($key)) {
                    $msg = $store->rPop($key);
                    if (!!trim($msg)) {
                        $msg = trim(strip_tags($msg));
                        $room_id = self::get_world_room_id($sid);
                        $client_cnt = Gateway::getClientCountByGroup($room_id);
//                        echo '获取房间 '.$room_id.' 在线用户：'.$client_cnt."\n";
                        if ($client_cnt > 0) {
                            $new_message = array(
                                'type' => 'say',
                                'from_sid' => $sid,
                                'from_uid' => 0,
                                'from_uname' => self::USER_NAME_SYSTEM,
                                'from_utype' => self::USER_TYPE_SYSTEM,
                                'from_uinfo' => '',
                                'to_channel' => self::CHANNEL_TYPE_WORLD,
                                'to_uid' => 0,
                                'content' => $msg,
                                'time' => date('Y-m-d H:i:s')
                            );
                            Gateway::sendToGroup($room_id, json_encode($new_message, JSON_UNESCAPED_UNICODE));
                        }
                    }
                }
            } catch (Exception $e) {
                echo '[异常]:' . $e->getTraceAsString() . "\n";
            }
            try {
                // 公会消息
                $key = 'chat_sys_c:' . $sid;
                if ($store->exists($key)) {
                    $msg = $store->rPop($key);
                    if (!!trim($msg)) {
                        $msg = json_decode($msg, true);
                        if (!!trim($msg['msg'])) {
                            $content = trim(strip_tags($msg['msg']));
                            $room_id = self::get_club_room_id($sid, $msg['ucid']);
                            $client_cnt = Gateway::getClientCountByGroup($room_id);
//                        echo '获取房间 '.$room_id.' 在线用户：'.$client_cnt."\n";
                            if ($client_cnt > 0) {
                                $new_message = array(
                                    'type' => 'say',
                                    'from_sid' => $sid,
                                    'from_uid' => 0,
                                    'from_uname' => self::USER_NAME_SYSTEM,
                                    'from_utype' => self::USER_TYPE_SYSTEM,
                                    'from_uinfo' => '',
                                    'to_channel' => self::CHANNEL_TYPE_CLUB,
                                    'to_uid' => $msg['ucid'],
                                    'content' => $content,
                                    'time' => date('Y-m-d H:i:s')
                                );
                                Gateway::sendToGroup($room_id, json_encode($new_message, JSON_UNESCAPED_UNICODE));
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                echo '[异常]:' . $e->getTraceAsString() . "\n";
            }
        }
    }

    public static function robot_chat($sids)
    {
        echo '[定时任务]:robot_chat:' . date('Y-m-d H:m:s', time()) . "\n";
        $big_sids = array();
        foreach($sids as $sid) {
            $big_sid = self::get_big_world_id($sid);
            if(!in_array($big_sid,$big_sids)) {
                $big_sids[] = $big_sid;
            }
        }
        if (self::$time_step_1 >= PHP_INT_MAX - 100 || self::$time_step_2 >= PHP_INT_MAX - 100) {
            self::$time_step_1 = 0;
            self::$time_step_2 = 0;
        }
        self::$time_step_1 += 5;
        if (self::$time_step_1 >= self::$time_step_2) {
            self::$time_step_2 += rand(5, 60);
            $all_client_cnt = Gateway::getAllClientCount();
            if ($all_client_cnt > 0) {
                $msgs = \Config\Robot::$robots_loop;
                if(count($msgs) > 0) {
                    // todo:循环遍历服务器大区ID
                    foreach($big_sids as $big_sid) {
                        if(self::get_robot_status_by_bigsid($big_sid)) {
                            $msg = $msgs[array_rand($msgs)];
                            $new_message = array(
                                'type' => 'say',
                                'from_sid' => 0,
                                'from_uid' => 1,
                                'from_uname' => self::USER_NAME_SYSTEM,
                                'from_utype' => self::USER_TYPE_ROBOT,
                                'from_uinfo' => '',
                                'to_channel' => self::CHANNEL_TYPE_WORLD,
                                'to_uid' => 0,
                                'content' => $msg,
                                'time' => date('Y-m-d H:i:s')
                            );
                            Gateway::sendToGroup(self::get_world_room_id($big_sid),json_encode($new_message,JSON_UNESCAPED_UNICODE));
                        }
                    }
                }
            }
        }
    }
    //检测消息限制时间
    public static function checkMessageTime($type, $sid, $uid) {
        $store = Store::instance('user');
        if($store->exists('message_'.$type.'_'.$sid.'_'.$uid)) {
            return true;
        }
        return false;
    }
}
