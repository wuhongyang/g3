<?php
session_start();
include '../library/global.fun.php';
$GroupData = domain::main()->GroupData();

//QQ登陆参数
if($_GET['login']=='qq'){
	$groupExtInfo=json_decode($GroupData['EXT'],true);
	$callback=$groupExtInfo['callback']['value'];
	if(empty($callback)){
		$callback="openlogin.vvku.com";
	}
	$url="http://{$callback}/passport/openlogin/qq/login.php?back={$_SERVER['HTTP_HOST']}&redirect=/&dplogin=1";
	header("location:{$url}");
	exit;
}

//登录
if(!empty($_POST['username']) && !empty($_POST['password'])){
	/*$_SESSION['groupLogin'] = array();
	$result = httpPOST(SSO_API_PATH,array('param'=>array('Uin'=>$_POST['username'],'SessionKey'=>md5($_POST['password'])),'extparam'=>array('Tag'=>'UserLogin',"Ip"=>get_ip())));
	if($result['Flag'] != 100){
		alertMsg($result['FlagString']);
	}
	$_SESSION['groupLogin'] = $result;*/
	//验证登录
	$param = array(
		'extparam' => array('Tag'=>'DpUserLogin','Data'=>$_POST),
		'param'    => array('BigCaseId'=>10006,'CaseId'=>10051,'ParentId'=>10279,'ChildId'=>101,'Desc'=>'站用户登录','GroupId'=>$GroupData['groupid'])
	);
	$param['extparam']['Data']['GroupId'] = $GroupData['groupid'];
	$rst = request($param);
	if($rst['Flag'] != 100){
		alertMsg($rst['FlagString'],'/group/index.php');
	}
	alertMsg('','/group/mgr.html#/group/group.php?module=group_info');
}else{
	$user = checkDpLogin(false);
	if($user['Flag'] == 100){
		alertMsg('','/group/mgr.html#/group/group.php?module=group_info');
	} 
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','group'));
	include template('permission/login.html',$tpl);
}