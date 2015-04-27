<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';
$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));

//排行

$param = array(
	'extparam' => array('Tag'=>'GetRoomConsume','Unit'=>1,'Type'=>'week'),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10027,'ParentId'=>10074,'ChildId'=>111,"ChannelId"=>574000)
);
$rank = request($param);
$rank = $rank['Result'];
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template("active_game/room_act.html",$tpl);