<?php
require_once '../library/global.fun.php';
$GroupData = domain::main()->GroupData();
$ext = json_decode($GroupData['EXT'], true);
$callback = $ext['callback']['value'];
if(empty($callback)) $callback = "openlogin.vvku.com";
//用户信息
/*
$param = array(
	'extparam' => array('Tag'=>'GetLoginUser'),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
);
$user = request($param);
*/
$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
if(isset($_COOKIE['GUEST_LOGIN_TOKEN']) && empty($user['Uin'])){
	$user = json_decode($_COOKIE['GUEST_LOGIN_TOKEN'],true);
	setcookie('GUEST_LOGIN_TOKEN',$_COOKIE['GUEST_LOGIN_TOKEN'],time()+86400*30,'/');
	$user['Token'] = $user['SessionKey'];
}
$ip=get_ip();
//房间信息
$param = array(
	'extparam' => array('Tag'=>'GetRoomInfo','Roomid'=>$_GET['roomid']),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>102)
);
$room_info = request($param);
if(empty($room_info['play_media_conf'])) $room_info['play_media_conf'] = '{}';
if(empty($room_info['admin_media_conf'])) $room_info['admin_media_conf'] = '{}';
if(empty($room_info['p2p_media_conf'])) $room_info['p2p_media_conf'] = '{}';

//房间404
if(empty($room_info) || $room_info['group'] != $GroupData['groupid']){
	$template = 'ktv_404.html';
}else{
	if(!empty($room_info['room_version'])){
		$room_info['room_version'] = '_'.$room_info['room_version'];
	}elseif(!empty($room_info['template_ui'])){
		$room_info['room_version'] = $room_info['template_ui'];
	}
	//elseif(!empty($GroupData['KtvTemplate'])){
	//	$room_info['room_version']=$GroupData['KtvTemplate'];
	//}
	if($_GET['type'] == 'g'){
		$template = 'game_flash.html';
	}else{
		if(strpos($room_info['template_ui'],'html') === false){
			$template = 'ktv_flash.html';
		}else{
			$template = 'ktv_html.html';
		}
	}
	
	if($_SERVER["HOSTNAME"] == 'test'){
		// $md5 = md5_file("http://{$_SERVER['HTTP_HOST']}/g3_ktv_flash{$room_info['room_version']}/tim.txt");
		// $tim = socket_request("http://{$_SERVER['HTTP_HOST']}/g3_ktv_flash{$room_info['room_version']}/tim.txt");
		// $g3_ktv = "http://{$_SERVER['HTTP_HOST']}/g3_ktv_flash{$room_info['room_version']}";
		//flash版本KTV需要以下处理
		if(strpos($room_info['template_ui'],'html') === false){
			if(!empty($room_info['template_ui'])){
					$tim =trim(socket_request("http://{$_SERVER['HTTP_HOST']}/{$_SERVER["HOSTNAME"]}/themes/g3/ktv/{$room_info['room_version']}/tim.txt"));
					$g3_ktv = "http://{$_SERVER['HTTP_HOST']}/{$_SERVER["HOSTNAME"]}/themes/g3/ktv/{$room_info['room_version']}";
					$core_path ="http://{$_SERVER['HTTP_HOST']}/{$_SERVER["HOSTNAME"]}/themes/g3/";
			}else{
				$tim = trim(socket_request("http://{$_SERVER['HTTP_HOST']}/g3_ktv_flash{$room_info['room_version']}/tim.txt"));
				$g3_ktv = "http://{$_SERVER['HTTP_HOST']}/g3_ktv_flash{$room_info['room_version']}";
			}
		}
	}else{
		// $md5 = md5_file("http://vvku-flash.oss-cn-hangzhou.aliyuncs.com/{$_SERVER["HOSTNAME"]}/g3_ktv_flash{$room_info['room_version']}/tim.txt");
		//cptest/themes/g3/ktv/flash_p1_3Video/
		//flash版本KTV需要以下处理
		if(strpos($room_info['template_ui'],'html') === false){
			if(!empty($room_info['template_ui'])){
				$tim = trim(socket_request("http://vvku-flash.oss-cn-hangzhou.aliyuncs.com/{$_SERVER["HOSTNAME"]}/themes/g3/ktv/{$room_info['room_version']}/tim.txt"));
				$g3_ktv = "http://flash.".getRootDomain()."/{$_SERVER["HOSTNAME"]}/themes/g3/ktv/{$room_info['room_version']}";
				$core_path ="http://flash.".getRootDomain()."/{$_SERVER["HOSTNAME"]}/themes/g3/";
			}else{
				$tim = trim(socket_request("http://vvku-flash.oss-cn-hangzhou.aliyuncs.com/{$_SERVER["HOSTNAME"]}/g3_ktv_flash{$room_info['room_version']}/tim.txt"));
				$g3_ktv = "http://flash.".getRootDomain()."/{$_SERVER["HOSTNAME"]}/g3_ktv_flash{$room_info['room_version']}";
			}
		}
	}
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template($template,$tpl);
