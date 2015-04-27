<?php
require dirname(dirname(__FILE__)).'/library/global.fun.php';
//require 'site.inc.php';
define('THEMES_ROOT', $_SERVER['DOCUMENT_ROOT'].'/themes/g3');

//房间分类
//$rooms_case = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetRoomsCase','RegionId'=>$site['region_id'])));
//$rooms_case = $rooms_case['Result'];

$param = array(
	'extparam' => array('Tag'=>'GetHotGroups'),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
);
$rooms_case = (array)request($param);

$curTotalUser = $rooms_case['TotalUser'];
$rooms_case = $rooms_case['Result'];


//推荐位房间
$param = array(
	'extparam' => array('Tag'=>'GetRecommedRooms','RegionId'=>$site['region_id']),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
);
$recommend_rooms = request($param);
$recommend_rooms = (array)$recommend_rooms['HotRooms'];
$rankweek = (int)date('oW');
$rankmonth = (int)date('Ym');

//歌手人气排行
function getArtistRank($region_id,$uptime,$type='week',$rows=6){
	//分站显示10个
	if($region_id > 0){
		$rows = 10;
	}
	$param = array(
		'extparam' => array('Tag'=>'ArtistPopularity','RegionId'=>$region_id,'Type'=>$type,'Rows'=>$rows,'Uptime'=>$uptime),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
	);
	$hot_rank = (array)request($param);
	return $hot_rank;
}

$artistweek = getArtistRank($site['region_id'],$rankweek,'week');
$artistmonth = getArtistRank($site['region_id'],$rankmonth,'month');
$artisttotal = getArtistRank($site['region_id'],0,'total');

//房间人气排行
function getRoomRank($region_id,$uptime,$type='week',$rows=6){
	//分站显示10个
	if($region_id > 0){
		$rows = 10;
	}
	$param = array(
		'extparam' => array('Tag'=>'RoomPopularity','RegionId'=>$region_id,'Type'=>$type,'Uptime'=>$uptime,'Rows'=>$rows),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
	);
	$hot_rank = (array)request($param);
	return $hot_rank;
}

$room_rankweek = getRoomRank($site['region_id'],$rankweek,'week');
$room_rankmonth = getRoomRank($site['region_id'],$rankmonth,'month');
$room_ranktotal = getRoomRank($site['region_id'],0,'total');

//图片广告
$advCycle = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'AdImg','RegionId'=>$site['region_id'])));
$advCycle = $advCycle['Result'];
foreach((array)$advCycle as $k=>$v){
	$advCycle[$k]['src'] = cdn_url(PIC_API_PATH.'/p/'.$v['src'].'/0/0.jpg');
	$advCycle[$k]['thumb_src'] = cdn_url(PIC_API_PATH.'/p/'.$v['thumb_src'].'/0/0.jpg');
}
$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));

//友情链接
$flParam = array(
	'extparam' => array('Tag'=>'GetLinkList'),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10232,'ChildId'=>101)
);
$fl = request($flParam);
$friendLink = (array)$fl['Data'];

//地域站
if($site['region_id'] > 0){
	//帮助中心
	$param = array(
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10161,'ChildId'=>102,'Desc'=>'帮助中心'),
		'extparam' => array('Tag'=>'SubstanceList','Data'=>array('Type'=>1,'Limit'=>4))
	);
	$res = request($param);
	$help = $res['Data'];
	
	//热门房间
	$param = array(
		'extparam' => array('Tag'=>'GetHotRooms','RegionId'=>$site['region_id']),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
	);
	$hot_rooms = request($param);
	$rooms_list = (array)$hot_rooms['HotRooms'];

	$param = array(
		'extparam' => array('Tag'=>'ArtistPopularity','RegionId'=>$site['region_id'],'Rows'=>5,'IsNew'=>true),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
	);
	$new_artists = (array)request($param);
	if($new_artists['Flag'] == 100){
		$new_artist = $new_artists['Artist'];
		$new_artistInfo = $new_artists['ArtistInfo'];
		foreach($new_artist as $key => $val){
			$info = getArtistInfo($val['uin']);
			$new_artist[$key]['online'] = intval($info['is_online']);
			$new_artist[$key]['roomid'] = intval($info['room_id']);
		}
	}
	$template = 'index.html';

//导航站
}else{

	//推荐房间
	foreach($recommend_rooms as $key=>$val){
		$city_arr = httpPOST(REGION_API_PATH, array('extparam'=>array('Tag'=>'GetCityName','CityId'=>$val['city_id'])));
		$city_arr['cityName'] = rtrim($city_arr['cityName'],'市');
		$recommend_rooms[$key]['city_name'] = $city_arr['cityName'];
	}

	//分站列表
	/*
	foreach((array)$rooms_case['rooms_case'] as $r){
		$region2name[$r['region_id']] = $r['city_name'];
	}
	*/

	//富豪排行周
	$param = array(
		'extparam' => array('Tag'=>'ConsumeRank','Uptime'=>$rankweek,'Type'=>'week','Rows'=>6),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
	);
	$res = request($param);
	$moneyRankWeek = (array)$res['Result'];

	//富豪排行月
	$param = array(
		'extparam' => array('Tag'=>'ConsumeRank','Uptime'=>(int)date('Ym'),'Type'=>'month','Rows'=>6),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
	);
	$res = request($param);
	$moneyRankMonth = (array)$res['Result'];

	//富豪排行总
	$param = array(
		'extparam' => array('Tag'=>'ConsumeRank','Type'=>'total','Rows'=>6),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
	);
	$res = request($param);
	$moneyRankTotal = (array)$res['Result'];

	//热门艺人
	$param = array(
		'extparam' => array('Tag'=>'HotArtist'),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
	);
	$res = request($param);
	$hotArtists = (array)$res['HotArtist'];

	//官方公告
	$param = array(
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10161,'ChildId'=>102,'Desc'=>'新闻'),
		'extparam' => array('Tag'=>'SubstanceList','Data'=>array('ClassifyId'=>3,'Limit'=>5))
	);
	$res = request($param);
	$news = $res['Data'];
	$param = array(
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10161,'ChildId'=>102,'Desc'=>'公告'),
		'extparam' => array('Tag'=>'SubstanceList','Data'=>array('ClassifyId'=>4,'Limit'=>5))
	);
	$res = request($param);
	$notice = $res['Data'];
	$param = array(
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10161,'ChildId'=>102,'Desc'=>'活动'),
		'extparam' => array('Tag'=>'SubstanceList','Data'=>array('ClassifyId'=>5,'Limit'=>5))
	);
	$res = request($param);
	$activity = $res['Data'];
	$param = array(
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10161,'ChildId'=>102,'Desc'=>'帮助'),
		'extparam' => array('Tag'=>'SubstanceList','Data'=>array('Type'=>1,'Limit'=>5))
	);
	$res = request($param);
	$help = $res['Data'];
	$template = 'navsite.html';
}

$top2 = array($recommend_rooms[0],$recommend_rooms[1]);
unset($recommend_rooms[0],$recommend_rooms[1]);

/*游戏活动分类列表*/
$interact_arr = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'InteractList','Data'=>array('region_id'=>$site['region_id'],'interact_status'=>1))));
$interact_arr = $interact_arr['list'];

/*加载模板*/
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template($template,$tpl);
