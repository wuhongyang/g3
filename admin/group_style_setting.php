<?php
include_once('../library/global.fun.php');
$module = isset($_GET['module']) ? trim($_GET['module']) : 'list';

if($module == 'list'){
	$link_array = getLevellink(10002,10003,10005,104);
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10005,"ChildId"=>103),
		'extparam'=>array("Tag"=>"StyleSettingList")
	);
	$result = request($param);
	$lists = (array)$result['Result'];
	$temp = 'style_setting_list.html';
}elseif($module == 'info'){
	$link_array = getLevellink(10002,10003,10005,103);
	//得到图片分类
	$cat = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'CatList','mypost'=>array('state'=>1))));
	//得到图片
	$pic = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1))));
	
	if($_GET['id']>0){
		$param = array(
			'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10005,"ChildId"=>103),
			'extparam'=>array("Tag"=>"StyleSettingList","Id"=>(int)$_GET['id'])
		);
		$rst = request($param);
		$info = $rst['Result'][0];
		$num=0;
		foreach($pic['lists'] as $val){
			if($info['bg_img']==$val['id']){
				$info['bg_img_cat']=$val['cat_id'];
				if($num>=2){
					break;
				}
				$num++;
			}
			if($info['banner']==$val['id']){
				$info['banner_cat']=$val['cat_id'];
				if($num>=2){
					break;
				}
				$num++;
			}
			if($info['thumb']==$val['id']){
				$info['thumb_cat']=$val['cat_id'];
				if($num>=2){
					break;
				}
				$num++;
			}
		}
	}
	
	$cat = json_encode((array)$cat['lists']);
	$pic = json_encode((array)$pic['lists']);
	
	//分类
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10005,"ChildId"=>103),
		'extparam'=>array("Tag"=>"StyleCategoryList")
	);
	$category = request($param);
	$category = $category['Result'];
	
	$temp = 'style_setting_info.html';
}elseif($module == 'save'){
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10005,"ChildId"=>104),
		'extparam'=>array("Tag"=>"StyleSettingSave","Data"=>$_POST)
	);
	$rst = request($param);
	if($rst['Flag'] != 100){
		alertMsg($rst['FlagString']);
	}
	alertMsg($rst['FlagString'],'?module=list');
}elseif($module == 'add_category'){
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10005,"ChildId"=>105),
		'extparam'=>array("Tag"=>"StyleCategorySave","Data"=>$_POST)
	);
	$rst = request($param);
	echo json_encode($rst);
	exit;
}
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('group/'.$temp,$tpl);