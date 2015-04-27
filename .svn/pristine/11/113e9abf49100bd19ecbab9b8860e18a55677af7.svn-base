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
$recommendSubJson=array();
foreach($recommendCat as $val){
	if(in_array($val['type'],array(1,2,4))){
		foreach($val['child'] as $val2){
			$recommendSubJson[$val2['id']]=array('name'=>$val2['name']);
		}
	}
}
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
foreach($artistList as $key=>$val){	
	if($groupExtInfo["artistRankRuleId"]["value"]){
		$roleData=array(
			'extparam'=>array(
				'Tag'=>'GetRole',
				'GroupId'=>$groupId,
				'Uin'=>$val['uin'],
				'Ruleid'=>$groupExtInfo["artistRankRuleId"]["value"]
			)
		);
		$roleInfo=httpPOST(ROLE_API_PATH,$roleData);
		$artistList[$key]['artistRankImg']=$roleInfo['Roles'][0]['role_small_icon'];
		$artistList[$key]['artistRankName']=$roleInfo['Roles'][0]['name'];
	}
	$artistListJson[$val['uin']]=$artistList[$key];
}
$artistListJson=json_encode($artistListJson);

//所有房间
$data=array(
	'groupId'=>$groupId,
	'limit'=>'all'
);
$param=array(
	'extparam'=>array('Tag'=>'GetGroupRoomList','Data'=>$data),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'获取站下艺人')
);
$roomList=request($param);
$roomListAll=$roomList['total'];
$roomList=$roomList['roomList'];

$roomListJson=array();
foreach($roomList as $val){
	$roomListJson[$val['id']]=$val;
}
$roomListJson=json_encode($roomListJson);

//滚动消息
$param=array(
	'extparam'=>array('Tag'=>'GetGroupMessage','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点动态信息')
);
$messageList=request($param);

//排行榜
$param=array(
	'extparam'=>array('Tag'=>'GetGroupRank','GroupId'=>$groupId,'Type'=>1,'Row'=>5,'RoleImg'=>1),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点首页排行榜设置')
);
$rankList=request($param);
$rankList=$rankList['rankList'];/*
foreach($rankList as $key=>$val){
	foreach($val as $key2=>$val2){
		if(in_array($key2,array('week','month','total','last_week'))){
			foreach($val[$key2] as $key3=>$val3){
				if($val3['SortType']==1){
					$param=array(
						'extparam'=>array('Tag'=>'GetScoreDiff','UinId'=>$val3['UinId'],'ExtendUin'=>$groupId,'Ruleid'=>$val3['Ruleid'],"Period"=>"total"),
						'param'=>array('BigCaseId'=>10001,'CaseId'=>10027,'ParentId'=>10074,'ChildId'=>118)
					);
					$result=request($param);
					if(!empty($result['RolesImg'])){
						$rankList[$key][$key2][$key3]['RolesImg']=$result['RolesImg'];
						$rankList[$key][$key2][$key3]['RolesName']=$result['RolesName'];
					}
				}
			}
		}
	}
}*/
?>