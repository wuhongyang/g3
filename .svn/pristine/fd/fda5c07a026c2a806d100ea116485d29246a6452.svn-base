<?php
require_once 'common.php';

$module = $_GET['module'];
//验证用户是否登录
$user['Nick'] = stripcslashes($user['Nick']);

/*
if(is_numeric($_REQUEST['select_uin'])&&$_REQUEST['select_uin']>0){
	$Uin=$_REQUEST['select_uin'];
	$data=array(
		'extparam'=>array(
			'Tag'=>'GetUserBasicForUin',
			'Uin'=>$Uin
		)
	);
	$userInfo=httpPOST(SSO_API_PATH,$data);
	$userInfo=$userInfo['baseInfo'];
	if(empty($userInfo)){
		header("Location:http://{$_SERVER['HTTP_HOST']}/passport/index.php?account&url={$_SERVER['HTTP_REFERER']}");
	}
	$userInfo['nick'] = $userInfo['nick'];
	$user['Nick'] = $userInfo['nick'];
	$returnUin=$Uin;
	unset($_POST['select_uin']);
	unset($_GET['select_uin']);
}
	
//通行证下的UIN
$param = array(
	'extparam' => array('Tag'=>"AllUins",'Uid'=>$user['Uid']),
	'param' => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10247,'ChildId'=>101)
);
$uinList=request($param);
$uinList=$uinList['Result'];
//判断UIN
$check=false;
foreach($uinList as $val){
	if($val['uin']==$Uin){
		$check=true;
		$user['Uin']=$val['uin'];
		$user['Nick']=$val['nick'];
		break;
	}
}
if(!$check){
	$user = checkLogin();
	$Uin = $user['Uin'];
	$user['Nick'] = stripcslashes($user['Nick']);
}*/

$Uin = $user['Uin'];
$Nick = stripcslashes($user['Nick']);
$Uid = $user['Uid'];

//客户端
$client = array(
	'unknow' => '未知设备',
	'web'    => '来自网页',
	'system' => '系统广播',
);

if($module=='infocenter' || empty($module)){
	$tag = 'GetWeiboByFriends';
	if($_GET['type']=='atme') $tag = 'getRelayMyWeibo';
	$param = array(
		'extparam' => array('Tag'=>$tag,'Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$result = request($param);
	$lists = $result['Result'];
	foreach ($lists as $key => $val) {
		$lists[$key]['tuser'] = stripcslashes(htmlspecialchars_decode($val['tuser'],ENT_QUOTES));
	}
	$count = count($lists);
	$page = $result['Page'];
	
	$param = array(
		'extparam' => array('Tag'=>'CountWeibo','Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$countWeibo = intval(request($param));
	$param = array(
		'extparam' => array('Tag'=>'GetFollowNum','Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$countFans = request($param);
	$countFans = $countFans['Num'];
	$param = array(
		'extparam' => array('Tag'=>'GetFansNum','Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$countFriends = request($param);
	$countFriends = $countFriends['Num'];
/*	$param = array(
		'extparam' => array('Tag'=>'RecommendFans','Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$recommendFans = request($param);*/
	
	//角色图标
	$rolesIcon = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetRolesIcon','Uin'=>$Uin)));
	$rolesIcon = $rolesIcon['RolesIcon'];
	
	$template = "weibo/infocenter.html";

}elseif($module=='publish'){
	$param = array(
		'extparam' => array('Tag'=>'AddWeibo','Nick'=>$user['Nick'],'Post'=>$_POST,'Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102,'Uin'=>$Uin)
	);
	$res = request($param);
	$message = '';
	if($res['Flag'] != 100) alertMsg($res['FlagString']);
	if(isset($returnUin)){
		header("Location:?module=infocenter&select_uin=".$returnUin);
	}
	else{
		header("Location:?module=infocenter");
	}

}elseif($module=='delete'){
	$param = array(
		'extparam' => array('Tag'=>'DelWeibo','Tid'=>$_GET['tid'],'Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102,'Uin'=>$Uin)
	);
	$res = request($param);
	header("Location:{$_SERVER['HTTP_REFERER']}");

}elseif($module=='myfollower'){
	$param = array(
		'extparam' => array('Tag'=>'GetFansNum','Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$countFriends = request($param);
	$countFriends = $countFriends['Num'];
	$param = array(
		'extparam' => array('Tag'=>'ListFans','Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102,'Uin'=>$Uin)
	);
	$res = request($param);
	$lists = $res['Result'];
	$page = $res['Page'];
	$Uin = (int)$_GET['user'] > 0 ? $_GET['user'] : $Uin;
	$user_info = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$Uin)));
	//角色图标
	$rolesIcon = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetRolesIcon','Uin'=>$Uin)));
	$rolesIcon = $rolesIcon['RolesIcon'];
	$template = 'weibo/myfollower.html';

}elseif($module=='myfollowing'){
	$param = array(
		'extparam' => array('Tag'=>'GetFollowNum','Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$countFans = request($param);
	$countFans = $countFans['Num'];
	$param = array(
		'extparam' => array('Tag'=>'ListFollow','Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$res = request($param);
	$lists = $res['Result'];
	$count = count($lists);
	$page = $res['Page'];
	$Uin = (int)$_GET['user'] > 0 ? $_GET['user'] : $Uin;
	$user_info = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$Uin)));
	//角色图标
	$rolesIcon = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetRolesIcon','Uin'=>$Uin)));
	$rolesIcon = $rolesIcon['RolesIcon'];
	$template = 'weibo/myfollowing.html';

}elseif($module=='movefollow'){
	$param = array(
		'extparam' => array('Tag'=>'MoveFollow','Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>101,'Uin'=>$Uin,'TargetUin'=>array($_GET['uin']))
	);
	$res = request($param);
	header("Location:?module=myfollowing");
}elseif($module=='movefans'){
	$data=array(
		'id'=>intval($_GET['id']),
		'Uin'=>$Uin,
	);
	$param = array(
		'extparam' => array('Tag'=>'MoveFans','Data'=>$data),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>106,'Uin'=>$Uin)
	);
	$res = request($param);
	header("Location:?module=myfollower");
}elseif($module=='addfollow'){
	$param = array(
		'extparam' => array('Tag'=>'AddFollow','Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10037,'ChildId'=>104,'Uin'=>$Uin,'TargetUin'=>array($_GET['uin']))
	);
	$res = request($param);
	header("Location:?module=myfollowing");
}

$title='我的微博';
$serviceType='weibo';
if($themes=='default'){
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','service'));
}
else{
	$tmp_config=get_config('template','group_site');
	$tmp_config['template_dir'].=$themes.'/tpl/service/';
	$tmp_config['cache_dir'].=$themes.'/tpl/service/';
	$tpl = template::getInstance();
	$tpl->setOptions($tmp_config);
}
include template($template,$tpl);