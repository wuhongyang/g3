<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$GroupData = domain::main()->GroupData();
$group_id = (int)$GroupData['groupid'];
$user=checkDpLogin();
$permisssions=getDpUserPermission($user['Uin']);
$isDz=(int)$permisssions['isDz'];
if(!$group_id || !$isDz){
	exit("需要用站长号登陆并且在站下操作");
}

switch($_GET['module']){
	default:
	case 'passport':
		if($_POST){
			$post = array_map("htmlspecialchars", array_map("addslashes", $_POST));
			$range1 = intval($post['range1']);
			$range2 = intval($post['range2']);
			if($range1 > $range2){
				ShowMsg("填写范围不正确", -1);
			}
			if(!$post['login_pre']){
				ShowMsg("前缀不能为空", -1);
			}
			if(!$post['password']){
				ShowMsg("密码不能为空", -1);
			}
			
			$parameter = array(
				"extparam"=>array(
						"Tag"=>"RegPassport",
						"Pass"=>md5($post['password']),
						"Platform"=>2
						)
			);
			for($i=$range1;$i<=$range2;$i++){
				$parameter['extparam']['User'] = $post['login_pre'].$i;
				$res = httpPOST("core/sso/sso_api.php", $parameter);
				if($res['Flag'] != 100){
					ShowMsg("进行到".$post['login_pre'].$i."被终止,错误原因:".$res['FlagString'], -1);
				}
			}
			ShowMsg("操作成功", "?module=passport");
		}
		$temp = "passport";
		break;
	case 'recharge':
		$param=array(
				'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$group_id),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101)
		);
		$groupinfo=request($param);
		$Ginfo=$groupinfo['Result'];
		
		if($_POST){
			$post = array_map("htmlspecialchars", array_map("addslashes", $_POST));
			$range1 = intval($post['range1']);
			$range2 = intval($post['range2']);
			if($range1 > $range2){
				ShowMsg("填写范围不正确", -1);
			}
			if(!$post['login_pre']){
				ShowMsg("前缀不能为空", -1);
			}
			if(!$post['weight']){
				ShowMsg("金额不能为空", -1);
			}
			
			for($i=$range1;$i<=$range2;$i++){
				$parameter = array(
						"extparam"=>array(
								"Tag"=>"GetUser",
								"UserName"=>$post['login_pre'].$i
						)
				);
				$res = httpPOST("core/sso/sso_api.php", $parameter);
				if($res['Flag'] != 100){
					ShowMsg("进行到".$post['login_pre'].$i."被终止,错误原因:".$res['FlagString'], -1);
				}
				
				$param=array(
						'extparam'=>array('Tag'=>'VipRecharge','Uin'=>$Ginfo['uin'],'TargetUin'=>$res['Uid'],'GroupId'=>$group_id,'Weight'=>$post['weight']),
						'param'=>array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>103,'GroupId'=>$group_id)
				);
				$result = request($param);
				if($result['Flag'] != 100){
					ShowMsg("进行到".$post['login_pre'].$i."被终止,错误原因:".$result['FlagString'], -1);
				}
			}
			ShowMsg("操作成功", "?module=recharge");
		}
		$temp = "recharge";
		break;
	case 'role':
		$param=array(
				'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$group_id,'IsDetails'=>true),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Desc'=>'获取站信息')
		);
		$res=request($param);
		$userGroupInfo = $res['Result'];
		$param=array(
				'extparam'=>array('Tag'=>'GetGroupRole','RoleShowOne'=>array(1),'RoleShowTwo'=>array(2)),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'GroupId'=>$group_id,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取站下角色')
		);
		$res=request($param);
		$roleList = $res['list'];
		foreach($roleList as $k=>$one){
			if($one['scope'] != 1){
				unset($roleList[$k]);
			}
		}
		
		if($_POST){
			$post = array_map("htmlspecialchars", array_map("addslashes", $_POST));
			$range1 = intval($post['range1']);
			$range2 = intval($post['range2']);
			if($range1 > $range2){
				ShowMsg("填写范围不正确", -1);
			}
			if(!$post['login_pre']){
				ShowMsg("前缀不能为空", -1);
			}
			
			for($i=$range1;$i<=$range2;$i++){
				$parameter = array(
						"extparam"=>array(
								"Tag"=>"GetUser",
								"UserName"=>$post['login_pre'].$i
						)
				);
				$res = httpPOST("core/sso/sso_api.php", $parameter);
				if($res['Flag'] != 100){
					ShowMsg("进行到".$post['login_pre'].$i."被终止,错误原因:".$res['FlagString'], -1);
				}
				
				$param=array(
					'extparam'=>array('Tag'=>'SaveRoleInfo','GroupId'=>$userGroupInfo['groupid'],'Data'=>array("GroupId"=>$group_id, "RoleId"=>intval($post['role_id']), "Uin"=>$res['Uid'], "RoomId"=>0)),
					'param'=>array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10262,'ChildId'=>102,'Desc'=>'添加、更新站内代理')
				);
				$result = request($param);
				if($result['Flag'] != 100){
					ShowMsg("进行到".$post['login_pre'].$i."被终止,错误原因:".$result['FlagString'], -1);
				}
			}
			ShowMsg("操作成功", "?module=role");
		}
		$temp = "role";
		break;
}

$tmp_config = array(
		'template_dir'  => './group_func/',
		'cache_dir'	=>'./group_func/cache/',
		'cache_lifetime'=> 300,
		'debug'		=> true,
);

$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template($temp.'.html',$tpl);