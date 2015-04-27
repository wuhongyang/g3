<?php
exit;
require '../library/global.fun.php';
//require 'site.inc.php';
//当前站点
/*
$param = array(
	'extparam' => array('Tag'=>'GetSiteData','City'=>$_GET['city']),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>102)
);
$site = request($param);
*/

//热门城市
/*
$param = array(
	'extparam' => array('Tag'=>'GetHotSites'),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>102)
);
$hot_citys = request($param);*/
$hot_citys = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetHotSites')));
$hot_citys = $hot_citys['Result'];

//开通站点
/*
$param = array(
	'extparam' => array('Tag'=>'GetOpenSites'),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>102)
);
$citys = json_encode(request($param));
*/
//$citys = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetOpenCity')),false);

//站点列表
/*
$param = array(
	'extparam' => array('Tag'=>'GetSiteList'),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>102)
);*/
$sites = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetSiteList')));
$sites = $sites['Result'];

//用户信息
/*
$param = array(
	'extparam' => array('Tag'=>'GetLoginUser'),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
);
$user = request($param);
*/
//$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template('allcity.html',$tpl);