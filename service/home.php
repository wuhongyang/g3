<?php
require_once 'common.php';

//$Uin  = intval($user['Uin']);
$Uin = empty($_GET['user'])? $user['Uin'] : intval($_GET['user']);
$user_info = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$Uin)));
if($user_info['Flag'] != 100){
	alertMsg('用户不存在','/');
}
$Nick = $user_info['Nick'];

//客户端
$client = array(
	'unknow' => '未知设备',
	'web'    => '来自网页',
	'system' => '系统广播',
);
//角色图标
$rolesIcon = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetRolesIcon','Uin'=>$Uin)));
$rolesIcon = $rolesIcon['RolesIcon'];

if($_GET['module']=='home' || empty($_GET['module'])){
	$param = array(
		'extparam' => array('Tag'=>'WeiboHome','Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$result = request($param);
	$lists = $result['Result'];
	$page = $result['Page'];
	$param = array(
		'extparam' => array('Tag'=>'CountWeibo','Uin'=>$Uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$countWeibo = intval(request($param));
	$template = "weibo/home.html";
}elseif($_GET['module']=='follower'){
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
	$template = 'weibo/follower.html';

}elseif($_GET['module']=='following'){
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
	$page = $res['Page'];
	$template = 'weibo/following.html';
}
elseif($_GET['module']=='video'){
	$param = array(
			'extparam' => array('Tag'=>'VideoList','GroupId'=>$group_id,'Uin'=>$Uin),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10644,'ChildId'=>101)
	);
	$videoList = request($param);
	$videoList = $videoList['Data'];
	$temp = 'video';
	$template = 'weibo/video.html';
}

$back_url=base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']);
$title=$Nick.'的微博';
$moduleAction='weibo';
$serviceType='weibo';

if($themes=='default'){
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','service'));
}
else{
	$uin=$Uin;
	$tmp_config=get_config('template','group_site');
	$tmp_config['template_dir'].=$themes.'/tpl/service/';
	$tmp_config['cache_dir'].=$themes.'/tpl/service/';
	$tpl = template::getInstance();
	$tpl->setOptions($tmp_config);
}
include template($template,$tpl);

