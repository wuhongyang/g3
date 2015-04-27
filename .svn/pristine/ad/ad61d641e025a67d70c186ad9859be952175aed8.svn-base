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

//验证用户是否登录
$user = checkLogin();

//个人资料
$userInfo=httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$uin)));
$userInfo=$userInfo['baseInfo'];
if(empty($userInfo)){
	header("Location:/");
}
$province_name=httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetProvinceName','ProvinceId'=>$userInfo['province'])));
$userInfo['province']=$province_name['provinceName'];
$city_name=httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCityName','CityId'=>$userInfo['city'])));
$userInfo['city']=$city_name['cityName'];

$param=array(
	'extparam'=>array('Tag'=>'GetFansNum','Uin'=>$uin),
	'param'=>array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
);
$countFriends=request($param);
$countFriends=$countFriends['Num'];

$themes=$GroupData['Template']!=''?$GroupData['Template']:'default';

//高级资料
$userAdvancedInfo=httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserAdvanced','Uin'=>$uin,'GroupId'=>$groupId)));
$userAdvancedInfo=$userAdvancedInfo['advanced']['info'];
//婚恋状况
$is_marry=$reg_info_config[$themes]['marry']['is_marry']['value'][$userAdvancedInfo['marry']['is_marry']];
//学历
$education=$reg_info_config[$themes]['marry']['education']['value'][$userAdvancedInfo['marry']['education']];
//住房情况
$house_status=$reg_info_config[$themes]['marry']['house_status']['value'][$userAdvancedInfo['marry']['house_status']];
//月收入
$salary=$reg_info_config[$themes]['marry']['salary']['value'][$userAdvancedInfo['marry']['salary']];
//购车情况
$has_car=$reg_info_config[$themes]['marry']['has_car']['value'][$userAdvancedInfo['marry']['has_car']];

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/';
$tmp_config['cache_dir'].=$themes.'/tpl/';

$moduleAction='profile';
$back_url=base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']);
$template='profile';
$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template('profile/'.$template.'.html',$tpl);