<?php
include_once('library/common.php');

$type=$_GET['type'];
$id=intval($_GET['id']);
if(!in_array($type,array('help','about_us','notice'))&&$id<=0){
	header('Location:/404.html');
	exit;
}

//顶部导航
$param=array(
	'extparam'=>array('Tag'=>'GetGroupNavigate','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Uin'=>$user['Uin'],'Desc'=>'查询站点顶部导航')
);
$navigateList=request($param);
$navigateList=$navigateList['navigateList'];

//文章内容
$param=array(
	'extparam'=>array('Tag'=>'GetNoticeInfo','GroupId'=>$groupId,'Type'=>$type,'Id'=>$id),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10576,'ChildId'=>101,'Uin'=>$user['Uin'],'Desc'=>'查询文章内容')
);
$notice=request($param);
if($notice['Flag']!=100||empty($notice['info'])||$notice['info']['group_id']!=$groupId){
	header('Location:/404.html');
	exit;
}
$notice=$notice['info'];

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/';
$tmp_config['cache_dir'].=$themes.'/tpl/';

$back_url=base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']);
$template='notice';
$moduleAction='notice';
$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template('notice/'.$template.'.html',$tpl);