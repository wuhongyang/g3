<?php
include_once('library/common.php');

if($themes != 'cc51'){
	header("HTTP/1.1 404 NOT FOUND");
	header("status:404 NOT FOUND");
	header("Content-Type:text/html;charset=utf-8");
	exit('<h1>文件不存在！</h1>');
}

//顶部导航
$param=array(
	'extparam'=>array('Tag'=>'GetGroupNavigate','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Uin'=>$user['Uin'],'Desc'=>'查询站点顶部导航')
);
$navigateList=request($param);
$navigateList=$navigateList['navigateList'];

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/';
$tmp_config['cache_dir'].=$themes.'/tpl/';

$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template('vclient.html',$tpl);
?>