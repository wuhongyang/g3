<?php
include_once('library/common.php');
include_once dirname(dirname(__FILE__)).'/group/reg_info_config.php';

if($groupId<=0){
	header("Location:/");
}
$uin=intval($_GET['uin']);
if($uin<=0){
	header("Location:/");
}
$action=empty($_GET['act'])?'marry':$_GET['act'];

//验证用户是否登录
$user = checkLogin();
$themes=$GroupData['Template']!=''?$GroupData['Template']:'default';

//个人资料
$userInfo=httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$uin)));
$userInfo=$userInfo['baseInfo'];
if(empty($userInfo)){
	header("Location:/");
}

//高级资料
$userAdvancedInfo=httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserAdvanced','Uin'=>$uin,'GroupId'=>$groupId)));
$userAdvancedInfo=$userAdvancedInfo['advanced']['info'][$action];

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/';
$tmp_config['cache_dir'].=$themes.'/tpl/';

$advancedInfo=$reg_info_config[$themes][$action];
$moduleAction='advanced';
$back_url=base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']);
$template=$action.'.html';
$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template('profile/'.$template,$tpl);