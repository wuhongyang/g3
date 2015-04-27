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

//左侧菜单
$param=array(
	'extparam'=>array('Tag'=>'GetJoinList','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10324,'ChildId'=>101,'Desc'=>'加入我们列表读取')
);
$list=request($param);
$joinList=(array)$list['List'];
$id=intval($_GET['id']);

//文章详情
$param=array(
	'extparam'=>array('Tag'=>'GetArticleInfo','GroupId'=>$groupId,'id'=>$id),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10324,'ChildId'=>101,'Uin'=>$user['Uin'],'Desc'=>'查询文章详情')
);
$articleInfo=request($param);
$articleInfo=$articleInfo['info'];
if($id<=0){
	$id=$articleInfo['id'];
}

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/';
$tmp_config['cache_dir'].=$themes.'/tpl/';

$back_url=base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']);
$template='join';
$moduleAction='join';
$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template('join/'.$template.'.html',$tpl);