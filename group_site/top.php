<?php
include_once('library/common.php');

//站风格
$param=array(
	'extparam'=>array('Tag'=>'GetGroupStyle','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Uin'=>$user['Uin'],'Desc'=>'查询站点当前风格')
);
$styleInfo=request($param);
$styleInfo=$styleInfo['StyleInfo'];

//顶部导航
$param=array(
	'extparam'=>array('Tag'=>'GetGroupNavigate','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Uin'=>$user['Uin'],'Desc'=>'查询站点顶部导航')
);
$navigateList=request($param);
$navigateList=$navigateList['navigateList'];

//排行榜
$param=array(
	'extparam'=>array('Tag'=>'GetGroupRank','GroupId'=>$groupId,'Type'=>2,'Row'=>10,'RoleImg'=>1),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10322,'ChildId'=>101,'Uin'=>$user['Uin'],'Desc'=>'查询站点首页排行榜设置')
);
$rankList=request($param);
$rankList=$rankList['rankList'];

//载入各站扩展
$library='library/'.$themes.'/'.basename(__FILE__);
if(file_exists($library)){
	include_once($library);
}

function str_cut_out($str){
	if(mb_strlen($str,'UTF-8')>7){
		$str=mb_substr($str,0,7,'UTF-8').'...';
	}
	return $str;
}

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/';
$tmp_config['cache_dir'].=$themes.'/tpl/';

$back_url=base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']);
$template='top';
$moduleAction='top';
$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template('top/'.$template.'.html',$tpl);