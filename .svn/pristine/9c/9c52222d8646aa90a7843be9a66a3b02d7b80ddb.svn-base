<?php
header("cache-control:no-cache,must-revalidate");
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';
$GroupData = domain::main()->GroupData();
//验证用户是否登录
$user=checkLogin();
$groupId=(int)$GroupData['groupid'];
$groupName=$GroupData['name'];
$GroupData['EXT']=json_decode($GroupData['EXT'], true);
$themes=$GroupData['Template']!=''?$GroupData['Template']:'default';

//顶部分类（站后台配置）
$param=array(
	'extparam'=>array('Tag'=>'Categories','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取商品分类')
);
$category=request($param);
$category=$category['Category'];
//顶部分类（公司后台配置,目前只配置靓号是否显示）
$categories=(array)$GroupData['scheme_info'];
$category[]=array(
	'id'=>0,
	'cate_name'=>$categories['pack_name'],
	'cate_id'=>4
);
unset($categories);
$category_id=(isset($_GET['category_id']))?intval($_GET['category_id']):0;//分类ID

if($category){
	foreach($category as $val){
		if($category_id==$val['cate_id']){
			$categoryInfo=$val;
		}
	}
}
?>