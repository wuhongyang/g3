<?php
require_once '../library/global.fun.php';
$module = $_GET['module'];
$link_array = getLevellink(10002,10040,10165,101);
if($module == 'list'){
	$status = array('未处理','已处理');
	$where = array();
	if($_GET['status']> -1) $where['status'] = (int)$_GET['status'];
	if(!empty($_GET['start'])) $where['uptime']['$gte'] = (int)strtotime($_GET['start']);
	if(!empty($_GET['end'])) $where['uptime']['$lte'] = (int)strtotime($_GET['end']);
	$param = array(
		'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10165,'ChildId'=>101,'Desc'=>'用户反馈列表'),
		'extparam' => array('Tag'=>'FeedList','page'=>(int)$_GET['page'],'where'=>$where)
	);
	$result = request($param);
	$lists = (array)$result['Result'];
	$_GET['page'] = $result['prev'];
	$prev = http_build_query($_GET,'','&');
	$_GET['page'] = $result['next'];
	$next = http_build_query($_GET,'','&');
	$tpl = 'list.html';

}elseif($module == 'view'){
	$param = array(
		'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10165,'ChildId'=>102,'Desc'=>'查看用户反馈'),
		'extparam' => array('Tag'=>'View','id'=>$_GET['id'])
	);
	$result = request($param);
	$list = (array)$result['Result'];
	$list['content'] = str_replace("\r\n",'<br />',$list['content']);
	$tpl = 'view.html';

}elseif($module == 'dispose'){
	$param = array(
		'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10165,'ChildId'=>103,'Desc'=>'处理用户反馈'),
		'extparam' => array('Tag'=>'Dispose','id'=>$_GET['id'])
	);
	$result = request($param);
	alertMsg($result['FlagString']);
}

$template = template::getInstance();
$template->setOptions(get_config('template','admin'));
include template("feedback/{$tpl}",$template);
