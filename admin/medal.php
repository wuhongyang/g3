<?php
require_once '../library/global.fun.php';
$link_array = getLevellink(10002,10017,10276,101);
$category = array('请选择','游戏勋章');
$status = array('不启用','启用');

switch($_GET['module']){
	case 'medaltype':
		$param = array(
				'param' => array('BigCaseId'=>10002,'CaseId'=>10017,'ParentId'=>10276,'ChildId'=>101,'Desc'=>'勋章分类列表'),
				'extparam' => array('Tag'=>'GetMedalType')
			);
		$result = request($param);
		$lists = $result['Result'];
		$template = 'medaltype.html';
		break;
	case 'medaltype_edit':
		if(!empty($_POST)){
			$param = array(
					'param' => array('BigCaseId'=>10002,'CaseId'=>10017,'ParentId'=>10276,'ChildId'=>102,'Desc'=>'添加编辑勋章分类'),
					'extparam' => array('Tag'=>'EditMedalType','TypeInfo'=>$_POST)
			);
			$result = request($param);
			alertMsg($result['FlagString'],'?module=medaltype');
		}elseif($_GET['del'] > 0){
			$param = array(
					'param' => array('BigCaseId'=>10002,'CaseId'=>10017,'ParentId'=>10276,'ChildId'=>102,'Desc'=>'删除勋章分类'),
					'extparam' => array('Tag'=>'DeleteMedalType','Id'=>$_GET['del'])
				);
			$result = request($param);
			alertMsg($result['FlagString'],'?module=medaltype');
		}elseif($_GET['id'] > 0){
			$param = array(
					'param' => array('BigCaseId'=>10002,'CaseId'=>10017,'ParentId'=>10276,'ChildId'=>101,'Desc'=>'勋章分类列表'),
					'extparam' => array('Tag'=>'GetMedalType','Id'=>$_GET['id'])
				);
			$result = request($param);
			$edit = $result['Result'][0];
		}
		
		$param = array(
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>139,"Desc"=>"业务参数配置列表查看"),
			'extparam'=>array("Tag"=>"ParamConfigList","SearchData"=>'')
		);
		$result = request($param);
		$paramConfigList = (array)$result['Result'];
		$template = 'medaltype_edit.html';
		break;
	case 'medallist':
		$param = array(
				'param' => array('BigCaseId'=>10002,'CaseId'=>10017,'ParentId'=>10276,'ChildId'=>103,'Desc'=>'勋章列表'),
				'extparam' => array('Tag'=>'GetMedalList','Typeid'=>intval($_GET['id']))
			);
		$result = request($param);
		$lists = $result['Result'];
		
		$param = array(
				'param' => array('BigCaseId'=>10002,'CaseId'=>10017,'ParentId'=>10276,'ChildId'=>101,'Desc'=>'勋章分类列表'),
				'extparam' => array('Tag'=>'GetMedalType')
			);
		$result = request($param);
		$result = $result['Result'];
		$medaltype = array();
		foreach($result as $key=>$val){
			$medaltype[$val['id']] = $val['name'];
		}
		
		$template = 'medallist.html';
		break;
	case 'medallist_edit':
		if(!empty($_POST)){
			//得到图片
			$pic = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('id'=>$_POST['iconid']))));
			$_POST['icon'] = $pic['lists'][0]['img_path'];
			$param = array(
					'param' => array('BigCaseId'=>10002,'CaseId'=>10017,'ParentId'=>10276,'ChildId'=>104,'Desc'=>'添加编辑勋章'),
					'extparam' => array('Tag'=>'EditMedal','MedalInfo'=>$_POST)
				);
			$result = request($param);
			alertMsg($result['FlagString'],'?module=medallist');
		}elseif($_GET['del'] > 0){
			$param = array(
					'param' => array('BigCaseId'=>10002,'CaseId'=>10017,'ParentId'=>10276,'ChildId'=>104,'Desc'=>'删除勋章分类'),
					'extparam' => array('Tag'=>'DeleteMedal','Id'=>$_GET['del'])
				);
			$result = request($param);
			alertMsg($result['FlagString'],'?module=medallist');
		}elseif($_GET['id'] > 0){
			$param = array(
					'param' => array('BigCaseId'=>10002,'CaseId'=>10017,'ParentId'=>10276,'ChildId'=>103,'Desc'=>'勋章分类列表'),
					'extparam' => array('Tag'=>'GetMedalList','Id'=>$_GET['id'])
				);
			$result = request($param);
			$edit = $result['Result'][0];
		}
		
		$param = array(
				'param' => array('BigCaseId'=>10002,'CaseId'=>10017,'ParentId'=>10276,'ChildId'=>101,'Desc'=>'勋章分类列表'),
				'extparam' => array('Tag'=>'GetMedalType')
			);
		$result = request($param);
		$result = $result['Result'];
		$medaltype = array();
		foreach($result as $key=>$val){
			$medaltype[$val['id']] = $val['name'];
		}
		//得到图片分类
		$cat = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'CatList','mypost'=>array('state'=>1))));
		$cat = json_encode($cat['lists']);
		//得到图片
		$pic = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1))));
		$pic = json_encode($pic['lists']);
		$template = 'medallist_edit.html';
		break;
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template("medal/".$template,$tpl);
