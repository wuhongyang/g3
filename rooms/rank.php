<?php
require_once '../library/global.fun.php';
// require_once dirname(__FILE__).'/site.inc.php';

// if(isset($_GET['region_id'])){
	// $site['region_id'] = intval($_GET['region_id']);
	// //得到城市名称
	// $r = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetNameByRegion','RegionId'=>$site['region_id'])));
	// $site['city_name'] = $r['SiteName'];
// }

$user_login = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));

$module_type = array(
	'send'  => array('tag'=>'GetMoneyRank','class'=>'top3','title'=>'送礼排行','unitname'=>'积分(万)','link'=>'/service/home.php?user=','rate'=>10000),
	'receive'   => array('tag'=>'GetMoneyRank','class'=>'top3','title'=>'收礼排行','unitname'=>'积分(万)','link'=>'/service/home.php?user=','rate'=>10000),
	'Room'  => array('tag'=>'RoomPopularity','class'=>'room','title'=>'房间人气排行','unitname'=>'人气值','link'=>'/v/','rate'=>1),
	'Artist' => array('tag'=>'ArtistPopularity','class'=>'singer','title'=>'歌手人气排行','unitname'=>'人气值','link'=>'/service/home.php?user=','rate'=>1),
);

$module_type = $module_type[$_GET['module']];
$tag = $module_type['tag'];
if(empty($tag)) exit('404');

$date = intval($_GET['date']);
$type = $_GET['type']=='month'? 'month' : 'week';

$rows = 100;

$param = array(
	'extparam' => array('Tag'=>$tag,'RegionId'=>$site['region_id'],'Uptime'=>$date,'Type'=>$type,'Rows'=>$rows),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>102)
);
$result = (array)request($param);
$info = $result[$_GET['module'].'Info'];
$result = (array)$result[$_GET['module']];
if($_GET['module'] == 'Artist'){
	foreach($result as $key => $val){
		$aInfo = getArtistInfo($val['uin']);
		$result[$key]['online'] = intval($aInfo['is_online']);
		$result[$key]['roomid'] = intval($aInfo['room_id']);
	}
}

$top_10 = array_slice($result,0,10,true);
$top_ranks = array(
	'TOP11-20'  => array_slice($result,10,10,true),
	'TOP21-30'  => array_slice($result,20,10,true),
	'TOP31-40'  => array_slice($result,30,10,true),
	'TOP41-50'  => array_slice($result,40,10,true),
	'TOP51-60'  => array_slice($result,50,10,true),
	'TOP61-70'  => array_slice($result,60,10,true),
	'TOP71-80'  => array_slice($result,70,10,true),
	'TOP81-90'  => array_slice($result,80,10,true),
	'TOP91-100' => array_slice($result,90,10,true),
);

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
if($_GET['module'] == 'Artist'){
	include template('artist_rank.html',$tpl);
}else{
	include template('rank.html',$tpl);
}
