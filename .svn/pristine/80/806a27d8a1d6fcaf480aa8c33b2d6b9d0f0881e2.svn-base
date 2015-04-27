<?php
require '../library/global.fun.php';
$GroupData = domain::main()->GroupData();
get_config('template','admin');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>API测试</title>
<script type="text/javascript" src="<?php echo ADMIN_THEMES_URL; ?>js/ajax.fun.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_THEMES_URL; ?>js/global.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_THEMES_URL; ?>js/md5.js"></script>
<script type="text/javascript" src="extparam.php"></script>
<style type="text/css">
*{padding:0;margin:0;font-size:13px;}
body{background:#EDFCEB;}
.title{background:#060;color:#FFF;font-size:16px;padding:10px;}
.param{display:inline-block;width:100%;}
.btn{padding:20px 10px;line-height:25px;}
.btn .submit{width:320px;text-align:center;border:solid 1px #000;}
.testRow{border:solid 1px #093;border-top:none;line-height:32px;padding:5px 10px;}
</style>
</head>

<body>
<div class="box">
    <h1 class="title">接口测试</h1>
    <form class="testForm" action="request_api.php" method="post" target="_blank">
		<div class="testRow">
			<div>发起用户：<input type="text" id="Uin" name="Uin" value="admin@aodiansoft.com" onkeyup="getExtparam(document.getElementById('ChildId'))" onblur="getExtparam(document.getElementById('ChildId'))" /></div>
			<div>发起密码：<input type="text" id="SessionKey" name="SessionKey" value="123456" onkeyup="getExtparam(document.getElementById('ChildId'))" onblur="getExtparam(document.getElementById('ChildId'))" /> 是否MD5<input type="checkbox" name="md5" id="md5" value="1" onclick="getExtparam(document.getElementById('ChildId'))" /></div>
			<div>业 务 ID：<input type="text" id="ChannelId" name="ChannelId" value="5880153" onkeyup="getExtparam(document.getElementById('ChildId'))" onblur="getExtparam(document.getElementById('ChildId'))" />（渠道ID,房间ID）</div>
			<div>分 站 ID：<input type="text" id="GroupId" name="GroupId" value="<?php echo $GroupData['groupid']?>" onkeyup="getExtparam(document.getElementById('ChildId'))" onblur="getExtparam(document.getElementById('ChildId'))" />（分站ID,群ID）</div>
			<div>目标用户：<input type="text" id="TargetUin" name="TargetUin" value="123456" onkeyup="getExtparam(document.getElementById('ChildId'))" onblur="getExtparam(document.getElementById('ChildId'))" /></div>
			<div>客 户 端：<input type="text" id="Client" name="Client" value="WEB Client" onkeyup="getExtparam(document.getElementById('ChildId'))" onblur="getExtparam(document.getElementById('ChildId'))" /></div>
			<div>行为权重：<input type="text" id="DoingWeight" name="DoingWeight" value="1" onkeyup="getExtparam(document.getElementById('ChildId'))" onblur="getExtparam(document.getElementById('ChildId'))" /></div>
			<div>资金权重：<input type="text" id="MoneyWeight" name="MoneyWeight" value="1" onkeyup="getExtparam(document.getElementById('ChildId'))" onblur="getExtparam(document.getElementById('ChildId'))" /></div>
			<div>描　　述：<input type="text" id="Desc" name="Desc" value="Test Platform" onkeyup="getExtparam(document.getElementById('ChildId'))" onblur="getExtparam(document.getElementById('ChildId'))" /></div>
            <div>
            	<label>请求次数：<input type="text" id="reqnum" name="num" value="1" /></label>
                使用Http<input type="checkbox" name="http" value="1" />
            </div>
			<div><label>并发次数：<input type="text" id="paiallel_num" name="parallel_num" value="1" /></label> 请求时长<input type="text" id="time" name="time" value="1" size="4" />秒</div>
			<!--<div><label>并发方式：<label>iframe加载<input type="radio" name="load_type" value="iframe" /> *用窗口加载方式，可能会受到浏览器本身打开窗口的个数所限制！窗口加载方式，暂不支持Chrome浏览器！</label></div>-->
            <div>其他功能：<a href="javascript:;" onclick="testfunc('check_api')">接口检测</a>　<a href="javascript:;" onclick="testfunc('stress')">PHP压力测试</a>　<a href="./stress_result.php" target="_blank">PHP压力测试结果</a>　<a href="javascript:;" onclick="testfunc('http_load')">http_load压力测试</a>　<a href="./http_load_result.php" target="_blank">http_load压力测试结果</a>  <a href="javascript:;" onclick="testfunc('ptest')">ptest压力测试</a>  <a href="./ptest_result.php" target="_blank">ptest压力测试结果</a>　<a href="./count_api.php" target="_blank">性能分析</a></div>
        </div>
        <div id="testRows">
            <div id="testRow" class="testRow">
                <label>选择科目：</label><select name="BigCaseId" id="BigCaseId" class="BigCaseId" onChange="getBusinessConfig('case='+this.value,this);"><option value="0">请选择</option></select>
                <select name="CaseId" id="CaseId" class="CaseId" onChange="getBusinessConfig('parent='+this.value,this);"><option value="0">请选择</option></select>
                <select name="ParentId" id="ParentId" class="ParentId" onChange="getBusinessConfig('child='+this.value,this);"><option value="0">请选择</option></select>
                <select name="ChildId" id="ChildId" class="ChildId" onchange="getExtparam(this)"><option value="0">请选择</option></select>
                <input type="hidden" class="param" />
                <span onclick="addRow()">增</span>
                <span onclick="delRow(this)">删</span>
                <div class="param"></div>
                <div class="descr"></div>
            </div>
        </div>
        <div class="btn">
            <div><input type="submit" class="submit" id="submit" value="提  交" /></div>
        </div>
		<input type="hidden" name="load_type" value="iframe" />
    </form>
</div>

<script language="javascript">
function testfunc(api){
	if(confirm('【慎重操作】该操作可能会影响运行速度或数据错误，确定运行？')){
		var uin = document.getElementById('Uin').value;
		var ses = document.getElementById('SessionKey').value;
		var cid = document.getElementById('ChannelId').value;
		var gid = document.getElementById('GroupId').value;
		var tid = document.getElementById('TargetUin').value;
		var dwt = document.getElementById('DoingWeight').value;
		var mwt = document.getElementById('MoneyWeight').value;
		var num = document.getElementById('reqnum').value;
		var pnum = document.getElementById('paiallel_num').value;
		var time = document.getElementById('time').value;
		window.open('./'+api+'.php?uin='+uin+'&session='+ses+'&channelid='+cid+'&groupid='+gid+'&target='+tid+'&doing='+dwt+'&money='+mwt+'&reqnum='+num+'&paiallel_num='+pnum+'&time='+time);
	}
}

getBusinessConfig('bigcase=1',false);
function getBusinessConfig(param,obj,selectid){
	if(typeof(selectid) == 'undefined') selectid = 0;
	new Ajax().ajaxRequest('','extparam.php',param,'get',true,
		function (result){
			var data = eval('('+result.responseText+')');
			if( ! obj){
				var selects = document.getElementById('testRow').getElementsByTagName('select');
				var opt = selects[0];
			}else{
				var selects = obj.parentNode.getElementsByTagName('select');
				var selectlen = selects.length;
				for(i=0;i<selectlen;i++){
					if(selects[i] == obj){
						if(i < selectlen){
							var opt = selects[i+1];
							opt.title = obj.value;
						}else{
							return true;
						}
					}
				}
			}
			opt.options.length = 0;
			opt.options.add(new Option('请选择',0));
			for(i=0;i<data.length;i++){
				var newopt = new Option('['+data[i].id+'] '+data[i].name,data[i].id);
				if(selectid==data[i].id) newopt.selected = true;
				opt.options.add(newopt);
			}
		}
	);
}

function getExtparam(obj){
	var Uin = document.getElementById('Uin').value;
	var SessionKey = document.getElementById('SessionKey').value;
	var ChannelId = document.getElementById('ChannelId').value;
	var GroupId = document.getElementById('GroupId').value;
	var TargetUin = document.getElementById('TargetUin').value;
	var Client = document.getElementById('Client').value;
	var DoingWeight = document.getElementById('DoingWeight').value;
	var MoneyWeight = document.getElementById('MoneyWeight').value;
	var Desc = document.getElementById('Desc').value;
	
	var selects = obj.parentNode.getElementsByTagName('select');
	var selectlen = selects.length;
	var divs = obj.parentNode.getElementsByTagName('div');
	var divlen = divs.length;
	var extkey = 'param_'+obj.title+'_'+obj.value;
	var param = '{';
	for(i=0;i<selectlen;i++){
		var o = i+1;
		if(o < selectlen){
			param += '"'+selects[i].className+'":'+selects[o].title+',';
		}else{
			param += '"'+selects[i].className+'":'+selects[i].value+',';
		}
	}
	SessionKey = (SessionKey != '' && checkbox('md5') == 1) ? hex_md5(SessionKey) : SessionKey;
	param += '"Uin":"'+Uin+'","SessionKey":"'+SessionKey+'","ChannelId":"'+ChannelId+'","GroupId":"'+GroupId+'","TargetUin":"'+TargetUin+'","Client":"'+Client+'","DoingWeight":"'+DoingWeight+'","MoneyWeight":"'+MoneyWeight+'","Desc":"'+Desc+'"}';
	if(typeof(extparams[extkey]) == 'undefined'){
		document.getElementById('submit').value = '接口不存在';
		document.getElementById('submit').disabled = true;
		return false;
	}else{
		document.getElementById('submit').value = '提交';
		document.getElementById('submit').disabled = false;
		var jsonparam = extparams[extkey];
	}
	for(i=0;i<divlen;i++){
		if(divs[i].className == 'param'){
			divs[i].innerHTML = '接口参数：<input type="text" name="param[]" class="param" value=\'{"param":'+ param +',"extparam":'+ jsonparam.extparam +'}\' />';
		}else if(divs[i].className == 'descr'){
			divs[i].innerHTML = '参数说明：param【Uin:用户ID,SessionKey:登录令牌,ChannelId:渠道ID,GroupId:分站ID,TargetUin:目标用户ID,Client:调用客户端,DoingWeight:行为权重(数量),MoneyWeight:金额,Desc:描述 】 extparam【'+ jsonparam.descr+'】';
		}
	}
}

function checkbox(name){
	var c = document.getElementsByName(name);
	var v = '';
	for(i=0;i <c.length;i++){
		if(c[i].checked == true){
			v = c[i].value;
			break;
		}
	}
	return v;
}

function addRow(){
	var testRow = document.getElementById('testRow');
	testRow.parentNode.innerHTML += '<div class="testRow">'+testRow.innerHTML+'</div>';
}

function delRow(obj){
	if(obj.parentNode.id != 'testRow')
	obj.parentNode.parentNode.removeChild(obj.parentNode);
}
</script>
</body>
</html>