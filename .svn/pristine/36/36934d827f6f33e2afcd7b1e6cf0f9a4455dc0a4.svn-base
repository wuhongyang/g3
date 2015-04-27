<?php
/*
include_once('../library/global.fun.php');

$module = trim($_GET['module']);

if($module=='list'){
	$link_array = getLevellink(10002,10016,10134,101);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10134,
			"ChildId"	 => 101,
			"Desc"		 => "渠道申请列表查询"
		),
		'extparam'=>array(
			"Tag" 		 => "JoinList",
			"Data" => $_GET
		)
	);
	if(empty($_GET['status'])){$_GET['status'] = -1;}
	$result = request($param);
	$joinList = (array)$result['List'];
	foreach ($joinList as $key => $value) {
		$citys = json_decode($value['permanent_city'],true);
		$pName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetProvinceName','ProvinceId'=>$citys['provinceId'])));
		$pName = $pName['provinceName'];
		$cName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCityName','CityId'=>$citys['cityId'])));
		$cName = $cName['cityName'];
		$joinList[$key]['city_name'] = $pName.' '.$cName;
	}
	$page = $result['Page'];
	$temp = 'join_list.html';
}elseif($module=='join_info'){
	$link_array = getLevellink(10002,10016,10134,102);
	$id = intval($_GET['id']);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10134,
			"ChildId"	 => 102,
			"Desc"		 => "查看申请资料"
		),
		'extparam'=>array(
			"Tag" 		 => "JoinInfo",
			"Data" => $id
		)
	);
	$joinuser = request($param);
	$info = $joinuser['Info'];
	$info['partner_name'] = ' 杭州奥点科技有限公司';
	$info['partner_id'] = ' 100';
	$citys = json_decode($info['city_json'],true);
	$pName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetProvinceName','ProvinceId'=>$citys['provinceId'])));
	$pName = $pName['provinceName'];
	$cName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCityName','CityId'=>$citys['cityId'])));
	$cName = $cName['cityName'];

	if($info['province'] > 0){
		$provinceName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetProvinceName','ProvinceId'=>$info['province'])));
		$provinceName = $provinceName['provinceName'];	
	}
	if($info['city'] > 0){
		$cityName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCityName','CityId'=>$info['city'])));
		$cityName = $cityName['cityName'];
	}
	if($info['area'] > 0){
		$areaName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetAreaName','AreaId'=>$info['area'])));
		$areaName = $areaName['areaName'];
	}

	$region_id = $info['area']>0 ? $info['area'] : $info['city'];
	
	//得到开通的站点
	$opens = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetOpenCity')));
	$openProvince = (array)$opens['province'];
	$openCity = (array)$opens['city'];
	$city_JSONDATA = json_encode($openCity);
	$openArea = (array)$opens['area'];
	$area_JSONDATA = json_encode($openArea);

	$temp = 'joinuser.html';
}elseif($module=='join_update'){
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10016,"ParentId"=>10134,"ChildId"=>103,"Desc"=>"站长审核"),
		'extparam'=>array("Tag"=>"JoinUpdate","Data"=>$_POST)
	);
	$joinupdate = request($param);
	showMsg($joinupdate['FlagString'],'?module=list');
}elseif($module=='add'){
	$link_array = getLevellink(10002,10016,10134,106);
	
	$p = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetAllProvince')));
	$p = (array)$p['Result'];
	unset($p[0]);
	foreach($p as $province){
		$provinces[$province['province_id']] = $province['province_name'];
	}
	
	//得到开通的站点
	$opens = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetOpenCity')));
	$openProvince = (array)$opens['province'];
	$openCity = (array)$opens['city'];
	$city_JSONDATA = json_encode($openCity);
	$openArea = (array)$opens['area'];
	$area_JSONDATA = json_encode($openArea);

	$temp = 'joinadd.html';
}elseif($module=='add_act'){
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10016,"ParentId"=>10134,"ChildId"=>106,"Desc"=>"站长添加"),
		'extparam'=>array("Tag"=>"JoinAdd","Data"=>$_POST)
	);
	$result = request($param);
	if($result['Flag']==100){
		showMsg($result['FlagString'],'?module=list');
	}
	else{
		showMsg($result['FlagString'],-1);
	}
}elseif($module=='checkRoomid'){
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10016,"ParentId"=>10134,"ChildId"=>103,"Desc"=>"室主艺人审核"),
		'extparam'=>array("Tag"=>"RoomInfo","Data"=>array('Roomid'=>$_POST['roomid']))
	);
	exit(json_encode(request($param)));
}elseif($module == 'getStationHead'){
	$region_id = intval($_GET['region_id']);
	$results = getChannelRoleInfo(7,0,$region_id);
	echo intval($results[0]['uid']);
	die();
}

$apply_status = array(-1=>'请选择',0=>'未审核',1=>'审核通过',2=>'审核返回');
$role_type = array(-1=>'请选择',1=>'站长',2=>'艺人',3=>'室主');

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('join/'.$temp,$tpl);
*/