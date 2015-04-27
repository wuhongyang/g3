<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'group_info':$_GET['module'];

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

//站详情
$param=array(
	'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$groupId,'IsDetails'=>true),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'获取站信息')
);
$userGroupInfo=request($param);
if($userGroupInfo['Flag']!=100){
	alertMsg($userGroupInfo['FlagString'],'/');
}
$userGroupInfo=$userGroupInfo['Result'];
$userGroupInfo['images']=(array)json_decode($userGroupInfo['images'],true);

//是否拥有站管理权限
if(checkGroupPermission(10258,$permission)){
	$groupManage=true;
}
//是否拥有站税收查看权限
if(checkGroupPermission(10259,$permission)){
	$imformation=true;
}
//是否拥有站内配置查看权限
if(checkGroupPermission(10315,$permission)){
	$groupConfig=true;
}

switch($module){
	case 'group_info':
		//权限判断
		if(!$groupManage){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title='站点信息-基本信息';
		$template='group_info';
		$serviceType='group_manage';
		
		//获取roomid
		// $param=array(
			// 'extparam'=>array('Tag'=>'GetGroupRooms','GroupId'=>$userGroupInfo['groupid']),
			// 'param'=>array('BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10260,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取站下房间')
		// );
		// $userRooms=request($param);
		// $roomIds=array();
		// foreach($userRooms['roomList'] as $val){
			// $roomIds[]=$val['id'];
		// }
		
		//获取用户余额
		$param = array(
			'extparam' => array('Tag'=>"GetTaxBalance",'GroupId'=>$userGroupInfo['groupid']),
			'param' => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10263,'ChildId'=>102)
		);
		$rBalance = httpPOST(KMONEY_API_PATH,$param);
		$balance=(int)$rBalance['LastBalance'];
		//站本月税收
	//	$tax=$userGroupInfo['tax'];
		
		$param=array(
				'extparam'=>array('Tag'=>"GetPointVaule",'ExtendUin'=>$userGroupInfo['groupid'],'Ruleid'=>33,'Period'=>'month'),
				'param'=>array('BigCaseId'=>10001,'CaseId'=>10027,'ParentId'=>10074,'ChildId'=>113,'Desc'=>"群积分查询")
		);
		$rBalance=request($param);
		$income=$balance;
		$money=$rBalance['Weight'];

		$data=array(
			'Type'=>array(9,15),
			'UpUid'=>$userGroupInfo['groupid']
		);
		$param=array(
			'extparam'=>array('Tag'=>'GetSignedList','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10261,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取用户旗下房间室主艺人')
		);
	//	$signedList=request($param);
		// $signedListTmp=array();
		// if(!empty($signedList['userList'])){
			// foreach($signedList['userList'] as $val){
				// $signedListTmp[$val['uid'].'_'.$val['room_id']]=$val;
			// }
		// }
		
		/*$param=array(
			'extparam'=>array('Tag'=>'SignatoryDetails','Data'=>array('Uin'=>$userGroupInfo['uin'],'Type'=>4,'Role'=>3)),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10265,'ChildId'=>101)
		);
		$signedListTax=request($param);
		if(!empty($signedListTax['Result'])){
			foreach($signedListTax['Result'] as $val){
				if(isset($signedListTmp[$val['Uin'].'_'.$val['ChannelId']])){
					$signedListTmp[$val['Uin'].'_'.$val['ChannelId']]['weight']=$val['Weight'];
				}
			}
		}*/
	//	$signedList['userList']=$signedListTmp;
		unset($signedListTmp);
	break;
	case 'group_config':
		//权限判断
		if(!$groupConfig){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title = '业务配置 - 人气票设置';
		if($_POST){
			unset($_POST['hv']);
			$param=array(
					'extparam'=>array('Tag'=>'SCSave2','Data'=>array('GroupId'=>$groupId, 'BigCaseId'=>10001, 'CaseId'=>10027, 'ParentId'=>10272, 'Post'=>$_POST)),
					'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10315,'ChildId'=>102,'Desc'=>'人气票配置保存')
			);
			$result=request($param);
			alertMsg($result['FlagString'], "group.php?module=group_config");
		}
		$param=array(
				'extparam'=>array('Tag'=>'SCGet','Data'=>array('GroupId'=>$groupId, 'BigCaseId'=>10001, 'CaseId'=>10027, 'ParentId'=>10272)),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10315,'ChildId'=>101,'Desc'=>'人气票配置查看')
		);
		$result=request($param);
		$data = json_decode($result['Data'], true);
		$template = "group_config";
	break;
	case 'group_proxy':
		//权限判断
		if(!checkGroupPermission(10262,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title='签约管理-赋予角色';
		$template='group_proxy';
		$serviceType='signed_manage';
		
		//站下角色
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupRole','RoleShowOne'=>array(1),'RoleShowTwo'=>array(2)),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'GroupId'=>$groupId,'Desc'=>'获取站下角色')
		);
		$roleList=request($param);
		$roleList=(array)$roleList['list'];
		
		//搜索条件
		if(intval($_GET['signed_uin'])>0){
			$data['Uin'] = intval($_GET['signed_uin']);
		}
		if(intval($_GET['RoomId'])>0){
			$data['RoomId'] = intval($_GET['RoomId']);
		}
		if(intval($_GET['role_id'])>0){
			$roleIds[] = intval($_GET['role_id']);
		}else{
			foreach ($roleList as $val){
				$roleIds[] = intval($val['id']);
			}
		}
		
		$param=array(
			'extparam'=>array('Tag'=>'GetRoleList','GroupId'=>$userGroupInfo['groupid'], 'RoleIds'=>(array)$roleIds, 'Data'=>$data),
			'param'=>array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10262,'ChildId'=>101,'Desc'=>'查看站内代理')
		);
		$proxyList=request($param);
		foreach ($roleList as $key=>$val){
			foreach ($proxyList['Result'] as $key1=>$val1){
				if($val['id'] == $val1['RoleId']){
					$proxyList['Result'][$key1]['role'] = $val['name'];
				}
			}
		}
		foreach ((array)$proxyList['Result'] as $k=>$v){
			$result = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$v['Uin'])));
			$proxyList['Result'][$k]['nick'] = empty($result['Nick']) ? $v['uin'] : $result['Nick'];
		}
	break;
	case 'group_proxy_add':
		//权限判断
		if(!checkGroupPermission(10262,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title='签约管理-赋予角色';
		$template='group_proxy_add';
		$serviceType='signed_manage';
		
		//代理信息
		$param=array(
			'extparam'=>array('Tag'=>'GetRoleInfo','Id'=>$_GET['id'],'GroupId'=>$userGroupInfo['groupid']),
			'param'=>array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10262,'ChildId'=>101,'Desc'=>'查看站内代理')
		);
		$proxyInfo=request($param);
		$proxyInfo=$proxyInfo['proxyInfo'];
		
		//站下房间
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupRooms','GroupId'=>$userGroupInfo['groupid']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10260,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取站下房间')
		);		
		$roomList=request($param);
		$roomList=$roomList['roomList'];
		
		//站下角色
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupRole','RoleShowOne'=>array(1),'RoleShowTwo'=>array(2)),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取站下角色','GroupId'=>$groupId)
		);
		$roleList=request($param);
		$roleList=$roleList['list'];
	break;
	case 'group_proxy_submit':
		//权限判断
		if(!checkGroupPermission(10262,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$param=array(
			'extparam'=>array('Tag'=>'SaveRoleInfo','GroupId'=>$userGroupInfo['groupid'],'Data'=>$_POST),
			'param'=>array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10262,'ChildId'=>102,'Desc'=>'添加、更新站内代理')
		);
		$result=request($param);
		if($result['Flag']==100){
			ShowMsg($result['FlagString'],'group.php?module=group_proxy');
		}else{
			ShowMsg($result['FlagString'],-1);
		}
	break;
	case 'group_proxy_remove':
		//权限判断
		if(!checkGroupPermission(10262,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title='签约管理-站代理设置';
		$param=array(
			'extparam'=>array('Tag'=>'RemoveRoleInfo','Id'=>$_GET['id'],'GroupId'=>$userGroupInfo['groupid'],'PackageId'=>$userGroupInfo['package_id'], 'RoleId'=>$_GET['roleid']),
			'param'=>array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10262,'ChildId'=>101,'Desc'=>'删除站内代理')
		);
		$result=request($param);
		if($result['Flag']==100){
			ShowMsg($result['FlagString'],'group.php?module=group_proxy');
		}else{
			ShowMsg($result['FlagString'],-1);
		}
	break;
	case 'group_update':
		$template = 'group_update';
	break;
	case 'game_robot':
		if(!empty($_POST)){
			$rst = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'SetInteractGropuConfig','Interact'=>(int)$_POST['interact'],'Groupid'=>$groupId,'Robot'=>(int)$_POST['robot'])));
			alertMsg($rst['FlagString'],'?module=game_robot');
		}
		$title = '业务配置 - 游戏机器人配置';
		$interact = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'InteractList','Data'=>array('robot'=>1,'interact_status'=>1))));
		foreach($interact['list'] as $key=>$list){
			$interact_group = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetInteractGropuConfig','Interact'=>$list['id'],'Groupid'=>$groupId)));
			$list['group_robot'] = (int)$interact_group['Result']['robot'];
			$interact['list'][$key] = $list;
		}
		$status = array('已关闭','已开启');
		$button = array('开启','关闭');
		$template = 'game_robot';
		break;
}

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('group_manage/'.$template.'.html',$tpl);
