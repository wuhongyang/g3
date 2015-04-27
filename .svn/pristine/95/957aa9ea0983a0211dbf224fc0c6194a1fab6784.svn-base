<?php
include_once('../library/global.fun.php');
include 'config/data.php';

$module = trim($_GET['module']);
//获得url
$link_array = getLevellink(10002,10016,10027,104);

$banks = array(1=>'中国招商银行',2=>'中国工商银行',3=>'中国建设银行',4=>'中国农业银行');

//当前使用站
$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);

if($module=='infoList'){
	$operate = $_GET['type'];
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10027,
			"ChildId"	 => 104,
			"Desc"		 => "渠道商详情"
		),
		'extparam'=>array(
			"Tag" 	 => "ChannelInfoList",
			"Data"	 => $_GET,
			"GroupId"=> $__ADMIN_CURGROUP['groupid']
		)
	);
	$result = request($param);
	$info = $result['info'];
	$bankInfo = $info['bankInfo'];
	
	$channl_categories = $result['channl_categories'];
	$cates = array();
	foreach($channl_categories as $val){
		$cates[$val['id']] = $val['name'];
	}

	$otherinfo = json_decode($info['other_info'],true);
	$provinceId = intval($otherinfo['province']);
	if($provinceId > -1){
		$pName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetProvinceName','ProvinceId'=>$provinceId)));
		$pName = $pName['provinceName'];
	}
	$cityId = intval($otherinfo['city']);
	if($cityId > -1){
		$cName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCityName','CityId'=>$cityId)));
		$cName = $cName['cityName'];
	}
	$areaId = intval($otherinfo['area']);
	if($areaId > -1){
		$aName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetAreaName','AreaId'=>$areaId)));
		$aName = $aName['areaName'];
	}
	unset($result);
	$regions = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'getOPenCity')));
	$province = (array)$regions['province'];
	$province_JSONDATA = json_encode($province);
	$city = (array)$regions['city'];
	$city_JSONDATA = json_encode($city);
	$area = (array)$regions['area'];
	$area_JSONDATA = json_encode($area);

	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','admin'));
	include template('partner/channel_info.html',$tpl);
}elseif($module == 'channelAdd'){
	$other = (array)$_POST['other'];
	$info = (array)$_POST['info'];
	$info['region_id'] = ($other['area'] > -1)? $other['area'] : $other['city'];
	if($info['region_id'] < 0) showMsg('请选择所属站点');
	if(!empty($_FILES['idcard_a']['tmp_name'])){
		$bytes = file_get_contents($_FILES['idcard_a']['tmp_name']);
		$index = md5($bytes);
		$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
		$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
		if($query['rst'] == 100){
			$other['idcard_a'] = $index;
		}
	}
	if(!empty($_FILES['idcard_b']['tmp_name'])){
		$bytes = file_get_contents($_FILES['idcard_b']['tmp_name']);
		$index = md5($bytes);
		$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
		$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
		if($query['rst'] == 100){
			$other['idcard_b'] = $index;
		}
	}
	$info['other_info'] = mysql_escape_string(json_encode($other));
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10027,
			"ChildId"	 => 106,
			"Desc"		 => "添加渠道商"
		),
		'extparam'=>array(
			"Tag" 	 => "ChannelAdd",
			"Data"	 => $info,
			"GroupId"=> $__ADMIN_CURGROUP['groupid']
		)
	);
	
	$result = request($param);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[104]['url']);
	}else{
		ShowMsg($result['FlagString'],$link_array[106]['url']);
	}
}elseif($module == 'channelUpdate'){
	$id = intval($_POST['id']);
	$other = (array)$_POST['other'];
	$info = (array)$_POST['info'];
	$info['region_id'] = ($other['area'] > -1)? $other['area'] : $other['city'];
	if($info['region_id'] < 0) showMsg('请选择所属站点');

	$info = array_merge($info,$other);
	$info['other_info'] = mysql_escape_string(json_encode($other));
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10027,
			"ChildId"	 => 111,
			"Uin"		 => "",
			"Desc"		 => "渠道商修改"
		),
		'extparam'=>array(
			"Tag" 	 => "ChannelUpdate",
			"Data"	 => $info,
			"Id" 	 => $id,
			"GroupId"=> $__ADMIN_CURGROUP['groupid']
		)
	);
	
	$result = request($param);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[104]['url']);
	}else{
		ShowMsg($result['FlagString'],$link_array[111]['url'].'&id='.$_POST['id']);
	}
}elseif($module == 'getUserBankInfo'){
	$uin = intval($_GET['uin']);
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10017,"ParentId"=>10028,"ChildId"=>101,"Desc"=>"获取用户信息"),
		'extparam'=>array("Tag"=>"GetUserBankInfo","Uin"=>$uin)
	);
	$rst = request($param);
	$rst['Bank'] = $banks;
	echo json_encode((array)json_encode($rst));
	exit;
}