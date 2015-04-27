<?php
header('Content-Type:text/html;charset=utf-8');
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$login_user = checkLogin('/');

$site = json_decode($_COOKIE['SiteData'],true);
$region_id = $site['region_id']>0 ? intval($site['region_id']) : 0;

//$regionId = intval($_POST['regionId']);
$uin = intval($_POST['uin']);
$group_id = intval($_GET['GroupId']);

if($_GET['module'] == 'getHistoryAccess'){
	$param = array(
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10157,'ChildId'=>101),
		'extparam' => array('Tag'=>'GetHistoryAccess','Uin'=>$login_user['Uin'],'GroupId'=>$group_id)
	);
	$result = request($param);
	$footPrint = (array)$result['HistoryAccess'];
	$footPrint = array_slice($footPrint, 0, 3);
	exit(json_encode($footPrint));
	//$k = 'HistoryAccess';
	//$title2 = '历史访问';
	
}elseif($_GET['module'] == 'getMyFavorite'){
	$param = array(
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10157,'ChildId'=>102),
		'extparam' => array('Tag'=>'GetMyFavorite','Uin'=>$login_user['Uin'],'GroupId'=>$group_id)
	);
	$result = request($param);
	$k = 'MyFavorite';
	
	$title2 = '我的收藏';
}
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template('common.html',$tpl);
