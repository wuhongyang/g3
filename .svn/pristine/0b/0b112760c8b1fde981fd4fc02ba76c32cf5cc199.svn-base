<?php
exit;
/**
* 区域分站定位，分三种情况
* 1 没有cookie 没有city
* 2 有city
* 3 只有cookie
*/
/*
setcookie('USER_LOGIN_TOKEN','',-1,'/','.kkyoo.com');
setcookie('GUEST_LOGIN_TOKEN','',-1,'/','.kkyoo.com');
setcookie('PASSPORT_TOKEN','',-1,'/','.kkyoo.com');
setcookie('PASSPORT_OPENID','',-1,'/','.kkyoo.com');
setcookie('SiteData','',-1,'/','.kkyoo.com');
*/
if(isset($_GET['city'])){
	$param = array('extparam'=>array('Tag'=>'GetSite','Domain'=>$_GET['city']));
	$site = httpPOST(REGION_API_PATH,$param);
	if($site['Flag'] != 100){
		header("Location:/");exit;
	}
	$site = $site['Result'];
	unset($site['id'],$site['province'],$site['city'],$site['area'],$site['weather_id'],$site['domain'],$site['pic_url'],$site['cat_pic_json'],$site['uptime'],$site['is_hot'],$site['status']);
	$sitedata = json_encode($site);
	@setcookie('SiteData',$sitedata,time()+86400*30,'/',$_SERVER['HTTP_HOST']);
}elseif($sitedata = $_COOKIE['SiteData']){
	$site = json_decode($sitedata,true);
	if(empty($site)){
		setcookie('SiteData','',-1,'/',$_SERVER['HTTP_HOST']);
		header("Location:/");exit;
	}
	@setcookie('SiteData',$sitedata,time()+86400*30,'/',$_SERVER['HTTP_HOST']);
}else{
	//$param = array('parameter'=>'{"Tag":"PositionSite","Ip":""}');
	//$sitedata = httpPOST(REGION_API_PATH,$param);
	//$site = json_decode($sitedata,true);
	//默认让所有用户进入杭州站
	//if(empty($site)){
		$param = array('extparam'=>array('Tag'=>'GetSite','Domain'=>"0"));
		$site = httpPOST(REGION_API_PATH,$param);
		$site = $site['Result'];
		unset($site['id'],$site['province'],$site['city'],$site['area'],$site['weather_id'],$site['domain'],$site['pic_url'],$site['cat_pic_json'],$site['uptime'],$site['is_hot'],$site['status']);
		$sitedata = json_encode($site);
		@setcookie('SiteData',$sitedata,time()+86400*30,'/',$_SERVER['HTTP_HOST']);
		//header("Location:http://{$_SERVER['HTTP_HOST']}/city/hz");
	//}
	//setcookie('SiteData',$sitedata,time()+86400*30,'/',$_SERVER['HTTP_HOST']);
}

$site['rooms_load_url'] = PIC_API_PATH."/p/{$site['rooms_load_url']}/0/0.jpg";
