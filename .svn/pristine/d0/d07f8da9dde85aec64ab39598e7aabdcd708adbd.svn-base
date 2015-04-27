<?php
require_once '../library/global.fun.php';
//require_once dirname(__FILE__).'/site.inc.php';

$user_login = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
$rankweek = (int)date('oW');
$rankupweek = (int)date('oW',strtotime('-1 week'));
$rankmonth = (int)date('Ym');

//歌手人气排行
function getArtistRank($region_id,$uptime,$type='week',$rows=10){
	$param = array(
		'extparam' => array('Tag'=>'ArtistPopularity','RegionId'=>$region_id,'Type'=>$type,'Rows'=>$rows,'Uptime'=>$uptime),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
	);
	$hot_rank = (array)request($param);
	return $hot_rank;
}

$artistweek = getArtistRank($site['region_id'],$rankweek,'week');
$artistupweek = getArtistRank($site['region_id'],$rankupweek,'week');
$artistmonth = getArtistRank($site['region_id'],$rankmonth,'month');
$artisttotal = getArtistRank($site['region_id'],0,'total');

//房间人气排行
function getRoomRank($region_id,$uptime,$type='week',$rows=10){
	$param = array(
		'extparam' => array('Tag'=>'RoomPopularity','RegionId'=>$region_id,'Type'=>$type,'Uptime'=>$uptime,'Rows'=>$rows),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
	);
	$hot_rank = (array)request($param);
	return $hot_rank;
}

$roomweek = getRoomRank($site['region_id'],$rankweek,'week');
$roomupweek = getRoomRank($site['region_id'],$rankupweek,'week');
$roommonth = getRoomRank($site['region_id'],$rankmonth,'month');
$roomtotal = getRoomRank($site['region_id'],0,'total');

if(empty($site['region_id'])){
	//富豪排行周榜
	$param = array(
		'extparam' => array('Tag'=>'ConsumeRank','Uptime'=>$rankweek,'Type'=>'week','Rows'=>20),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
	);
	$res = request($param);
	$moneyRankCurmonth10 = array_slice((array)$res['Result'],0,10,true);
	$moneyRankCurmonth20 = array_slice((array)$res['Result'],10,10,true);
	
	//富豪排行月榜
	$param = array(
		'extparam' => array('Tag'=>'ConsumeRank','Uptime'=>$rankmonth,'Type'=>'month','Rows'=>20),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
	);
	$res = request($param);
	$moneyRankUpmonth10 = array_slice((array)$res['Result'],0,10,true);
	$moneyRankUpmonth20 = array_slice((array)$res['Result'],10,10,true);
	
	//富豪排行总榜
	$param = array(
		'extparam' => array('Tag'=>'ConsumeRank','Type'=>'total','Rows'=>20),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
	);
	$res = request($param);
	$moneyRankUpweek10 = array_slice((array)$res['Result'],0,10,true);
	$moneyRankUpweek20 = array_slice((array)$res['Result'],10,10,true);
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template('top.html',$tpl);
