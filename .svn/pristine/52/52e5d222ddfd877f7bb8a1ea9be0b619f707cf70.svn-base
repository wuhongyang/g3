<?php
include_once('../library/global.fun.php');

$module = trim($_GET['module']);

if($module == 'cate_list'){
	$link_array = getLevellink(10002,10069,10648,101);
	$search = array_map("addslashes", array_map("htmlspecialchars", (array)$_GET['search']));
	$param = array(
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>150,"Desc"=>"角色组列表"),
			'extparam'=>array("Tag"=>"RoleCate","SearchData" =>$search)
	);
	$result = request($param);
	$cateList = (array)$result['CateList'];
	$page = $result['Page'];
	$temp = 'cate_list.html';
}elseif($module == 'cate_del'){
	$link_array = getLevellink(10002,10069,10648,101);
	$cate_id = intval($_GET['cate_id']);
	$param = array(
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>147,"Desc"=>"角色组删除"),
			'extparam'=>array("Tag"=>"DelCate","CateId"=>$cate_id)
	);
	$result = request($param);
	ShowMsg($result['FlagString'], "?module=cate_list");
}elseif($module == 'cate_update'){
	$link_array = getLevellink(10002,10069,10648,101);
	if($_POST){
		$post = array_map("addslashes", array_map("htmlspecialchars", array_map("trim", array_merge($_POST, array('tpl_id'=>$_GET['tpl_id'])))));
		$param = array(
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>147,"Desc"=>"角色组保存"),
			'extparam'=>array("Tag"=>"SaveCate","Data" =>$post)
		);
		$result = request($param);
		if($result['Flag'] != 100){
			ShowMsg($result['FlagString'], -1);
		}else{
			ShowMsg($result['FlagString'], "?module=cate_list&search[tpl_id]=".$_GET['tpl_id']);
		}
	}
	$cate_id = $_GET['cate_id']?$_GET['cate_id']:-1;
	if($cate_id){
	 	$cate_id = intval($cate_id);
	 	$param = array(
	 			'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>150,"Desc"=>"角色组详情"),
	 			'extparam'=>array("Tag"=>"CateInfo","CateId"=>$cate_id)
	 	);
	 	$result = request($param);
	 	$row = $result['Data'];
	}
	$temp = 'cate_update.html';
}elseif($module=='list'){
	$link_array = getLevellink(10002,10069,10648,101);
    
    $search = $_GET['search'];
    $search['tpl_id'] = $_GET['tpl_id'];
	$param = array(
		'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>138,"Desc"=>"角色管理列表"),
		'extparam'=>array("Tag"=>"RoleList","SearchData" => $search)
	);
	$result = request($param);
	$roleList = (array)$result['RoleList'];
	$page = $result['Page'];
	$temp = 'role_list.html';
}elseif($module == 'info'){
	$rule_config = array(1=>'身份类', 2=>'等级类');
	$rule_show_config = array(
			1 => array(
					'name' => '签约管理',
					'sub' => array(
							1 => '室主签约',
							2 => '站长直接签约',
							3 => '艺人签约'
					)
			),
			2 => array(
					'name' => '室主赋予',
					'sub' => array(
							1 => '房间角色赋予'
					)
			),
			3 => array(
					'name' => '登录赋予',
					'sub' => array(
							1 => '普通用户赋予'
					)
			),
			4 => array(
					'name' => '站长赋予',
					'sub' => array(
						1 => '站长角色'
					)
			)
	);
	$rule_show_config_json = json_encode($rule_show_config);
	$scope = array(1=>'站',2=>'房间',3=>'平台');
	
	if(isset($_GET['id']) && $_GET['id'] > 0){
		//修改页面
		$param = array(
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>138,"Desc"=>"角色管理详情"),
			'extparam'=>array("Tag"=>"RoleInfo","Id"=>$_GET['id'])
		);
		$result = request($param);
		$info = $result['RoleInfo'];
		$colors = json_decode($info['color'],true);
		$font_color = json_decode($info['font_color'],true);
		$icon_area = json_decode($info['icon_area'],true);
	}
	$param = array(
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>150,"Desc"=>"角色组列表"),
			'extparam'=>array("Tag"=>"RoleCate","IsNotPage" =>true,'SearchData'=>array('status'=>1,'tpl_id'=>$_GET['tpl_id']))
	);
	$result = request($param);
	$list = $result['CateList'];
	
	$temp = 'role_info.html';
}elseif($module == 'add'){
	$data = $_POST;
	//添加保存
	$param = array(
		'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>142,"Desc"=>"添加角色"),
		'extparam'=>array("Tag"=>"RoleAdd","Data"=>$data)
	);
	$result = request($param);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],'?module=list&search[cate_id]='.$data['cate_id']."&tpl_id=".$_GET['tpl_id']);
	}else{
		ShowMsg($result['FlagString'],'-1');
	}
}elseif($module == 'update'){
	$data = $_POST;
	$id = $_POST['id'];

	//修改保存
	$param = array(
		'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>142,"Desc"=>"修改角色"),
		'extparam'=>array("Tag"=>"RoleUpdate","Id"=>$id,"Data"=>$data)
	);
	$result = request($param);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],'?module=list&search[cate_id]='.$data['cate_id']."&tpl_id=".$_GET['tpl_id']);
	}else{
		ShowMsg($result['FlagString'],'-1');
	}

}elseif($module == 'copy'){
	$param = array(
		'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>142,"Desc"=>"复制角色"),
		'extparam'=>array("Tag"=>"CopyRole","RoleId" => $_POST['roleid'])
	);
	$rst = request($param);
	exit(json_encode($rst));
}elseif($module == 'del'){
	$param = array(
		'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>145,"Desc"=>"删除角色"),
		'extparam'=>array("Tag"=>"DelRole","RoleId" => $_POST['roleid'])
	);
	$rst = request($param);
	exit(json_encode($rst));
}elseif($module == 'up_role_icon'){
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if(empty($_FILES)){
			alertMsg('图片太大，请重新上传');
		}
		if(!empty($_FILES['role_icon']['tmp_name'])){
            preg_match('/image/',$_FILES['role_icon']['type'],$imgpreg);
            preg_match('/flash/',$_FILES['role_icon']['type'],$swfpreg);
			if(empty($imgpreg) && empty($swfpreg)){
				alertMsg("上传图片格式必须为jpg，png，gif，swf格式");
			}
			$size = $_FILES['role_icon']['size']/(pow(1024, 2));
			if($size > 2){
				alertMsg("上传图片不能大于2M，请重新上传");
			}
			$bytes = file_get_contents($_FILES['role_icon']['tmp_name']);
			$index = md5($bytes);
			$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
			$query = json_decode(socket_request(UPLOAD_API_PATH,$opt),true);
			$rst = json_encode(array('Flag'=>$query['rst'],'File'=>$index));
		}
	}
	$type = isset($_GET['type']) ? $_GET['type'] : $_POST['type'];
	$temp = 'up_role_icon.html';
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('permission/'.$temp,$tpl);