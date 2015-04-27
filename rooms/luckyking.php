<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

//排行
$param = array(
	'extparam' => array('Tag'=>'GetLuckyRank'),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10239,'ChildId'=>102)
);
$rank = request($param);
$rank1 = array_slice((array)$rank['Result'],0,10,true);
$rank2 = array_slice((array)$rank['Result'],10,10,true);


$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template("active_game/index.html",$tpl);