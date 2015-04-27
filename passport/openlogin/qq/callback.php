<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/data/library/global.fun.php';
require_once("API/qqConnectAPI.php");
$qc = new QC();
$access_token = $qc->qq_callback();
$qq_openid = $qc->get_openid();
$qc = new QC($access_token,$qq_openid);
$userinfo = $qc->get_user_info();

if($userinfo['ret'] != 0 || empty($access_token) || empty($qq_openid)){
    exit('<script>alert("登录失败");location.href="/";</script>');
}
$gender = $userinfo['gender']=='男'? 1 : 2;
$query = array(
    'access_token' => $access_token,
    'openid' => $qq_openid,
    'nick' => rawurlencode($userinfo['nickname']),
    'gender' => $gender,
    'picurl' => urlencode($userinfo['figureurl_qq_2']),
    'platform'=>'qq'
);
if(isset($_COOKIE['redirect'])){
	$query['redirect'] = $_COOKIE['redirect'];
	setcookie('redirect','',0,'/',$_SERVER['HTTP_HOST']);
}
if(isset($_COOKIE['domain'])){
	$query['domain'] = $_COOKIE['domain'];
	setcookie('domain','',0,'/',$_SERVER['HTTP_HOST']);
}
if(isset($_COOKIE['dplogin'])){
	$query['dplogin'] = $_COOKIE['dplogin'];
	setcookie('dplogin','',0,'/',$_SERVER['HTTP_HOST']);
}
$query = http_build_query($query);
$back = isset($_COOKIE['back'])? $_COOKIE['back'] : $_SERVER['HTTP_HOST'];
$url = "http://{$back}/passport/login_callback.php?{$query}";
header("location:{$url}");
