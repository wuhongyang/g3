<?php
include_once( '../library/global.fun.php');
$link_array = getLevellink(10002,10062,10470,101);

if($_POST){
	if($_POST['force']){
		$param = array(
				'param'=>array("BigCaseId"=>10002,"CaseId"=>10062,"ParentId"=>10470,"ChildId"=>101,"Desc"=>"数据导出"),
				'extparam'=>array("Tag"=>"ForceEnd")
		);
		$result = request($param);
		echo json_encode($result);
	}elseif($_POST['end']){
		$param = array(
				'param'=>array("BigCaseId"=>10002,"CaseId"=>10062,"ParentId"=>10470,"ChildId"=>101,"Desc"=>"数据导出"),
				'extparam'=>array("Tag"=>"ExportEnd")
		);
		$result = request($param);
		echo json_encode($result);
	}else{
		$param = array(
				'param'=>array("BigCaseId"=>10002,"CaseId"=>10062,"ParentId"=>10470,"ChildId"=>101,"Desc"=>"数据导出"),
				'extparam'=>array("Tag"=>"ExportTable","Table"=>$_POST['table'],"NewFile"=>$_POST['new_file'])
		);
		$result = request($param);
		echo json_encode($result);
	}
	exit;
}
$module = empty($_GET['module']) ? 'data_export' : trim($_GET['module']);
switch($module){
	case 'data_export':
		$config_array = array(
				array('name'=>'角色管理', 'value'=>'g3_template.role'),
				array('name'=>'角色权限管理', 'value'=>'g3_template.role_permission'),
				array('name'=>'角色套餐管理', 'value'=>'g3_permission.role_package'),
				array('name'=>'印章管理', 'value'=>'g3_regions.tbl_stamp'),
				array('name'=>'印章分类', 'value'=>'g3_regions.tbl_stamptype'),
				array('name'=>'表情分类', 'value'=>'g3_regions.tbl_expressiontype'),
				array('name'=>'表情管理', 'value'=>'g3_regions.tbl_expression'),
				array('name'=>'礼物列表', 'value'=>'g3_regions.tbl_props'),
				array('name'=>'礼物分类', 'value'=>'g3_template.tbl_gift_cate'),
				array('name'=>'房间方案管理', 'value'=>'g3_system_config.tbl_ui_package'),
				array('name'=>'图片分类', 'value'=>'g3_system_config.pic_catagory'),
				array('name'=>'图片列表', 'value'=>'g3_system_config.pic_manager'),
				array('name'=>'房间活动规则', 'value'=>'kkyoo_behavior.business_param_group'),
				array('name'=>'房间界面管理', 'value'=>'g3_system_config.tbl_rooms_ui'),
				array('name'=>'站内排行配置', 'value'=>'g3_groups.rank_setting'),
				array('name'=>'一级科目', 'value'=>'kkyoo_ccs.tbl_bigcase'),
				array('name'=>'二级科目', 'value'=>'kkyoo_ccs.tbl_case'),
				array('name'=>'三级科目', 'value'=>'kkyoo_ccs.tbl_parent'),
				array('name'=>'四级科目', 'value'=>'kkyoo_ccs.tbl_child'),
				array('name'=>'常规科目', 'value'=>'kkyoo_ccs.tbl_child_common'),
				array('name'=>'商品方案管理', 'value'=>'g3_shop.scheme'),
				array('name'=>'商品管理', 'value'=>'g3_shop.commodity'),
				array('name'=>'商品类别', 'value'=>'g3_shop.category'),
				array('name'=>'业务参数', 'value'=>'kkyoo_behavior.business_param_config'),
				array('name'=>'积分规则', 'value'=>'kkyoo_behavior.business_rule'),
				array('name'=>'站点风格模板', 'value'=>'g3_groups.style_setting&g3_groups.style_category'),
				array('name'=>'角色组', 'value'=>'g3_template.role_cate'),
				array('name'=>'主键列表', 'value'=>'kkyoo_behavior.business_key'),
				array('name'=>'主键组', 'value'=>'kkyoo_behavior.business_compose_key')
		);
		break;
	case 'download':
		$filepath = "./data_export/data_export.sql";
		header("Content-Type: text/plain");
		header('Content-Disposition: attachment; filename="'.uniqid().'.sql"');
		header("Content-Length: ".filesize($filepath));
		echo file_get_contents($filepath);
		exit;
}


$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('data_export/'.$module.".html",$tpl);