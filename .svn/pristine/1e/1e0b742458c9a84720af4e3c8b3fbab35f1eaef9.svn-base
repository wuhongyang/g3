<?php
include_once('library/common.php');

if($groupId<=0){
	header("Location:/");
}

//验证用户是否登录
$user = checkLogin();

$uin = empty($_GET['uin'])? $user['Uin'] : intval($_GET['uin']);
$user_info = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$uin)));
if($user_info['Flag'] != 100){
	alertMsg('用户不存在','/');
}

//角色图标
$rolesIcon = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetRolesIcon','Uin'=>$uin)));
$rolesIcon = $rolesIcon['RolesIcon'];

if($_GET['module']=='home' || empty($_GET['module'])){
	$param = array(
		'extparam' => array('Tag'=>'WeiboHome','Uin'=>$uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$result = request($param);
	$lists = $result['Result'];
	$page = $result['Page'];
	$param = array(
		'extparam' => array('Tag'=>'CountWeibo','Uin'=>$uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$countWeibo = intval(request($param));
	$template = "weibo/home.html";
}elseif($_GET['module']=='follower'){
	$param = array(
		'extparam' => array('Tag'=>'GetFansNum','Uin'=>$uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$countFriends = request($param);
	$countFriends = $countFriends['Num'];
	$param = array(
		'extparam' => array('Tag'=>'ListFans','Uin'=>$uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102,'Uin'=>$uin)
	);
	$res = request($param);
	$lists = $res['Result'];
	$page = $res['Page'];
	$template = 'weibo/follower.html';

}elseif($_GET['module']=='following'){
	$param = array(
		'extparam' => array('Tag'=>'GetFollowNum','Uin'=>$uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$countFans = request($param);
	$countFans = $countFans['Num'];
	$param = array(
		'extparam' => array('Tag'=>'ListFollow','Uin'=>$uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
	);
	$res = request($param);
	$lists = $res['Result'];
	$page = $res['Page'];
	$template = 'weibo/following.html';
}
$title=$user_info['Nick'].'的微博';

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/';
$tmp_config['cache_dir'].=$themes.'/tpl/';

$moduleAction='weibo';
$back_url=base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']);
$tpl = template::getInstance();
$tpl->setOptions($tmp_config);
include template($template,$tpl);

