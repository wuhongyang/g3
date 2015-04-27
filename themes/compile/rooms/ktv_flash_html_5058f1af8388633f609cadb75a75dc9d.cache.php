<?php if (!class_exists('template')) die('Access Denied');?>
<!DOCTYPE html>
<html>

<!--template compile at 2014-04-25 17:02:04-->

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $room_info['name'];?>|<?php echo $room_info['description'];?></title>
<style type="text/css">
html,body{width:100%;height:100%;margin:0;padding:0;}
body{overflow:hidden;text-align:center;background:#FFF}
object:focus{outline:none;}
#swfObjectBox{height:100%;}
</style>
<script type="text/javascript" src="<?php echo $g3_ktv;?>/swfobject.js"></script>
<script type="text/javascript" src="<?php echo STATIC_API_PATH;?>/frontend/base/jquery/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo STATIC_API_PATH;?>/static/js/quality.js"></script>
<script type="text/javascript">
var swfVersionStr = "11.0.0"; //最低Flash版本要求
var ajaxtime;
var onloadtime = (new Date()).getTime();
var client = /vclient/.test(window.location.href)? '_v' : '';
var client_arg = {};

SetDoMain();

SetBeforeUnLoad(UnloadCancel);
function openlogin(type){
	SetBeforeUnLoad(null);
    var redirect = window.location.href;
    var rep = "http://<?php echo $_SERVER['HTTP_HOST'];?>"
    redirect = redirect.replace(rep,'');
    var qqLoginWindow = window.open("http://<?php echo $callback;?>/passport/openlogin/"+type+"/login.php?back=<?php echo $_SERVER['HTTP_HOST'];?>&redirect="+redirect+"&domain="+document.domain,"OpenLogin","width=450,height=320,menubar=0,scrollbars=1, resizable=1,status=1,titlebar=0,toolbar=0,location=1");
	var timer = setInterval(function(){
		if(qqLoginWindow.closed){
			SetBeforeUnLoad(UnloadCancel);
			clearInterval(timer);
		}
	},1000);
}

$.ajax({
	url: '/rooms/ajax_info.php',
	type: 'POST',
	async:false,
	data: {Tag:"GetRoomLoginUser","GroupId":"<?php echo $room_info['group'];?>"},
	dataType:'JSON',
	success: function(data){
		ajaxtime = (new Date()).getTime() - onloadtime;
		var flashvars = {"roombg":"<?php echo cdn_url(PIC_API_PATH.'/roombg/'.$_GET['roomid'].'/0/0.jpg') ;?>","ui_path":"<?php echo $room_info['ui_path'];?>","bgalign":"<?php echo $room_info['bgalign'];?>","uin":data.Uin,"sessionkey":data.Token,"url":"ws://<?php echo $room_info['host'];?>:<?php echo $room_info['port'];?>/client","roomid":"<?php echo $_GET['roomid'];?>","Ip":"<?php echo $ip;?>","GroupId":"<?php echo $room_info['group'];?>","play_media":<?php echo $room_info['play_media_conf'];?>,"admin_media":<?php echo $room_info['admin_media_conf'];?>,"p2p_media":<?php echo $room_info['p2p_media_conf'];?>,"roomurl":"<?php echo cdn_url(PIC_API_PATH.'/roomid/'.$_GET['roomid'].'/0/0.jpg') ;?>","roomname":"<?php echo $room_info['name'];?>","tim":"<?php echo $tim;?>"};
		flashvars.start_skin = '<?php echo $room_info["start_skin"];?>';
		flashvars.room_skin = '<?php echo $room_info["room_skin"];?>';
		flashvars.layout_file = '<?php echo $room_info["layout_file"];?>';
		flashvars.core_path = '<?php echo $core_path;?>';
		//flashvars.ui_path += client;
		url = "<?php echo $g3_ktv;?>/warp_ktv.html?<?php echo $tim;?>";
		client_arg.flashvars = flashvars;
		client_arg.href = window.location.href;
		client_arg.host = "http://<?php echo $_SERVER['HTTP_HOST'];?>";
		client_arg.tim  ="<?php echo $tim;?>";
		client_arg.width = "<?php echo $room_info['width'];?>";
		client_arg.height = "<?php echo $room_info['height'];?>";
		client_arg.callback = "<?php echo $callback;?>";
		client_arg.client = (!!client);
		flashLoad();
	},
	error: function(){
		//alert('房间信息获取失败');
		//window.location.href="/";
	}
});

function SetDoMain(){
	var str =  window.location.host;
	var tmp = str.substring(0,str.lastIndexOf('.'));
	var index = tmp.lastIndexOf('.');
	var domain = str.substr(index+1);
	document.domain = domain;
}

function flashLoad(){
	var obj = document.getElementById("ktv_frame");
	if(!obj)return;
	if(typeof(url) != 'undefined'){
		obj.src = encodeURI(url);
		window.onresize=SetFrameSize;
		obj.onload = SetFrameSize;
	}
}

function SetFrameSize(){
	var h = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
	var w = window.innerWidth || document.documentElement.clientWidth  || document.body.clientWidth ;
	var iframe = document.getElementById('ktv_frame');
	iframe.height = h+"px";
	iframe.width = w+"px";
}

var playerVersion = swfobject.getFlashPlayerVersion();
var flashCurVer = playerVersion.major + "." + playerVersion.minor + "." + playerVersion.release;

//升级flash
if(flashCurVer < swfVersionStr){
	location.href="/rooms/getflash.html";
}
function SetBeforeUnLoad(fn){
	if(client == ''){
		window.onbeforeunload = fn;
	}
}

function UnloadCancel() { 
	var waring = "你是否真的要关闭窗口？ "; 
	return waring;
}

var message = {
	time: 0,
	title: document.title,
	timer: null,
	// 显示新消息提示
	show: function () {
		var title = message.title.replace("【　　　】", "").replace("【新消息】", "");
		// 定时器，设置消息切换频率闪烁效果就此产生
		message.timer = setInterval(function () {
			message.time++;
			//  message.show();
			if (message.time % 2 == 0) {
				document.title = "【新消息】" + title
			}else{
				document.title = "【　　　】" + title
			};
		}, 600);
		return [message.timer, message.title];
	},
	// 取消新消息提示
	clear: function () {
		clearInterval(message.timer);
		document.title = message.title;
		message.timer = null;
	}
};
 
function blink(){
	if(!message.timer){
		message.show(); 
	}
}

function clear_blink(){
	message.clear();
}
</script>
</head>
<body>
<div style="display:none">
<script type="text/javascript">
window.onload = function(){
	flashLoad();
	onLoadTime = new quality().getPageLoadTime();
	new quality().addlog({onLoadTime:onLoadTime,ajaxTime:ajaxtime},'Response',1000);
	$('body').css('background-image',"url(<?php echo PIC_API_PATH;?>/roombg/<?php echo $_GET['roomid'];?>/0/0.jpg) repeat top left;");
}
window.onunload = function(){
	var obj = document.getElementById("ktv_frame");
	if(!obj)return;
	obj.src = "about:blank";
}
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F5698333870a14f89a37f58c9c30ffe60' type='text/javascript'%3E%3C/script%3E"));
</script>
</div>
<iframe id="ktv_frame" width="100%" height="100%" frameborder="0" style="display:block;"></iframe>
</body>
</html>