<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'join_list':$_GET['module'];

//验证是否登陆
$user=checkDpLogin();
$Uin=$user['Uin'];
$Nick=$user['Nick'];
//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$isDz=(int)$permisssions['isDz'];
$groupId=(int)$permisssions['groupId'];
$permission=(array)$permisssions['permission'];
$menuPermissions=(array)$permisssions['menuPermissions'];

$roles = array('roomer'=>'室主','artist'=>'艺人','agent'=>'代理');
$role2num = array('roomer'=>2,'artist'=>1,'agent'=>3);

if(!checkGroupPermission(10331,$permission)){
	alertMsg('无权访问','group.php?module=group_info');
}

switch ($module) {
	case 'join_list':
		$title='主页装修-加入我们';
		$template = 'join_list';
		$param = array(
			'extparam'=>array('Tag'=>'JoinList','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10331,'ChildId'=>101,'Desc'=>'加入我们列表读取')
		);
		$list = request($param);
		$joinList = (array)$list['List'];	
		
		/*$joins = array();
		foreach ($joinList as $key => $value) {
			if($value['status'] == 1){
				$joins[$value['role']] = array('uptime'=>$value['uptime']);
			}
		}

		$diff = array_diff(array_keys($roles), array_keys($joins));

		foreach ($diff as $val) {
			$joins[$val] = array('uptime'=>0);
		}*/
		break;
	case 'join_edit':
		$title='主页装修-加入我们';
		if(isset($_POST) && !empty($_POST)){
			$data = $_POST;
			$data['groupId'] = $groupId;
			$param = array(
				'extparam'=>array('Tag'=>'JoinEdit','Data'=>$data),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10331,'ChildId'=>102,'Desc'=>'加入我们保存')
			);
			$rst = request($param);
			exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'")</script>');
		}else{
			//$role = $_GET['role'];
			$id = $_GET['id'];
			$param = array(
				'extparam'=>array('Tag'=>'JoinInfo','GroupId'=>$groupId,'Role'=>$id),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10331,'ChildId'=>101,'Desc'=>'加入我们列表读取')
			);
			$info = request($param);
			$info = $info['Info'];
			$contact = json_decode($info['contact'], true);
			$count = count($contact)-1;
			$template = 'join_info';
		}
		break;
	case 'join_del':
		//$role = $_POST['role'];
		$id = $_POST['id'];
		$param = array(
			'extparam'=>array('Tag'=>'JoinDel','GroupId'=>$groupId,'id'=>$id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10331,'ChildId'=>103,'Desc'=>'加入我们删除')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'join_add':
		$title='主页装修-加入我们';
		if(isset($_POST) && !empty($_POST)){
			$data = $_POST;
			$data['groupId'] = $groupId;
			$param = array(
				'extparam'=>array('Tag'=>'JoinEdit','Data'=>$data),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10331,'ChildId'=>102,'Desc'=>'加入我们保存')
			);
			$rst = request($param);
			exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'")</script>');
		}else{
			$count = -1;
			$template = 'join_info';
		}
		break;
}

$tool = 'joinus';
$serviceType = 'decoration';

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('decoration/'.$template.'.html',$tpl);