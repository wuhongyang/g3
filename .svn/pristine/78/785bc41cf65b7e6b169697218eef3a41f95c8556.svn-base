<?php
require_once 'common.php';

//$isArtist = getChannelType($user['Uin']);

//获取全站等级
// $param = array(
		// 'extparam' => array('Tag'=>'GetLevel','Data'=>array('UinId'=>$user['Uin'])),
		// 'param'    => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10312,'ChildId'=>101)
// );
// $result = request($param);
// $levelinfo = $result['Data'];

//得到通行证信息
$passRst = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$user['Uin'],'Status'=>1)));
$passInfo = array('name'=>$passRst['Name'],'idcard'=>$passRst['IdCard'],'phone'=>$user['Phone'],'email'=>$user['Email'],'username'=>$passRst['UserName']);

//微博信息
$param = array(
	'extparam' => array('Tag'=>'CountWeibo','Uin'=>$user['Uin']),
	'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
);
$weiboNum = intval(request($param));

//我的粉丝数量
$param = array(
	'extparam'=>array('Tag'=>"GetFansNum",'Uin'=>$user['Uin']),
	'param'=>array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=> 10037,'ChildId'=> 108,'Client'=> 'WEB ADMIN','Desc'=>'我的粉丝数量')
);
$fansNum = request($param);
$fansNum = intval($fansNum['Num']);

//我的关注数量
$param = array(
	'extparam' => array('Tag'=>"GetFollowNum",'Uin'=>$user['Uin']),
	'param'=>array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10037,'ChildId'=>109,'Client'=>'WEB ADMIN','Desc'=>'我的关注数量')
);
$follower = request($param);
$followerNum = intval($follower['Num']);


//待办事项
$param = array(
	'extparam' => array('Tag'=>'UnRead'),
	'param' => array('BigCaseId'=>10004,'CaseId'=>10035,'ParentId'=>10250,'ChildId'=>101)
);
$handleMatters = request($param);
$handleMatters = (array)$handleMatters['List'];

//等级
$param = array(
	'extparam'=>array('Tag'=>'ShowBasic','Uin'=>$user['Uin']),
	'param'=>array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10024,'ChildId'=>102)
);
$result = request($param);
// $level_array = $result['Level_array'];
//角色图标
$rolesIcon = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetRolesIcon','Uin'=>$user['Uin'])));
$rolesIcon = $rolesIcon['RolesIcon'];

//V豆
$kmoney = get_money($user['Uin']);
//V点
$vmoney = get_money($user['Uin'],$user['GroupId']);

//nick
$userInfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$user['Uin'])));

$info = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$user['Uin'],'Status'=>1)));

$currentUinInfo = array(
	'level' => $level_array,
	'icon' => $rolesIcon,
	'kmoney' => $kmoney,
	'vmoney' => $vmoney,
	'nick' => $userInfo['Nick'],
	'isArtist' => $isArtist
);

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
$channelInfo = getChannelInfo($user['Uin']);
include template('personal/index.html',$tpl);