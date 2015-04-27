<?php
include_once('library/common.php');

//站风格
$param=array(
	'extparam'=>array('Tag'=>'GetGroupStyle','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点当前风格')
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

//会员搜索
$param=array(
	'extparam'=>array('Tag'=>'search','GroupId'=>$groupId,'Data'=>$_GET),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10400,'ChildId'=>101,'Desc'=>'根据条件查询站内会员')
);
$vipList=request($param);

//年龄
$age=array();
for($i=1;$i<=100;$i++){
	$age[]=$i;
}

//省
$p=httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetAllProvince')));
$p=(array)$p['Result'];
unset($p[0]);
foreach($p as $province){
	$provinces[$province['province_id']]=$province['province_name'];
}

function str_cut_out($str){
	if(mb_strlen($str,'UTF-8')>8){
		$str=mb_substr($str,0,7,'UTF-8');
	}
	return $str;
}

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/';
$tmp_config['cache_dir'].=$themes.'/tpl/';

$back_url=base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']);
$template='member_search';
$moduleAction='search';
$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template('search/'.$template.'.html',$tpl);
?>