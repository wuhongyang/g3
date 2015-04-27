<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';
require_once 'image_to_oss.php';

$module=empty($_GET['module'])?'cate':$_GET['module'];

//验证是否登陆
$user=checkDpLogin();
//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$permission=(array)$permisssions['permission'];
$group_id=(int)$permisssions['groupId'];

if(!checkGroupPermission(10620,$permission)){
	alertMsg('无权访问','/group/mgr.html');
}

$cates = array("img" => "图片", "txt" => "文字");

switch($module){
	default:
	case 'cate':
		$template = "link_cate_list";
		$param = array(
			'extparam'=>array('Tag'=>'LinkCateList','GroupId'=>$group_id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10620,'ChildId'=>101,'Desc'=>'友情链接分类列表读取')
		);
		$result = request($param);
		$cateList = (array)$result['List'];
		foreach ($cateList as $val){
			if($val['type'] == "img"){
				$imgCate = array("title"=>$val['title'], "is_show"=>$val['is_show']);
			}
			if($val['type'] == "txt"){
				$txtCate = array("title"=>$val['title'], "is_show"=>$val['is_show']);
			}
		}
		break;
	case 'cate_save':
		$type = $_POST['type'] == 1?'img':'txt';
		$_POST['type'] = $type;
		$_POST['group_id'] = intval($group_id);
		$param = array(
			'extparam'=>array('Tag'=>'LinkCateSave','data'=>$_POST),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10620,'ChildId'=>102,'Desc'=>'友情链接分类保存')
		);
		$result = request($param);
		echo json_encode($result);
		exit;
	case 'cate_show':
		$type = $_POST['type'] == 1?'img':'txt';
		$_POST['type'] = $type;
		$_POST['group_id'] = intval($group_id);
		$param = array(
			'extparam'=>array('Tag'=>'LinkCateShow','data'=>$_POST),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10620,'ChildId'=>102,'Desc'=>'友情链接分类保存')
		);
		$result = request($param);
		echo json_encode($result);
		exit;
	case 'link_list':
		$data = array('type'=>$_GET['type'], 'group_id'=>intval($group_id));
		$param = array(
			'extparam'=>array('Tag'=>'LinkList','data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10620,'ChildId'=>103,'Desc'=>'友情链接列表读取')
		);
		$result = request($param);
		$list = $result['Data'];
		$page = $result['Page'];
		$template = "link_".$_GET['type']."_list";
		break;
	case 'link_add':
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$data = $_POST;
			$data['group_id'] = intval($group_id);
			if($_FILES['logo']['tmp_name']){
				$iconMd5 = '';
				$rst = check_upload($_FILES['logo']);
				if($rst['Flag'] != 100){
					exit('<script>parent.callback(101,"'.$rst['FlagString'].'");</script>');
				}
				$rst = send_to_oss($_FILES['logo']['tmp_name']);
				if($rst['Flag'] != 100){
					exit('<script>parent.callback(101,"上传图标失败");</script>');
				}
				$iconMd5 = $rst['File'];
				$data['logo'] = $iconMd5;
			}
			$data['order'] = intval($_POST['order']);
			$param=array(
				'extparam'=>array('Tag'=>'LinkSave','data'=>$data),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10620,'ChildId'=>105,'Desc'=>'友情链接保存')
			);
			$rst = request($param);
			exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'");</script>');
		}else{
			exit('<script>parent.callback(101,"非法调用");</script>');
		}
		break;
	case 'link_edit':
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$data = $_POST;
			$data['group_id'] = intval($group_id);
			$iconMd5 = '';
			if(empty($_FILES['logo']['tmp_name'])){
				$iconMd5 = $_POST['logo'];
			}else{
				$rst = check_upload($_FILES['logo']);
				if($rst['Flag'] != 100){
					exit('<script>parent.callback(101,"'.$rst['FlagString'].'");</script>');
				}
				$rst = send_to_oss($_FILES['logo']['tmp_name']);
				if($rst['Flag'] != 100){
					exit('<script>parent.callback(101,"上传图标失败");</script>');
				}
				$iconMd5 = $rst['File'];
			}
			$data['logo'] = $iconMd5;
			$data['order'] = intval($_POST['order']);
			$param=array(
				'extparam'=>array('Tag'=>'LinkSave','data'=>$data),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10620,'ChildId'=>105,'Desc'=>'友情链接保存')
			);
			$rst = request($param);
			exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'");</script>');
		}else{
			exit('<script>parent.callback(101,"非法调用");</script>');
		}
		break;
	case 'link_info':
		$param = array(
			'extparam'=>array('Tag'=>'LinkInfo','id'=>$_GET['id']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10620,'ChildId'=>103,'Desc'=>'友情链接读取')
		);
		$info = request($param);
		$info = (array)$info['Data'];
		exit(json_encode($info));
		break;
	case 'link_del':
		$id = intval($_GET['id']);
		$param = array(
			'extparam'=>array('Tag'=>'LinkDel','id'=>$id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10620,'ChildId'=>104,'Desc'=>'友情链接删除')
		);
		$rst = request($param);
		echo json_encode($rst);
		exit;
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('decoration/'.$template.".html",$tpl);