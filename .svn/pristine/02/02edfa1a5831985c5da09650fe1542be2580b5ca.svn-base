<?php
require dirname(dirname(__FILE__)).'/library/global.fun.php';
// require 'site.inc.php';
define('THEMES_ROOT', $_SERVER['DOCUMENT_ROOT'].'/themes/g3');
$site['region_id'] = 0;

//图片广告
$advCycle = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'AdImg','RegionId'=>320500)));
$advCycle = $advCycle['Result'];
foreach($advCycle as $k=>$v){
	$advCycle[$k]['src'] = PIC_API_PATH.'/p/'.$v['src'].'/0/0.jpg';
	$advCycle[$k]['thumb_src'] = PIC_API_PATH.'/p/'.$v['thumb_src'].'/0/0.jpg';
}

$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));


//官方公告
$param = array(
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10161,'ChildId'=>102,'Desc'=>'官方公告'),
	'extparam' => array('Tag'=>'SubstanceList','Data'=>array('Type'=>3,'Limit'=>6))
);
$res = request($param);
$notice = $res['Data'];

//房间列表
$param = array(
	'extparam' => array('Tag'=>'GetActivityRooms','Gameid'=>0),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>102)
);
$rooms_list = request($param);

$rooms_list = $rooms_list['Result'];
$top2 = array_slice($rooms_list,0,2);
unset($rooms_list[0],$rooms_list[1]);

/*游戏活动分类列表*/
$interact_arr = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'InteractList','Data'=>array('region_id'=>320500,'interact_status'=>1))));
$interact_arr = $interact_arr['list'];


$banner = array('href'=>'/employ.html','src'=>'/static/images/banner2.jpg');
$template = 'games.html';

/*加载模板*/
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template($template,$tpl);
