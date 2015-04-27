<?php
/*tt1758首页扩展*/

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
	'extparam'=>array('Tag'=>'GetGroupRecommend','GroupId'=>$groupId,'IsArtistDetail'=>true,'RuleId'=>$groupExtInfo["artistRankRuleId"]["value"]),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点推荐位')
);
$recommend=request($param);
$recommend=$recommend['recommendCat'];
$recommendCat=array();
foreach($recommend as $val){
	if($val['type']==4){
		$recommendCat[]=$val;
	}
}
unset($recommend);

//排行榜
$param=array(
	'extparam'=>array('Tag'=>'GetGroupRank','GroupId'=>$groupId,'Type'=>1,'Row'=>5,'RoleImg'=>1),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点首页排行榜设置')
);
$rankList=request($param);
$rankList=$rankList['rankList'];
?>