<?php
/*爱尚网大厅*/
$callback=$groupExtInfo['callback']['value'];
if(empty($callback)){
	$callback="openlogin.vvku.com";
}
if($groupId<=0){
	header("Location:/");
}
//是否意向客户
$is_intention = $_GET['is_intention'];
//是否引导页
$is_guide = $_GET['is_guide'];

//站详情
$param=array(
   'extparam'=>array('Tag'=>'GetGroupInfo','Id'=>$groupId),
   'param'=>array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101,'Desc'=>'获取站信息')
);
$groupInfo=request($param);
if($groupInfo['Flag']!=100){
   alertMsg($groupInfo['FlagString'],'/');
}
$groupInfo=$groupInfo['Result'];
if(empty($groupInfo)){
   header('Location:/404.html');
   exit;
}

//站配置
$param=array(
	'extparam'=>array('Tag'=>'GetGroupSetting','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点配置')
);
$setting=request($param);
$setting=$setting['setting'];

//站图片
$param=array(
	'extparam'=>array('Tag'=>'GetGroupImg','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点当前风格')
);
$imgList=request($param);
$imgList=$imgList['imgList'];

//体验页
if($is_guide && $user['Flag'] == 100){
	//引导页角色列表
	$param = array(
		'param' => array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10642,'ChildId'=>101),
		'extparam' => array('Tag'=>'GetPractice', 'GroupId'=>$groupId)
	);
	$rst = request($param);
	$all_role = $role = $rst['Data'];
	$role_pos = $_GET['role_pos'];
	$role_name = urldecode($_GET['role_name']);
	$account_pos = $_GET['account_pos'];
	foreach($role as $key=>$val){
		if($val['role_name'] == $role_name){
			$current_role = $val;
		}else{
			$rst_role[] = $val;
		}
	}
}
		
//在线人数
$param=array(
	'extparam'=>array('Tag'=>'GetGroupOnlineNum','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点在线人数')
);
$onlineNum=request($param);
$onlineNum=number_format($onlineNum['total']);

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
		}
	}
}
$roomListJson=json_encode($roomListJson);
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

function str_cut_out($str,$num=7){
	if(mb_strlen($str,'UTF-8')>($num+1)){
		$str=mb_substr($str,0,$num,'UTF-8').'...';
	}
	return $str;
}

$client=empty($_GET['client'])?'tpl':'tpl_'.$_GET['client'];

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/'.$client.'/';
$tmp_config['cache_dir'].=$themes.'/'.$client.'/';

$back_url=base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']);
$moduleAction='index';
$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template('index/home.html',$tpl);
pagecache::main()->end();

?>