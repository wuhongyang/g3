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
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点顶部导航')
);
$navigateList=request($param);
$navigateList=$navigateList['navigateList'];

//友情链接分类
$param=array(
	'extparam'=>array('Tag'=>'GetLinkCateList','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10622,'ChildId'=>102,'Desc'=>'查询友情链接分类')
);
$cateList=request($param);
$cateList=$cateList['List'];
if($cateList){
	foreach($cateList as $key=>$val){
		//友情链接列表
		$param=array(
			'extparam'=>array('Tag'=>'GetLinkList','GroupId'=>$groupId,'CateId'=>$val['id']),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10622,'ChildId'=>101,'Desc'=>'查询友情链接列表')
		);
		$linkList=request($param);
		$cateList[$key]['link']=$linkList['List'];
	}
}

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/';
$tmp_config['cache_dir'].=$themes.'/tpl/';

$back_url=base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']);
$template='index';
$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template('links/'.$template.'.html',$tpl);