<?php
include_once('../library/global.fun.php');
include 'config/data.php';

$module = trim($_GET['module']);

//当前使用站
$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);

if($module=='partnerList'){
	$link_array = getLevellink(10002,10016,10027,101);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10027,
			"ChildId"	 => 101,
			"Desc"		 => "合作商列表"
		),
		'extparam'=>array(
			"Tag" 		 => "PartnerList",
			"SearchData" => $_GET,
			"GroupId"=> $__ADMIN_CURGROUP['groupid']
		)
	);
	$result = request($param);
	$areaInfo = json_encode($result['region']);
	$partnerList = (array)$result['li'];
	$page = $result['page'];
	unset($result);
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','admin'));
	include template('partner/partner_list.html',$tpl);
}elseif($module == 'channelList'){
	$link_array = getLevellink(10002,10016,10027,104);
	
	$_GET['up_uid'] = $__ADMIN_CURGROUP['groupid'];
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10027,
			"ChildId"	 => 104,
			"Desc"		 => "渠道商列表"
		),
		'extparam'=>array(
			"Tag" 		 => "ChannelList",
			"SearchData" => $_GET,
			"GroupId"=> $__ADMIN_CURGROUP['groupid']
		)
	);
	$result = request($param);
	$page = $result['page'];
	$channelList = $result['li'];
	$partners = $result['partners'];
	foreach($result['channelCategory'] as $v){
		$channelCategory[$v['id']] = $v['name'];
	}
	unset($result);
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','admin'));
	include template('partner/channel_list.html',$tpl);
}elseif($module == 'showChannel'){
	$link_array = getLevellink(10002,10016,10027,107);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10027,
			"ChildId"	 => 107,
			"Desc"		 => "查看渠道商"
		),
		'extparam'=>array(
			"Tag" 		 => "ChannelsInPartner",
			"PartnerId"  => $_GET['id'],
			"GroupId"=> $__ADMIN_CURGROUP['groupid']
		)
	);
	$result = request($param);
	$page = $result['page'];
	$channelList = $result['li'];
	foreach($result['channelCategory'] as $c){
		$channelCategory[$c['id']] = $c['name'];
	}
	unset($result);
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','admin'));
	include template('partner/channel_list.html',$tpl);
}elseif($module == 'setSalaryAndReward'){
	$link_array = getLevellink(10002,10016,10027,107);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10027,
			"ChildId"	 => 107,
			"Desc"		 => "设置工资与奖励"
		),
		'extparam'=>array(
			"Tag" 		 => "SetSalaryAndReward",
			"Id"		 => $_GET['id'],
			"GroupId"=> $__ADMIN_CURGROUP['groupid']
		)
	);
	$info = request($param);
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','admin'));
	include template('partner/set_salary_reward_info.html',$tpl);
}elseif($module == 'saveSalaryAndReward'){
	$link_array = getLevellink(10002,10016,10027,107);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10027,
			"ChildId"	 => 109,
			"Desc"		 => "保存工资与奖励"
		),
		'extparam'=>array(
			"Tag" 		 => "SaveSalaryAndReward",
			"Data"		 => $_POST['SAR'],
			"GroupId"=> $__ADMIN_CURGROUP['groupid']
		)
	);
	$result = request($param);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[104]['url']);
	}else{
		ShowMsg($result['FlagString'],$link_array[109]['url'].'&id='.$_POST['SAR']['id']);
	}
}elseif($module == 'agentAdd'){
	if(isset($_POST) && !empty($_POST)){
		$data = array('Uin'=>intval($_POST['uin']),'ConfirmUin'=>intval($_POST['confirm_uin']));
		$param = array(
			'param' => array('BigCaseId'=>10002,'CaseId'=>10016,'ParentId'=>10027,'ChildId'=>112,'Desc'=>'新增代理'),
			'extparam' => array('Tag'=>'ProxyAdd','Data'=>$data,'GroupId'=>$__ADMIN_CURGROUP['groupid'])
		);
		$rst = request($param);
		alertMsg($rst['FlagString'],'?module=agentAdd');
	}else{
		$tpl = template::getInstance();
		$tpl->setOptions( get_config( 'template','admin' ) );
		include template( "partner/agent_add.html",$tpl );
	}
}elseif($module == 'channelSync'){
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10027,
			"ChildId"	 => 115,
			"Desc"		 => "渠道关系列表同步"
		),
		'extparam'=>array(
			"Tag" 		 => "ChannelSync",
			"Ids" 	 	 => $_POST['id'],
			"GroupId"=> $__ADMIN_CURGROUP['groupid']
		)
	);
	$result = request($param);
	exit(json_encode($result));
}elseif($module == 'showzzinfo'){
	$link_array = getLevellink(10002,10016,10027,106);
	//取得地域
	$regions = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'getOPenCity')));
	$province = (array)$regions['province'];
	$province_JSONDATA = json_encode($province);
	$city = (array)$regions['city'];
	$city_JSONDATA = json_encode($city);
	$area = (array)$regions['area'];
	$area_JSONDATA = json_encode($area);
	
	$tpl = template::getInstance();
	$tpl->setOptions( get_config( 'template','admin' ) );
	include template( "partner/region_add.html",$tpl );
}elseif($module == 'zzAdd'){
	$post = $_POST;
	$other = $post['other'];
	$post['region_id'] = $other['area']>-1 ? $other['area'] : $other['city'];
	$regionInfo = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetNameByRegion','RegionId'=>$post['region_id'])));
	$post['name'] = $regionInfo['SiteName'].'地域负责人';
	$post['type'] = 7;
	$post['roomid'] = $post['region_id'];
	$post['up_uid'] = 10000;
	$post['partner_id'] = 100;
	$post['descr'] = $regionInfo['SiteName'].'地域负责人';
	$post['pact_id'] = 1;
	$post['other_info'] = addslashes(json_encode($other));
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10016,"ParentId"=>10027,"ChildId"=>106,"Desc"=> "添加地域负责人"),
		'extparam'=>array("Tag"=>"ChannelAdd","Data"=>$post,"GroupId"=> $__ADMIN_CURGROUP['groupid'])
	);
	$result = request($param);
	if($result['Flag'] == 100){
		alertMsg($result['FlagString'], '?module=channelList');
	}else{
		alertMsg($result['FlagString'], -1);
	}
}