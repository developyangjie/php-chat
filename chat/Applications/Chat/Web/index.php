<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>网页即时聊天系统</title>
<link href="/css/bootstrap.min.css" rel="stylesheet">
<link href="/css/style.css" rel="stylesheet">
<!-- Include these three JS files: -->
<script type="text/javascript" src="/js/swfobject.js"></script>
<script type="text/javascript" src="/js/web_socket.js"></script>
<script type="text/javascript" src="/js/json.js"></script>
<script type="text/javascript" src="/js/jquery.min.js"></script>

<script type="text/javascript">
if (typeof console == "undefined") {
    this.console = {
        log: function (msg) {
        }
    };
}
WEB_SOCKET_SWF_LOCATION = "/swf/WebSocketMain.swf";
WEB_SOCKET_DEBUG = true;
<?php
    require_once '../Config/Db.php';
    $sids = \Config\Db::$servers;
    $sid = isset($_GET['sid']) ? $_GET['sid'] : $sids[0]['sid'];
    foreach($sids as $sinfo) {
        if($sid == $sinfo['sid']) {
            $sname = $sinfo['sname'];
            break;
        }
    }

?>
var ws, sid = <?php echo $sid;?>, token, uid, uname,client_list = {}, timeid, reconnect = false, rec_cnt = 0,timeid2;
function init() {
    // 创建websocket
//            ws = new WebSocket("ws://"+document.domain+":7272");
    ws = new WebSocket("ws://14.63.174.162:8383");
    // 当socket连接打开时，输入用户名
    ws.onopen = function () {
        timeid && window.clearInterval(timeid);
        timeid2 && window.clearInterval(timeid2);
        if(!uid) {
            show_prompt_uid();
        }
        if (!token) {
            show_prompt_token();
        }
        show_prompt_uname();
        if (!uid || !token) {
            return ws.close();
        }
        var logind = {
            "sid": sid,
            "uid": uid,
            "token": token
        };
        if(uname) {
            logind["uname"] = uname;
        }
        if (reconnect == false) {
            logind["type"] = "gmlogin";
            var login_data = JSON.stringify(logind);
            // 登录
            console.log("websocket握手成功，发送登录数据:" + login_data);
            ws.send(login_data);
            reconnect = true;
        }
        else {
            logind["type"] = "re_gmlogin";
            // 断线重连
            var relogin_data = JSON.stringify(logind);
            console.log("websocket握手成功，发送重连数据:" + relogin_data);
            ws.send(relogin_data);
        }
    };
    // 当有消息时根据消息类型显示不同信息
    ws.onmessage = function (e) {
        console.log(e.data);
        var data = JSON.parse(e.data);
        switch (data['type']) {
            // 服务端ping客户端
            case 'ping':
                ws.send(JSON.stringify({"type": "pong"}));
                break;
            // 登录 更新用户列表
            case 'gmlogin':
            case 're_gmlogin':
                flush_client_list(data['client_list']);
                flush_robot_status(data['robot_status']);
                if(data['type'] == 're_gmlogin') {
                    console.log("重连成功");
                } else {
                    console.log("登录成功");
                }
                timeid2 = window.setInterval(get_list,10000);
                get_offline_message();
                get_banned_list();
                break;
            // 发言
            case 'say':
                say(data['from_sid'],data['from_uid'], data['from_uname'], data['from_utype'], data['content'], data['time']);
                break;
            case 'getlist':
                flush_client_list(data['client_list']);
                break;
            case 'setrobot':
                flush_robot_status(data['robot_status']);
                break;
            case 'setbanned':
                get_list();
                get_banned_list();
                break;
            case 'getofflinemessage':
                get_offline_message_handler(data['data']);
                break;
            case 'getbannedlist':
                get_banned_list_handler(data['banned_list']);
                break;
        }
    };
    ws.onclose = function () {
        console.log("连接关闭，定时重连");
        // 定时重连
        window.clearInterval(timeid);
        window.clearInterval(timeid2);
        rec_cnt ++;
        if(rec_cnt < 3) {
            timeid = window.setInterval(init, 3000);
        }
    };
    ws.onerror = function () {
        console.log("出现错误");
    };
}

function get_list() {
    ws.send(JSON.stringify({
        "type":"getlist"
    }));
}

function get_offline_message() {
    ws.send(JSON.stringify(
        {
            "type":"getofflinemessage",
            "to_channel":"w",
            "to_uid":0,
            "page":1,
            "pagesize":100
        }
    ));
}

function get_offline_message_handler(data) {
    var i;
    data.reverse();
    for(i in data) {
        say(data[i]['from_sid'],data[i]['from_uid'], data[i]['from_uname'], data[i]['from_utype'], data[i]['content'], data[i]['time']);
    }
}

function get_banned_list() {
    ws.send(JSON.stringify({
        "type":"getbannedlist"
    }));
}

// 输入姓名
function show_prompt_token() {
    token = prompt('输入你的密钥：', '');
    if (!token || token == 'null') {
        alert("输入密钥为空或者为'null'，请重新输入！");
        show_prompt_token();
    }
}

function show_prompt_uid() {
    uid = prompt('请输入你的用户名：','');
    if(!uid || uid == 'null') {
        alert("输入用户名为空或者为'null'，请重新输入！");
        show_prompt_uid();
    }
}

function show_prompt_uname() {
    uname = prompt('请输入你的昵称（GM无需填写）：','');
}

// 提交对话
function onSubmit() {
    var input = document.getElementById("textarea");
//    var to_channel = $("#client_list option:selected").attr("value");
//    var to_uid = $("#client_list option:selected").text();
    var to_uid = $("#client_list option:selected").attr("value");
    var to_channel;
    (to_uid == 0) ? to_channel='w':to_channel='u';
    ws.send(JSON.stringify({
        "type": "say",
        "to_channel": to_channel,
        "to_uid": to_uid,
        "content": input.value
    }));
    input.value = "";
    input.focus();
}

function onBanned() {
    var input = document.getElementById("txt_banned_uid");
    ws.send(JSON.stringify({
        "type": "setbanned",
        "banned": 1,
        "to_uid": input.value
    }));
    input.value = "";
}

// 刷新用户列表框
function flush_client_list(client_list) {
    var userlist_window = $("#userlist");
    var client_list_slelect = $("#client_list");
    userlist_window.empty();
    client_list_slelect.empty();
    userlist_window.append('<h4>在线用户' + client_list.length + '人</h4><ul>');
    client_list_slelect.append('<option value="0" id="cli_all">所有人</option>');
    for(var i = 0; i < client_list.length; i ++) {
        var client_info = client_list[i];
        var sid = client_info[0];
        var uid = client_info[1];
        var suid = sid + '_' + uid;
        var uname = client_info[2];
        var utype = client_info[3];
        var banned = client_info[4];
        if(utype == 'player') {
            if(banned == 1) {
                userlist_window.append('<li>'+uname + '<a class="btn btn-link" onclick="on_ban_user_click(' + 0 + ',' + sid + ',' + uid + ')">解除禁言</a></li>');
            } else {
                userlist_window.append('<li>'+uname + '<a class="btn btn-link" onclick="on_ban_user_click(' + 1 + ',' + sid + ',' + uid + ')"><font color="red">禁言</font></a></li>');
            }
            client_list_slelect.append('<option value="'+ suid + '" id=cli_"' + suid + '">' + uname + '</option>');
        }else {
            userlist_window.append('<li>'+uname + '</li>');
        }
    }
    $("#client_list").val(select_client_id);
    userlist_window.append('</ul>');
}

function get_banned_list_handler(data) {
    var div_bannedlist = $("#bannedlist");
    div_bannedlist.empty();
    div_bannedlist.append('<h4>封禁用户' + data.length + '人</h4><ul>');
    var i;
    for(var i in data) {
        var tmp = data[i].split(":");
        var sid = tmp[0];
        var uid = tmp[1];
        div_bannedlist.append('<li>' + data[i] + '<a class="btn btn-link" onclick="on_ban_user_click(' + 0 + ',' + sid + ',' + uid + ')">解除禁言</a></li>');
    }
    div_bannedlist.append('</ul>');
}

function on_ban_user_click(status,sid,uid) {
    ws.send(JSON.stringify({
        "type": "setbanned",
        "banned": status,
        "to_uid": sid + '_' + uid
    }));
}

function flush_robot_status(status) {
    if($("#div_robot_status").hasClass("hidden")) {
        $("#div_robot_status").removeClass("hidden");
    }
    if(status == 1) {
        $("#span_robot_status").removeClass("label label-warning");
        $("#span_robot_status").addClass("label label-success");
        $("#span_robot_status").html("聊天机器人：开启");
        $("#btn_open_robot").hide();
        $("#btn_close_robot").show();
    } else {
        $("#span_robot_status").removeClass("label label-success");
        $("#span_robot_status").addClass("label label-warning");
        $("#span_robot_status").html("聊天机器人：关闭");
        $("#btn_open_robot").show();
        $("#btn_close_robot").hide();
    }
}

function on_robot_statuc_click(status) {
    ws.send(JSON.stringify({
        "type": "setrobot",
        "status": status
    }));
}

// 发言
function say(from_sid, from_uid, from_uname, from_utype, content, time) {
    var utype = "";
    if(from_utype == "gm") {
        utype = "GM";
    } else if(from_utype == "player") {
        utype = "玩家";
    } else if(from_utype == "system") {
        utype = "系统";
    } else if(from_utype == "robot") {
        utype = "机器人";
    }
    if(utype != "") {
        from_uname += "(" + utype + ")";
    }
    if(from_utype == "player") {
    }
    if(from_utype == "system") {
        var textObjArr=JSON.parse(content);
        var htmls="";
        for(var i=0;i<textObjArr.length;i++){
            var textObj=textObjArr[i];
            var size=20,color="white";
            if(textObj[1]){
                size=textObj[1];
            }
            if(textObj[2]){
                color=textObj[2];
            }
            htmls+="<span style='color:"+color+";font-size:"+size+"px;'>"+textObj[0]+"</span>";
        }
        content = htmls;
    }
    $("#dialog").append('<div class="speech_item"><img src="/img/1.png" class="user_icon" /> ' + from_uname + '(' + from_sid + ':' + from_uid + ')' + ' <br> ' + time + '<div style="clear:both;"></div><p class="triangle-isosceles top">' + content + '</p> </div>');
}

$(function () {
    select_client_id = 0;
    $("#client_list").change(function () {
        select_client_id = $("#client_list option:selected").attr("value");
    });
});

</script>
</head>
<body onload="init()">
<div class="container">
    <div class="row clearfix">
        <div class="col-md-1 column">
        </div>
        <div class="col-md-8 column">
            <div class="thumbnail">
                <div class="caption" style="min-height: 200px;max-height: 600px;overflow:auto;" id="dialog"></div>
            </div>
            <form onsubmit="onSubmit(); return false;">
                <select style="margin-bottom:8px" id="client_list">

                </select>
                <textarea class="textarea thumbnail" id="textarea"></textarea>

                <div class="say-btn"><input type="submit" class="btn btn-default" value="发表"/></div>
            </form>
            <div>
                &nbsp;&nbsp;&nbsp;&nbsp;<b>服务器列表：</b>（当前服务器：<?php echo $sname;?>）<br>
                <?php
                foreach($sids as $sinfo) {
                    if($sinfo['sid'] != $sid) {
                        echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="/?sid='.$sinfo['sid'].'">'.$sinfo['sname'].'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                }
                ?>
                <br><br>
            </div>
        </div>
        <div class="col-md-3 column">
            <div class="thumbnail ">
                <div class="caption" id="userlist"></div>
            </div>
            <div class="thumbnail">
                <div id="div_robot_status" class="hidden">
                    <span id="span_robot_status"></span>
                    <button id="btn_open_robot" type="button" class="btn btn-link" onclick="on_robot_statuc_click(1)">开启机器人</button>
                    <button id="btn_close_robot" type="button" class="btn btn-link" onclick="on_robot_statuc_click(0)">关闭机器人</button>
                </div>
            </div>
            <div class="thumbnail">
                <div class="caption" id="bannuser">
                    <form onsubmit="onBanned();return false;">
                        <input type="text" name="to_uid" id="txt_banned_uid" placeholder="输入要禁言的用户sid_uid"/>
                        <input type="submit" class="btn btn-default btn-sm" value="禁言"/>
                    </form>
                </div>
                <div class="caption" id="bannedlist">

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>