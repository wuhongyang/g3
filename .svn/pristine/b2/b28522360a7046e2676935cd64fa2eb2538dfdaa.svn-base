<?php
include_once('../library/global.fun.php');

$module = trim($_GET['module']);
if(empty($module) || $module=='cate_list'){
	$link_array = getLevellink(10002,10069,10648,101);
	$param = array(
			'param' => array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10648,"ChildId"=>131,"Desc"=>"分站礼物信息"),
			'extparam' => array("Tag"=>"GetGiftCate", "TplId"=>intval($_GET['tpl_id']))
	);
	$data = request($param);
	$list = $data['List'];
	$page = $data['Page'];
	$tpl_name = "gift_cate_list.html";
}elseif($module=='add_cate'){
	$link_array = getLevellink(10002,10069,10648,101);
	$one = "";
	if($_GET['cate_id']){
		$param = array(
				'param' => array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10648,"ChildId"=>131,"Desc"=>"分站礼物信息"),
				'extparam' => array("Tag"=>"GetGiftCate","Id"=>$_GET['cate_id'])
		);
		$data = request($param);
		$one = $data['List'];
	}
	$tpl_name = "gift_cate_add.html";
}elseif($module == 'move_cate'){
    $param = array(
			'param' => array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10648,"ChildId"=>137,"Desc"=>"礼物分类排序"),
			'extparam' => array("Tag"=>"MoveCate","Id"=>$_GET['cate_id'],"Direct"=>$_GET['direct'])
	);
    $data = request($param);
    ShowMsg($data['FlagString'], "props_manage.php?module=cate_list&tpl_id=".$_GET['tpl_id']);
}elseif($module=='add_cate_submit'){
	$cate_name = $_POST['cate_name'];
	if(trim($cate_name) == ""){
		$data = array("Flag"=>102, "FlagString"=>"名字不能为空");
		echo json_encode($data);
		exit;
	}
	$status = $_POST['status'];
	$cate_id = $_POST['cate_id']?$_POST['cate_id']:0;
	$len = mb_strlen($cate_name, "utf-8");
    $tpl_id = intval($_POST['tpl_id']);
	if($len >= 20){
		$data = array("Flag"=>102, "FlagString"=>"名字长度不能超过20个字符");
	}else{
		$param = array(
				'param' => array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10648,"ChildId"=>133,"Desc"=>"分站礼物信息"),
				'extparam' => array("Tag"=>"SaveGiftCate","Name"=>$cate_name, "Status"=>$status, "Id"=>$cate_id, "TplId"=>$tpl_id)
		);
		$data = request($param);
	}
	echo json_encode($data);
	exit;
}elseif($module=='props_list'){
	$link_array = getLevellink(10002,10069,10648,101);
	$param = array(
			'param' => array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10648,"ChildId"=>131,"Desc"=>"分站礼物信息"),
			'extparam' => array("Tag"=>"GetGiftCate", "Id"=>0, "NoPage"=>True, "TplId"=>$_GET['tpl_id'])
	);
	$data = request($param);
	$cate = $data['List'];
    
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 112,
			"Desc"		 => "分站礼物列表"
		),
		'extparam'=>array(
			"Tag"	=> "PropsList",
			"Data"  => $_GET
		)
	);
	
	$arr = request($info);
    $_GET['cate_id'] = $arr['cate_id']?$arr['cate_id']:$_GET['cate_id'];
    
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
	
	$tpl_name = "props_list.html";
	
}elseif($module == 'props_add'){
	$regionId = ($_POST['area'] == -1) ? $_POST['city'] : $_POST['area'];
	
	$percent_with_pool = (int)$_POST['receive_percent'] + (int)$_POST['tax_percent'] + (int)$_POST['pool_percent'];
	$percent_without_pool = (int)$_POST['receive_percent'] + (int)$_POST['tax_percent'];
	if(($percent_without_pool != 100 && !$_POST['is_prize']) || ($percent_with_pool != 100 && $_POST['is_prize'])){
		ShowMsg("税收比例不正确", -1);
	}
	
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 114,
			"Desc"		 => "添加分站礼物"
		),
		'extparam'=>array(
			"Tag" 			=> "PropsAdd",
			"CateId"		=> $_POST['cate_id'],
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
			"SwfCatId"		=> $_POST['swf_category'],
			"SwfPicId"		=> $_POST['swf_pic'],
			"BigSwfCatId"   => $_POST['big_swf_category'],
			"BigSwfPicId"   => $_POST['big_swf_pic'],
			"SCREENSIZE"    => $_POST['screen_size'],
			"PropsMoney"	=> $_POST['props_money'],
			"TaxPercent"	=> $_POST['tax_percent'],
			"ReceivePercent"=> $_POST['receive_percent'],
			"PropsStatus"	=> $_POST['props_status'],
			"ActorTax"		=> $_POST['actor_tax'],
			"ConfigName"	=> $_POST['config_name'],
			"Key"			=> $_POST['key'],
			"Value"			=> $_POST['value'],
			"IsPrize"		=> $_POST['is_prize'],
			"PoolPercent"	=> $_POST['pool_percent']
		)
	);
	$result = request($info);
	if($result['Flag'] == '100'){
		ShowMsg($result['FlagString'],'?module=props_list&is_tricky='.$_GET['is_tricky'].'&tpl_id='.$_GET['tpl_id'].'&cate_id='.$_GET['cate_id']);
	}else{
		ShowMsg($result['FlagString'],'?module=props_list&is_tricky='.$_GET['is_tricky'].'&tpl_id='.$_GET['tpl_id'].'&cate_id='.$_GET['cate_id']);
	}
	}elseif($module == 'props_update'){
	$regionId = ($_POST['area'] == -1) ? $_POST['city'] : $_POST['area'];
	
	$percent_with_pool = (int)$_POST['receive_percent'] + (int)$_POST['tax_percent'] + (int)$_POST['pool_percent'];
	$percent_without_pool = (int)$_POST['receive_percent'] + (int)$_POST['tax_percent'];
	if(($percent_without_pool != 100 && !$_POST['is_prize']) || ($percent_with_pool != 100 && $_POST['is_prize'])){
		ShowMsg("税收比例不正确", -1);
	}
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 117,
			"Desc"		 => "修改分站礼物"
		),
		'extparam'=>array(
			"Tag" 			=> "PropsUpdate",
			"Id"			=> $_POST['id'],
			"CateId"		=> $_POST['cate_id'],
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
			"SwfCatId"		=> $_POST['swf_category'],
			"SwfPicId"		=> $_POST['swf_pic'],
			"BigSwfCatId"   => $_POST['big_swf_category'],
			"BigSwfPicId"   => $_POST['big_swf_pic'],
			"SCREENSIZE"    => $_POST['screen_size'],
			"PropsMoney"	=> $_POST['props_money'],
			"TaxPercent"	=> $_POST['tax_percent'],
			"ReceivePercent"=> $_POST['receive_percent'],
			"PropsStatus"	=> $_POST['props_status'],
			"ActorTax"		=> $_POST['actor_tax'],
			"ConfigName"	=> $_POST['config_name'],
			"Key"			=> $_POST['key'],
			"Value"			=> $_POST['value'],
			"IsPrize"		=> $_POST['is_prize'],
			"PoolPercent"	=> $_POST['pool_percent']
		)
	);
	$result = request($info);
	if($result['Flag'] == '100'){
		ShowMsg($result['FlagString'],'?module=props_list&is_tricky='.$_GET['is_tricky'].'&tpl_id='.$_GET['tpl_id'].'&cate_id='.$_GET['cate_id']);
	}else{
		ShowMsg($result['FlagString'],'?module=props_list&is_tricky='.$_GET['is_tricky'].'&tpl_id='.$_GET['tpl_id'].'&cate_id='.$_GET['cate_id']);
	}
	exit();
}elseif($module == 'props_del'){
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 124,
			"Desc"		 => "删除分站礼物"
		),
		'extparam'=>array(
			"Tag" 			=> "PropsDel",
			"Id"			=> $_GET['id']
		)
	);
	$result = request($info);
	if($result['Flag'] == '100'){
		ShowMsg($result['FlagString'],'?module=props_list');
	}
	exit();
}elseif($module == "props_order"){
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 125,
			"Desc"		 => "分站礼物排序"
		),
		'extparam'=>array(
			"Tag" 	=> "PropsOrder",
			"Id"	=> intval($_GET['id']),
			"Type"  => trim($_GET['type'])
		)
	);
	$result = request($info);
	ShowMsg($result['FlagString'],'?module=props_list&is_tricky='.$_GET['is_tricky'].'&tpl_id='.$_GET['tpl_id'].'&cate_id='.$_GET['cate_id']);
}elseif($module == 'ajax'){
	$bigcase_id = intval($_GET['bigcase_id']);
	$case_id = intval($_GET['case_id']);
	$parent_id = intval($_GET['parent_id']);
	exit(json_encode(httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetFlashCMD','BigCaseId'=>$bigcase_id,'CaseId'=>$case_id,'ParentId'=>$parent_id)))));
}


$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('regions/'.$tpl_name,$tpl);