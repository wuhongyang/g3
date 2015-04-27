<?php
include_once('../library/global.fun.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : '';

//状态
$status_arr = array(
	"0" => "不使用",
	"1" => "使用"
);
//尺寸
$size_arr = array(
	"0" => "小",
	"2" => "大"
);

if(!empty($id) && $id!=0){
	$link_array = getLevellink(10002,10069,10648,101);
	$param = array(
		'param' => array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10648,"ChildId"=>111,"Desc"=>"分站礼物信息"),
		'extparam' => array("Tag"=>"GamePropsList","Id"=>$id)
	);
	$result = request($param);
	//得到图片分类
	$cat = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'CatList','mypost'=>array('state'=>1))));
	$cat = json_encode($cat['lists']);
	//得到图片
	$pic = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1))));
	$pic = json_encode($pic['lists']);
	//地域信息
	$areaInfo = $result['region'];
	$areaInfo = json_encode($areaInfo);
	//分站礼物详情
	$info = $result['list'];
	$url = $link_array[111]['url'].'?module=props_update';
}elseif($module == 'getPic'){
	//$pic = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1,'id'=>$v['pic_id']))));
	//$v['img_path'] = $pic['lists'][0]['img_path'];
}else{
	//添加礼物
	$link_array = getLevellink(10002,10069,10648,101);
	$param = array(
		'param' => array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10648,"ChildId"=>111,"Desc"=>"分站礼物信息"),
		'extparam' => array("Tag"=>"GamePropsList")
	);
	$result = request($param);
	//得到图片分类
	$cat = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'CatList','mypost'=>array('state'=>1))));
	$cat = json_encode($cat['lists']);
	//得到图片
	$pic = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1))));
	$pic = json_encode($pic['lists']);
	//地域信息
	$areaInfo = $result['region'];
	$areaInfo = json_encode($areaInfo);
	$url = $link_array[111]['url'].'?module=props_add';
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('regions/game_props_info.html',$tpl);