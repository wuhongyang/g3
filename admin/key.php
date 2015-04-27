<?php
require_once '../library/global.fun.php';

$module = $_GET['module']?$_GET['module']:'key_list';
$link_array = getLevellink(10002,10008,10575,101);

switch($module){
	case 'key_list':
		if($_GET['search']){
			$search = $_GET['search'];
			$search = array_map("addslashes", array_map("htmlspecialchars", (array)$search));
		}else{
			$search = array();
		}
		$param = array(
			'extparam' => array('Tag'=>'KeyList','SearchData'=>$search),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10008,'ParentId'=>10575,'ChildId'=>101,'Desc'=>'主键列表读取')
		);
		$result = request($param);
		$list = $result['List'];
		foreach($list as $k=>$v){
			switch($v['type']){
				case 'ChannelId':
					$list[$k]['type_name'] = "房间id";
					break;
				case 'ParentId':
					$list[$k]['type_name'] = "三级科目id";
					break;
				case 'GroupId':
					$list[$k]['type_name'] = "站id";
					break;
				case 'RoleId':
					$list[$k]['type_name'] = "角色id";
					break;
				default:
					$list[$k]['type_name'] = "未知";
					break;
			}
			$list[$k]['status_name'] = $v['status']?"启用":"不启用";
		}
		$page = $result['Page'];
		break;
	case 'key_update':
		if($_POST){
			$post = $_POST;
			$post['name'] = addslashes(htmlspecialchars(trim($post['name'])));
			$post['engname'] = addslashes(htmlspecialchars(trim($post['engname'])));
			if(!$post['name']){
				ShowMsg("主键名称不能为空", -1);
			}
			if(!$post['engname']){
				ShowMsg("主键英文名称不能为空", -1);
			}
			if($post['type'] == "RoleId"){
				if(!$post['extra']['role']){
					ShowMsg("对应身份不能为空", -1);
				}
				$post['extra'] = json_encode($post['extra']);
			}else{
				unset($post['extra']);
			}
			$param = array(
					'extparam' => array('Tag'=>'KeySave','Data'=>$post),
					'param' => array('BigCaseId'=>10002,'CaseId'=>10008,'ParentId'=>10575,'ChildId'=>102,'Desc'=>'主键更新')
			);
			$result = request($param);
			if($result['Flag'] != 100){
				ShowMsg($result['FlagString'], -1);
			}else{
				ShowMsg($result['FlagString'], "?module=key_list");
			}
		}
		//读取角色分组
		$param = array(
				'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>150,"Desc"=>"角色组列表"),
				'extparam'=>array("Tag"=>"RoleCate","IsNotPage" =>true,"SearchData"=>array("status"=>1))
		);
		$result = request($param);
		$cateList = $result['CateList'];
		//读取角色
		$param = array(
				'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>138,"Desc"=>"角色管理列表"),
				'extparam'=>array("Tag"=>"RoleList","SearchData" => array('rule'=>1,'status'=>1),'IsNotPage'=>true)
		);
		$roleList = request($param);
		$roleList = $roleList['RoleList'];
		
		$roleListArr = array();
		$id_to_cate = array();
		foreach($roleList as $one){
			if($one['cate_id']){
				$roleListArr[$one['cate_id']][] = array($one['id'], $one['name']);
				$id_to_cate[$one['cate_id']][] = $one['id'];
			}
		}
		$roleListJson = json_encode($roleListArr);
		if($_GET["key_id"]){
			$param = array(
					'extparam' => array('Tag'=>'KeyDetail','KeyId'=>$_GET["key_id"]),
					'param' => array('BigCaseId'=>10002,'CaseId'=>10008,'ParentId'=>10575,'ChildId'=>101,'Desc'=>'主键读取')
			);
			$result = request($param);
			$row = $result['Row'];
			$row['extra'] = json_decode($row['extra'], true);
			if($row['extra']['role']){
				foreach($id_to_cate as $cate_id=>$id_arr){
					if(in_array($row['extra']['role'], $id_arr)){
						$select_cate_id = $cate_id;
						break;
					}
				}
			}
		}
		break;
	case 'compose_list':
		$param = array(
				'extparam' => array('Tag'=>'ComposeList'),
				'param' => array('BigCaseId'=>10002,'CaseId'=>10008,'ParentId'=>10575,'ChildId'=>103,'Desc'=>'主键组列表读取')
		);
		$result = request($param);
		$list = $result['List'];
		$page = $result['Page'];
		foreach($list as $k=>$v){
			$list[$k]['status_name'] = $v['status']?"开启":"不开启";
			$list[$k]['add_time_name'] = date("Y-m-d H:i:s", $v['uptime']);
		}
		break;
	case 'compose_update':
		if($_POST){
			$post = $_POST;
			$post['name'] = addslashes(htmlspecialchars(trim($post['name'])));
			$post['engname'] = addslashes(htmlspecialchars(trim($post['engname'])));
			$post['desc'] = addslashes(htmlspecialchars(trim($post['desc'])));
			if(!$post['name']){
				ShowMsg("主键名称不能为空", -1);
			}
			if(!$post['engname']){
				ShowMsg("主键英文名称不能为空", -1);
			}
			$param = array(
					'extparam' => array('Tag'=>'ComposeSave','Data'=>$post),
					'param' => array('BigCaseId'=>10002,'CaseId'=>10008,'ParentId'=>10575,'ChildId'=>104,'Desc'=>'主键组更新')
			);
			$result = request($param);
			if($result['Flag'] != 100){
				ShowMsg($result['FlagString'], -1);
			}else{
				ShowMsg($result['FlagString'], "?module=compose_list");
			}
		}
		if($_GET['compose_id']){
			$param = array(
					'extparam' => array('Tag'=>'ComposeDetail','ComposeId'=>$_GET['compose_id']),
					'param' => array('BigCaseId'=>10002,'CaseId'=>10008,'ParentId'=>10575,'ChildId'=>103,'Desc'=>'主键组读取')
			);
			$result = request($param);
			$row = $result['Row'];
			$row['keys'] = json_decode($row['keys'], true);
			$row_arr = array();
		}
		$param = array(
			'extparam' => array('Tag'=>'KeyList','SearchData'=>array("status"=>1), "IsNotPage"=>true),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10008,'ParentId'=>10575,'ChildId'=>101,'Desc'=>'主键列表')
		);
		$result = request($param);
		$list = $result['List'];
		$key_arr = array();
		$type_arr = array();
		foreach($list as $one){
			switch($one['type']){
				case 'ChannelId':
					$type_name = "房间id";
					break;
				case 'ParentId':
					$type_name = "三级科目id";
					break;
				case 'GroupId':
					$type_name = "站id";
					break;
				case 'RoleId':
					$type_name = "角色id";
					break;
				default:
					$type_name = "未知";
				break;
			}
			$key_arr[$one['type']][] = array($one['id'], $one['name']);
			$type_arr[$one['type']] = $type_name;
			if($row['keys'] && @in_array($one['id'], $row['keys'])){
				$row_arr[] = array($one['id'], $one['name'], $type_name);
			}
		}
		$key_arr_json = json_encode($key_arr);
		$type_arr_json = json_encode($type_arr);
		break;
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('key/'.$module.".html",$tpl);