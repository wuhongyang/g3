<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';
$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));

//斗牛排行
$param = array(
	'extparam' => array('Tag'=>'GetList','Data'=>array('ParentId'=>10114,'RoomId'=>0)),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10239,'ChildId'=>101)
);
$niuniurank = request($param);
$niuniurank = (array)$niuniurank['Result'];

//扎金花排行
$param = array(
	'extparam' => array('Tag'=>'GetList','Data'=>array('ParentId'=>10110,'RoomId'=>0)),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10239,'ChildId'=>101)
);
$zjhrank = request($param);
$zjhrank1 = array_slice((array)$zjhrank['Result'],0,10,true);
$zjhrank2 = array_slice((array)$zjhrank['Result'],10,10,true);
$zjhrank3 = array_slice((array)$zjhrank['Result'],20,10,true);

//斗地主排行
$param = array(
	'extparam' => array('Tag'=>'GetList','Data'=>array('ParentId'=>10109,'RoomId'=>0)),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10239,'ChildId'=>101)
);
$ddzrank = request($param);
$ddzrank1 = array_slice((array)$ddzrank['Result'],0,10,true);
$ddzrank2 = array_slice((array)$ddzrank['Result'],10,10,true);
$ddzrank3 = array_slice((array)$ddzrank['Result'],20,10,true);

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template("active_game/activity.html",$tpl);