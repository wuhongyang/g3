<?php
require_once '../library/global.fun.php';
$param = array(
	'extparam'=>array('Tag'=>'listGroupUsed'),
	'param'=>array('BigCaseId'=>10002,'CaseId'=>10003,'ParentId'=>10002,'ChildId'=>101,'Desc'=>'群列表')
);
$group_list = request($param);

$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);

$template = template::getInstance();
$template->setOptions(get_config('template','admin'));

include template('group_cutover/group_list.html', $template);