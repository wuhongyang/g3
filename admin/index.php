<?php
session_start();
header('Content-Type:text/html;charset=utf-8');
include '../library/global.fun.php';
//退出登录
if($_GET['logout']){
	$_SESSION['adminLogin'] = array();
	header('Location:login.php');
	exit;
}

//登录
if(!empty($_POST['username']) && !empty($_POST['password'])){
	$_SESSION['adminLogin'] = array();
	if(strtolower($_POST['captcha']) != $_SESSION['captcha']){
		exit('<script type="text/javascript">alert("验证码错误");window.history.back();</script>');
	}
	$result = httpPOST(SSO_API_PATH,array('param'=>array('Uin'=>$_POST['username'],'SessionKey'=>md5($_POST['password']),'GroupId'=>10000),'extparam'=>array('Tag'=>'UserLogin',"Ip"=>get_ip())));
	if($result['Flag'] != 100){
		exit('<script type="text/javascript">alert("'.$result['Flag'].$result['FlagString'].'");window.top.location.href="login.php"</script>');
	}
	$_SESSION['adminLogin'] = $result;
	header('Location:index.php');
	exit;
}

//验证用户登录
if( ! empty($_SESSION['adminLogin'])){
	$param = array(
		'extparam' => array('Tag'=>'GetAdminLeftMenu'),
		'param'    => array('BigCaseId'=>10002,'CaseId'=>10006,'ParentId'=>10012,'ChildId'=>104,'Uin'=>$_SESSION['adminLogin']['Uin'],'SessionKey'=>$_SESSION['adminLogin']['Token'],'Desc'=>'验证用户登录')
	);
	$result = request($param);
}else{
	$result['FlagString'] = '请登陆';
}

//登录失败
if($result['Flag'] != 100){
	$_SESSION['adminLogin'] = array();
	exit('<script type="text/javascript">alert("'.$result['Flag'].$result['FlagString'].'");window.top.location.href="login.php"</script>');
}
$menu = $result['Result'];
$menu_num = count($menu);
$menu_url = get_config('menu_url');

//当前使用默认站
if(!$_COOKIE['__ADMIN_CURGROUP']){
	setcookie('__ADMIN_CURGROUP','{"groupid":10000,"name":"10000"}');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>
<title>VV酷后台管理</title>
<link rel="stylesheet" type="text/css" href="template/css/default.css" />
<link rel="stylesheet" type="text/css" href="template/js/themes/default/easyui.css" />
<style type="text/css">
table{border-collapse:collapse;border-spacing:0;}
.main-width{width:98%; margin:0 auto;}
.table-list{text-align:center;border:solid 1px #CCC;font-size:12px;}
.table-list tr th{background:#E0ECFF;padding:5px;border:solid 1px #CCC;}
.table-list tr td{padding:5px;border:solid 1px #CCC;}
.table-list tr:hover{background:#E0ECFF;}
.runtime{color:#999;float:right;padding:10px;text-align:right;}
</style>
<script type="text/javascript">
var _menus = {"menus":[
			<?php foreach($menu as $m_k=>$m_v): ?>
            {"menuid":"1","icon":"icon-sys","menuname":"<?php echo $m_v['case_name'] ?>",
                "menus":[
                		<?php $child_menu = $m_v['parent']; $child_num = count($child_menu);$c_count=0; ?>
                        <?php foreach($child_menu as $c_k=>$c_v): ?>
                        {"menuname":"<?php echo $c_v['parent_name']; ?>","icon":"icon-nav","url":"<?php echo $menu_url[$c_v['parent_id']]['url']; ?>"}<?php if($c_count != $child_num-1) echo ','; ?>
						<?php $c_count++;?>
                        <?php endforeach; ?>
                    ]
            }<?php if($m_k != $menu_num-1) echo ','; ?>
           <?php endforeach; ?>
    ]};
</script>
<script type="text/javascript" src="template/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="template/js/jquery.easyui.min.js"></script>
<script type="text/javascript" src="template/js/jquery.cookie.min.js"></script>
<script type="text/javascript" src="template/js/jquery.url.min.js"></script>
<script type="text/javascript" src='template/js/outlook2.1.js'></script>
<script type="text/javascript">
$(function(){
	var __ADMIN_CURGROUP = $.cookie('__ADMIN_CURGROUP');
	var __ADMIN_CURGROUP = eval("("+__ADMIN_CURGROUP+")");
	if(__ADMIN_CURGROUP){
		$('#curGroup').html('当前站点：'+__ADMIN_CURGROUP.name+'（'+__ADMIN_CURGROUP.groupid+'）');
	}
});
function groupCutover(groupid,name,prevGroupid){
	$.cookie('__ADMIN_CURGROUP','{"groupid":'+groupid+',"name":"'+name+'"}');
	$('#curGroup').html('当前站点：'+name+'（'+groupid+'）');
	refreshTab(groupid,prevGroupid);
	closeWindow();
}
</script>
</head>
<body class="easyui-layout" style="overflow-y:hidden" scroll="no">

<noscript>
<div style="position:absolute;z-index:100000;height:2046px;top:0px;left:0px;width:100%;background:white;text-align:center;">
	<img src="template/images/noscript.gif" alt='抱歉，请开启脚本支持！' />
</div>
</noscript>

<div class="header" region="north" split="true" border="false" style="overflow:hidden;height:35px;background:url(template/images/layout-browser-hd-bg.gif) #7f99be repeat-x center;line-height:30px;color:#fff;">
	<span style="float:right; padding-right:20px;" class="head">
		<?php echo "<font title=\"{$result['Uin']}\">{$result['Nick']}({$result['Group_name']})</font>" ?> &nbsp;
        <a href="#" onclick="addTab('密码修改','passport.php?module=adminPassEdit','icon');return false;">修改密码</a> 
        <a href="#" id="loginOut">安全退出</a>
	</span>
	<span style="color:#FFF;margin-left:10px;font-size:14px;"><b style="color:#ff0000;"><?php echo $_SERVER['HOSTNAME'];?>平台</b> - 后台管理</span>
	<a href="javascript:openWindow();" style="color:#FFF;margin-left:10px;font-size:12px;">[站点切换]</a> &nbsp;
	<span id="curGroup">当前站点：</span>
</div>

<!-- modal window -->
<div id="window" class="easyui-window" title="站点切换" style="padding:10px;">
    
</div>
<!-- modal window -->

<!--<div region="south" split="true" style="height: 30px; background: #D2E0F2; ">
	<div class="footer">vv酷后台管理_v1.0</div>
</div>-->

<div region="west" split="true" title="导航菜单" style="width:185px;" id="west">
	<div id="nav" class="easyui-accordion" fit="true" border="false"><!--  导航内容 --></div>
</div>

<!--默认内容页面-->
<div id="mainPanle" region="center" style="overflow-y:hidden">
	<div id="tabs" class="easyui-tabs"  fit="true" border="false">
		<div title="欢迎使用" style="overflow:hidden;" id="home">
			<iframe width="100%" height="100%" src="systemindex.php"></iframe>
		</div>
	</div>
</div>

<div id="mm" class="easyui-menu" style="width:150px;">
	<div id="mm-tabclose">关闭</div>
	<div id="mm-tabcloseall">全部关闭</div>
	<div id="mm-tabcloseother">除此之外全部关闭</div>
	<div class="menu-sep"></div>
	<div id="mm-tabcloseright">当前页右侧全部关闭</div>
	<div id="mm-tabcloseleft">当前页左侧全部关闭</div>
</div>
</body>
</html>