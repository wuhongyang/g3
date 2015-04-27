<?php
/*六房间首页扩展*/

//站图片
$param=array(
	'extparam'=>array('Tag'=>'GetGroupImg','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点当前风格')
);
$imgList=request($param);
$imgList=$imgList['imgList'];

//在线人数
$param=array(
	'extparam'=>array('Tag'=>'GetGroupOnlineNum','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点在线人数')
);
$onlineNum=request($param);
$onlineNum=number_format($onlineNum['total']);

//自定义导航
$param=array(
	'extparam'=>array('Tag'=>'GetGroupMenu','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点首页左部导航')
);
$menuList=request($param);
$menuList=$menuList['menuList'];

//轮播图
$param=array(
	'extparam'=>array('Tag'=>'GetGroupCarousel','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点首页轮播图')
);
$carouselList=request($param);
$carouselList=$carouselList['carouselList'];

//推荐位
$param=array(
	'extparam'=>array('Tag'=>'GetGroupRecommend','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点推荐位')
);
$recommendCat=request($param);
$recommendCat=$recommendCat['recommendCat'];

$liveList=false;
$roomListJson=array();
$recommendSubJson=array();
foreach($recommendCat as $val){
	if($val['type']==1){
		foreach($val['child'] as $val2){
			$recommendSubJson[$val2['id']]=array('name'=>$val2['name']);
			foreach($val2['list'] as $val3){
				$roomListJson[$val3['id']]=array(
					'id'=>$val3['id'],
					'name'=>$val3['name'],
					'curuser'=>$val3['curuser'],
					'hasplay'=>$val3['hasplay'],
					'group'=>$val3['group']
				);
			}
		}
	}
	elseif($val['type']==2||$val['type']==4){
		foreach($val['child'] as $val2){
			$recommendSubJson[$val2['id']]=array('name'=>$val2['name']);
		}
	}
}
$roomListJson=json_encode($roomListJson);
$recommendSubJson=json_encode($recommendSubJson);
//直播墙
$param=array(
	'extparam'=>array('Tag'=>'GetGroupLivePhoto','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点艺人直播墙')
);
$liveList=request($param);
$liveList=$liveList['liveList'];
//所有艺人
$data=array(
	'groupId'=>$groupId,
	'limit'=>'all'
);
$param=array(
	'extparam'=>array('Tag'=>'GetGroupArtistList','Data'=>$data),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'获取站下艺人')
);
$artistList=request($param);
$artistAll=$artistList['total'];
$artistList=$artistList['artistList'];

$artistListJson=array();
foreach($artistList as $val){
	$artistListJson[$val['uin']]=$val;
}
$artistListJson=json_encode($artistListJson);

//滚动消息
$param=array(
	'extparam'=>array('Tag'=>'GetGroupMessage','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点动态信息')
);
$messageList=request($param);

//排行榜
$param=array(
	'extparam'=>array('Tag'=>'GetGroupRank','GroupId'=>$groupId,'Type'=>1,'Row'=>5),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点首页排行榜设置')
);
$rankList=request($param);
$rankList=$rankList['rankList'];
?>