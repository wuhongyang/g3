<?php
include_once('../library/global.fun.php');

$module = trim($_GET['module']);
if($module=='props_list' || empty($module)){
	$link_array = getLevellink(10002,10069,10648,101);
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 111,
			"Desc"		 => "游戏道具列表"
		),
		'extparam'=>array(
			"Tag"	=> "GamePropsList",
			"Data"  => $_GET
		)
	);
	
	$arr = request($info);
	$area_arr = $arr['region'];
	$areaInfo = json_encode($area_arr);
	$perpage = 15;
	$pages = new extpage(array (
		'total' => $arr['total'],
		'perpage' => $perpage
	));
	
	$arr = (array)$arr['list'];
	for($i=$pages->offset; $i<$pages->offset + $perpage; $i++){
		if($i >= count($arr))  break;
		$props_arr[] = $arr[$i];
	}
	$page = $pages->show();
	
	if(!empty($props_arr)){
		//得到科目名称
		foreach((array)$props_arr as $k=>$v){
			$res = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetBigCase','BigCaseId'=>$v['big_case_id'])));
			$props_arr[$k]['bigcase_name'] = $res['Result']['bigcase_name'];
			$res = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetCase','CaseId'=>$v['case_id'])));
			$props_arr[$k]['case_name'] = $res['Result']['case_name'];
			$res = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetParent','ParentId'=>$v['parent_id'])));
			$props_arr[$k]['parent_name'] = $res['Result']['parent_name'];
		}
	}
	
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','admin'));
	include template('regions/game_props_list.html',$tpl);
}elseif($module == 'props_add'){
	$regionId = ($_POST['area'] == -1) ? $_POST['city'] : $_POST['area'];
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 115,
			"Desc"		 => "添加游戏道具"
		),
		'extparam'=>array(
			"Tag" 			=> "GamePropsAdd",
			"Cmd"			=> $_POST['cmd'],
			"CmdPath"		=> $_POST['cmd_path'],
			"BigCaseId"		=> $_POST['bigcase_id'],
			"CaseId"		=> $_POST['case_id'],
			"ParentId"		=> $_POST['parent_id'],
			"ProvinceId"	=> $_POST['province'],
			"CityId"		=> $_POST['city'],
			"AreaId"		=> $_POST['area'],
			"RegionId"		=> $regionId,
			"PropsName"		=> $_POST['props_name'],
			"PropsDesc"		=> $_POST['props_desc'],
			"PropsSize"		=> $_POST['props_size'],
			"PropsStatus"	=> $_POST['props_status'],
			"CatId"			=> $_POST['category'],
			"PicId"			=> $_POST['pic'],
			"PropsIco"		=> $_POST['props_ico'],
            "TplId"         => $_POST['tpl_id']
		)
	);
	$result = request($info);
	if($result['Flag'] == '100'){
		ShowMsg($result['FlagString'],'?module=props_list&tpl_id='.$_POST['tpl_id']);
	}
}elseif($module == 'props_update'){
	$regionId = ($_POST['area'] == -1) ? $_POST['city'] : $_POST['area'];
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 120,
			"Desc"		 => "修改游戏道具"
		),
		'extparam'=>array(
			"Tag" 			=> "GamePropsUpdate",
			"Id"			=> $_POST['id'],
			"Cmd"			=> $_POST['cmd'],
			"CmdPath"		=> $_POST['cmd_path'],
			"BigCaseId"		=> $_POST['bigcase_id'],
			"CaseId"		=> $_POST['case_id'],
			"ParentId"		=> $_POST['parent_id'],
			"ProvinceId"	=> $_POST['province'],
			"CityId"		=> $_POST['city'],
			"AreaId"		=> $_POST['area'],
			"RegionId"		=> $regionId,
			"PropsName"		=> $_POST['props_name'],
			"PropsDesc"		=> $_POST['props_desc'],
			"PropsSize"		=> $_POST['props_size'],
			"PropsStatus"	=> $_POST['props_status'],
			"CatId"			=> $_POST['category'],
			"PicId"			=> $_POST['pic'],
			"PropsIco"		=> $_POST['props_ico']
		)
	);
	$result = request($info);
	if($result['Flag'] == '100'){
		ShowMsg($result['FlagString'],'?module=props_list&tpl_id='.$_POST['tpl_id']);
	}
}elseif($module == 'props_del'){
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 121,
			"Desc"		 => "删除游戏道具"
		),
		'extparam'=>array(
			"Tag" 			=> "GamePropsDel",
			"Id"			=> $_GET['id']
		)
	);
	$result = request($info);
	if($result['Flag'] == '100'){
		ShowMsg($result['FlagString'],'?module=props_list');
	}
}elseif($module == "props_order"){
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 127,
			"Desc"		 => "游戏道具排序"
		),
		'extparam'=>array(
			"Tag" 	=> "GamePropsOrder",
			"Id"	=> intval($_GET['id']),
			"Type"  => trim($_GET['type'])
		)
	);
	$result = request($info);
	ShowMsg($result['FlagString'],'?module=props_list&tpl_id='.$_GET['tpl_id']);
}elseif($module == 'ajax'){
	$bigcase_id = intval($_GET['bigcase_id']);
	$case_id = intval($_GET['case_id']);
	$parent_id = intval($_GET['parent_id']);
	exit(json_encode(httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetFlashCMD','BigCaseId'=>$bigcase_id,'CaseId'=>$case_id,'ParentId'=>$parent_id)))));
}elseif($module == 'props_config'){
	$cmd = $_GET['cmd'];
	$info = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10648,"ChildId"=>132,"Desc"=>"游戏道具配置列表"),
		'extparam'=>array("Tag"=>"GamePropsConfig","Cmd"=>$cmd)
	);
	$result = request($info);
	$result = $result['Result'];
	$fixed_value = $result['value']['fixed'];
	$fixed_descr = $result['descr']['fixed'];
	$descr = $result['descr']['extend'];
	$value = $result['value']['extend'];
	$link_array = getLevellink(10002,10069,10648,101);
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','admin'));
	include template('regions/game_props_config_list.html',$tpl);
}elseif($module == 'modifyConfig'){
	foreach($_POST['key'] as $key=>$value){
		if(!empty($_POST['key'][$key]) && is_string($_POST['key'][$key])){
			$ext_value[$_POST['key'][$key]] = htmlspecialchars($_POST['value'][$key],ENT_QUOTES);
		}
		$ext_descr[$_POST['key'][$key]] = $_POST['descr'][$key];
	}

	$info['key'] = $_POST['cmd'];
	$info['value']['fixed'] = $_POST['val'];
	$info['value']['extend'] = $ext_value;
	$info['descr']['fixed'] = array();
	$info['descr']['extend'] = $ext_descr;
	$info['status'] = $_POST['status'];
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10648,"ChildId"=>130,"Desc"=>"修改游戏道具配置"),
		'extparam'=>array("Tag"=>"GamePropsConfigModify","Data"=>$info)
	);
	$result = request($param);
	ShowMsg($result['FlagString'],'?module=props_config&cmd='.$_POST['cmd']."&tpl_id=".$_POST['tpl_id']);
}/*elseif($module == 'money_bind_config'){
	$cmd = $_GET['cmd'];
	$info = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10111,"ChildId"=>107,"Desc"=>""),
		'extparam'=>array("Tag"=>"GameMoneyBindConfig","Cmd"=>$cmd)
	);
	$result = request($info);
	$funduin = $result['Result'];
	$money = intval(get_money($funduin));
	$link_array = getLevellink(10002,10002,10111,107);
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','admin'));
	include template('regions/game_money_bind_config.html',$tpl);
}elseif($module == 'modifyUinConfig'){
	$cmd = $_POST['cmd'];
	$uin = $_POST['funduin'];
	$info = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10111,"ChildId"=>107,"Desc"=>""),
		'extparam'=>array("Tag"=>"GameMoneyBindConfigSet","Cmd"=>$cmd,"Funduin"=>$uin)
	);
	$result = request($info);
	ShowMsg($result['FlagString'],'?module=money_bind_config&cmd='.$_POST['cmd']);
}elseif($module == 'getBalance'){
	$uin = $_GET['uin'];
	exit(get_money($uin));
}*/