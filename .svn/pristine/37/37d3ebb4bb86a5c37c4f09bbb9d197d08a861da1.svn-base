<?php
/*默认模板首页扩展*/
		
//站风格
$param=array(
	'extparam'=>array('Tag'=>'GetGroupStyle','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点当前风格')
);
$styleInfo=request($param);
$styleInfo=$styleInfo['StyleInfo'];

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

//站搜索配置
$param=array(
	'extparam'=>array('Tag'=>'GetGroupSearchConfig','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点搜索配置')
);
$searchConfig=request($param);
$searchConfig=$searchConfig['info'];
if($searchConfig['vip_search']==1){
	//年龄
	$age=array();
	for($i=1;$i<=100;$i++){
		$age[]=$i;
	}
	
	//省
	$p=httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetAllProvince')));
	$p=(array)$p['Result'];
	unset($p[0]);
	foreach($p as $province){
		$provinces[$province['province_id']]=$province['province_name'];
	}
}

//房间分类
$param=array(
	'extparam'=>array('Tag'=>'GetSortList','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10319,'ChildId'=>102,'Desc'=>'获取站内房间分类')
);
$catList=request($param);
$catList=(array)$catList['List'];
$catListJson=array();
foreach($catList as $key=>$val){
	$catListJson[$val['id']]=$val;
}
$catListJson=json_encode($catListJson);

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

if($_GET['keywords']!=''){
	$searchData=array(
		'groupId'=>$groupId,
		'keywords'=>$_GET['keywords'],
		'limit'=>$_GET['limit']
	);
	
	//房间
	$param=array(
		'extparam'=>array('Tag'=>'GetGroupRoomList','Data'=>$searchData),
		'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'获取站下房间')
	);
	$roomList=request($param);
	$roomList['page']=str_replace('条记录','个房间',$roomList['page']);
	
	//艺人
	$param=array(
		'extparam'=>array('Tag'=>'GetGroupArtistList','Data'=>$searchData),
		'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'获取站下艺人')
	);
	$artistList=request($param);
	$artistList['page']=str_replace('条记录','个艺人',$artistList['page']);
}
else{		
	//推荐位
	$param=array(
		'extparam'=>array('Tag'=>'GetGroupRecommend','GroupId'=>$groupId),
		'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点推荐位')
	);
	$recommendCat=request($param);
	$recommendCat=$recommendCat['recommendCat'];
	
	$liveList=false;
	$vipListJson=array();
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
				//如果设置了艺人直播墙
				if($val2['is_live']==1){
					$liveList=true;
				}
				foreach($val2['list'] as $val3){
					$vipListJson[$val3['uin']]=$val3;
				}
			}
		}
	}
	$roomListJson=json_encode($roomListJson);
	$vipListJson=json_encode($vipListJson);
	$recommendSubJson=json_encode($recommendSubJson);
	if($liveList){
		//直播墙
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupLivePhoto','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点艺人直播墙')
		);
		$liveList=request($param);
		$liveList=$liveList['liveList'];
	}
}

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

//在麦会员
$param=array(
	'extparam'=>array('Tag'=>'GetGroupVipOnline','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站内在线会员')
);
$vipList=request($param);
$vipTitle=$vipList['title'];
$vipList=$vipList['vipList'];
if(!empty($vipList)){
	$vipList=json_encode($vipList);
}
else{
	$vipList='';
}

?>