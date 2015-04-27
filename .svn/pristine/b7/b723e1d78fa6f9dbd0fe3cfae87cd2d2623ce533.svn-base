<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'signed_manage':$_GET['module'];

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
	'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'获取站信息')
);
$userGroupInfo=request($param);
if($userGroupInfo['Flag']!=100){
	alertMsg($userGroupInfo['FlagString'],'/');
}
$userGroupInfo=$userGroupInfo['Result'];

//是否有签约管理的权限
if(checkGroupPermission(10261,$permission)){
	$signedManage=true;
}

//是否有站代理的权限
if(checkGroupPermission(10262,$permission)){
	$groupProxy=true;
}
//是否有艺人守护的权限
if(checkGroupPermission(10273,$permission)){
	$groupGuardian=true;
}

//站角色
$param=array(
		'extparam'=>array('Tag'=>'GetGroupRole','RoleShowOne'=>array(1),'RoleShowTwo'=>array(1, 3)),
		'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'GroupId'=>$groupId,'Desc'=>'获取站下角色')
);
$roleList=request($param);
$roleList=(array)$roleList['list'];
$rlist = array();
foreach ($roleList as $key => $val) {
	$rlist[$val['role_show_2']] = $val['id'];
}
switch($module){
	case 'signed_manage':
		//权限判断
		if(!$signedManage){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title='签约管理-已签约人员';
		$template='signedmanage';
		
		$data=array(
			'Type'=>array(9,15),
			'UpUid'=>$userGroupInfo['groupid']
		);
		if(intval($_GET['signed_uin'])>0){
			$data['Uid']=intval($_GET['signed_uin']);
		}
		if(intval($_GET['RoomId'])>0){
			$data['RoomId']=intval($_GET['RoomId']);
		}
		if(intval($_GET['role_id'])>0){
			$data['role_id']=intval($_GET['role_id']);
		}
		$param=array(
			'extparam'=>array('Tag'=>'GetSignedList','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10261,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取用户旗下房间室主艺人')
		);
		$signedList=request($param);
	break;
	case 'signed':
		//权限判断
		if(!$signedManage){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title='签约管理-签约人员绑定房间';
		$template='signed';
		
		//站房间
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupRoomsList','GroupId'=>$userGroupInfo['groupid'], "no_page"=>true),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取站下房间')
		);
		$userRooms=request($param);
		if(empty($userRooms['roomList'])){
			ShowMsg('站下没有房间','index.php');
		}
		
		foreach($roleList as $key=>$val){
			if(preg_match('#室主#',$val['name'])){
				$roleList[$key]['type']=1;
			}
			elseif(preg_match('#艺人#',$val['name'])){
				$roleList[$key]['type']=2;
			}
		}
		
	break;
	case 'signed_submit':
		//权限判断
		if(!$signedManage){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$data=array(
			'PartnerUin'=>intval($_POST['signed_uin']),//签约人员id
			'RoomId'=>intval($_POST['room_id']),//房间id
			'Type'=>intval($_POST['signed_type']),//签约类型：自定义1.室主 2.艺人
			'RoleId'=>intval($_POST['role_id']),//角色id
			'GroupId'=>$groupId
		);
		$row = getGroupVip($data['PartnerUin'],$groupId);
		if(empty($row)){
			echo json_encode(array('Flag'=>101,'FlagString'=>'不是本站会员，不能添加'));
			exit;
		}
		foreach ($roleList as $key=>$val){
			if($val['id'] == $data['RoleId'])
				$data['role_name'] = $val['name'];
		}
		$param=array(
			'extparam'=>array('Tag'=>'PartnerSigned','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10261,'ChildId'=>102,'Uin'=>$userGroupInfo['uin'],'Desc'=>'签约室主或艺人')
		);
		$result=request($param);
		if($result['Flag']!=100){
			echo json_encode($result);
			exit;
		}
		if(intval($_POST['role_id'])==10537){
			$param = array(
					'extparam' => array('Tag'=>'EditArtistSalary','Salary'=>intval($_POST['salary']),'Uin'=>intval($_POST['signed_uin']),'RoomId'=>intval($_POST['room_id']),'GroupId'=>$groupId),
					'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10738,'ChildId'=>102)
			);
			$result = request($param);
			if($result['Flag']==100){
				$result['FlagString']='操作成功';
			}
		}
		echo json_encode($result);
		exit;
	break;
	case 'signed_list':
		//权限判断
		if(!$signedManage){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title='签约管理-签约人员账户';
		$template='signedlist';
		
		$data=array(
			'Type'=>array(9,15),
			'UpUid'=>$userGroupInfo['groupid']
		);
		if($_GET['type']==1){
			$data['Type']=array(9);
		}
		elseif($_GET['type']==2){
			$data['Type']=array(15);
		}
		if(intval($_GET['signed_uin'])>0){
			$data['Uid']=intval($_GET['signed_uin']);
		}
		$param=array(
			'extparam'=>array('Tag'=>'GetSignedList','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10261,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取用户旗下房间室主艺人')
		);
		$signedList=request($param);
		foreach($signedList['userList'] as $key=>$val){
			// $param=array(
				// 'extparam'=>array('Tag'=>'InfoForUin','Uin'=>$val['uid']),
				// 'param'=>array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10247,'ChildId'=>101)
			// );
			// $info=request($param);
			$param=array(
				'extparam'=>array('Tag'=>'Account','Uin'=>$val['uid']),
				'param'=>array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10234,'ChildId'=>103)
			);
			$info=request($param);
			$signedList['userList'][$key]['bank_id']=$info['Info']['bank_id'];
			$signedList['userList'][$key]['bank_name']=$info['Info']['bankName'];
			$signedList['userList'][$key]['bank_address']=$info['Info']['bank_address'];
			$signedList['userList'][$key]['name']=$info['Info']['name'];
			$signedList['userList'][$key]['idcard']=$info['Info']['idcard'];
		}
	break;
	case 'termination':
		//权限判断
		if(!$signedManage){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title='签约管理-解约人员';
		$template='termination';
		
		$id=intval($_REQUEST['id']);
		if($id<=0){
			ShowMsg('error','index.php');
		}
		$param=array(
			'extparam'=>array('Tag'=>'GetUserSignedList','Data'=>array('Id'=>$id)),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10246,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'用户签约的渠道')
		);
		$signedInfo=request($param);
		if(empty($signedInfo['total'])){
			ShowMsg('该用户已被冻结','index.php');
		}
		$signedInfo=$signedInfo['userList'][0];
	break;
	case 'termination_submit':
		//权限判断
		if(!$signedManage){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$id=intval($_POST['id']);
		$newPartnerType=intval($_POST['new_partner_type']);
		$newPartnerUin=intval($_POST['new_partner_uin']);
		if($newPartnerType==1){
			$newPartnerUin=$userGroupInfo['uin'];
		}
		if($id<=0){
			ShowMsg('error','index.php');
		}
		$data=array(
			'GroupId'=>$userGroupInfo['groupid'],
			'ChannelId'=>$id,
			'Type'=>1,
			'NewPartnerUin'=>$newPartnerUin
		);
		$param=array(
			'extparam'=>array('Tag'=>'PartnerTermination','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10261,'ChildId'=>103,'Uin'=>$userGroupInfo['uin'],'Desc'=>'解约室主或艺人')
		);
		$result=request($param);
		echo json_encode($result);
		exit;
	break;
	case 'termination_success':
		//权限判断
		if(!$signedManage){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title='签约管理-解除签约成功';
		$template='terminationsuccess';
		
		$id=intval($_REQUEST['id']);
		if($id<=0){
			ShowMsg('error','index.php');
		}
		$newPartnerUin=intval($_REQUEST['new_partner_uin']);
		if(empty($newPartnerUin)){
			$newPartnerUin=$userGroupInfo['uin'];
		}
		
		$param=array(
			'extparam'=>array('Tag'=>'GetTerminationRecordsInfo','Id'=>$id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10261,'ChildId'=>104,'Uin'=>$userGroupInfo['uin'],'Desc'=>'查看解约历史详情')
		);
		$result=request($param);
		if($result['Flag']!=100){
			ShowMsg($result['FlagString'],'signed_manage.php?module=signed_list');
		}
		$records=$result['Records'];
		if($records['group_uin']!=$userGroupInfo['uin']){
			ShowMsg('error','signed_manage.php?module=signed_list');
		}
		//得到通行证信息
		if($records['channel_type']==9){
			$data=array(
				'extparam'=>array(
					'Tag'=>'GetUserBasicForUin',
					'Uin'=>$newPartnerUin
				)
			);
			$newPartnerInfo=httpPOST(SSO_API_PATH,$data);
			$newPartnerInfo=$newPartnerInfo['baseInfo'];
		}
	break;
	case 'termination_list':
		//权限判断
		if(!$signedManage){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title='签约管理-已解约人员';
		$template='terminationlist';
		
		$data=array(
			'GroupUin'=>$userGroupInfo['uin']
		);
		if(intval($_GET['signed_uin'])>0){
			$data['Uin']=intval($_GET['signed_uin']);
		}
		if(intval($_GET['RoomId'])>0){
			$data['RoomId']=intval($_GET['RoomId']);
		}
		if(intval($_GET['role_id'])>0){
			$data['role_id']=intval($_GET['role_id']);
		}
		$param=array(
			'extparam'=>array('Tag'=>'GetTerminationRecordsList','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10261,'ChildId'=>104,'Uin'=>$userGroupInfo['uin'],'Desc'=>'查看解约历史')
		);
		$signedList=request($param);
	break;
	case 'guardian':
		//权限判断
		if(!checkGroupPermission(10273,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title='签约管理-艺人守护列表';
		//站下房间
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupRooms','GroupId'=>$userGroupInfo['groupid']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10260,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取站下房间')
		);		
		$userRooms=request($param);
		$roomId=$userRooms['roomList'][0]['id'];
		
		if(isset($_GET['del'])){
			unset($guardian[$_GET['del']]);
			$guardian = array_values($guardian);
			$param = array(
				'extparam' => array('Tag'=>"SetGuardian",'Guardians'=>$guardian),
				'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10246,'ChildId'=>101,'ChannelId'=>$roomId,'Desc'=>"守护者保存")
			);
			$result = request($param);
			if($result['Flag'] == 100){
				alertMsg('删除成功','?module=guardian');
			}else{
				alertMsg($result['FlagString'],-1);
			}
		}
		
		//保存守护者
		if(!empty($_POST['guardian'])){
			$guardian = array();
			foreach($_POST['guardian'] as $key=>$val){
				foreach($val as $k=>$v){
					$guardian[$k][$key] = $v;
				}
			}
			
			$param = array(
				'extparam' => array('Tag'=>"SetGuardian",'Guardians'=>$guardian),
				'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10246,'ChildId'=>101,'ChannelId'=>$roomId,'Desc'=>"守护者保存")
			);
			$result = request($param);
			if($result['Flag'] == 100){
				alertMsg('保存成功','?module=guardian');
			}else{
				alertMsg($result['FlagString'],'?module=guardian');
			}
		}
		
		$guardian = (array)json_decode($userRooms['roomList'][0]['guardian'],true);
		$curdate = date('Y-m-d');
		foreach($guardian as $key=>$val){
			if($val['Start'] > $curdate || $val['End'] < $curdate || $val['Start'] > $val['End']){
				$guardian[$key]['dated'] = 1;
			}else{
				$guardian[$key]['dated'] = 0;
			}
		}
		
		//获取签约艺人列表
		$param=array(
			'extparam'=>array('Tag'=>'GetSignedList','Data'=>array('Type'=>array(15),'UpUid'=>$userGroupInfo['groupid'])),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10261,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取用户旗下房间室主艺人')
		);
		$signedList=request($param);
		$signedList = $signedList['userList'];
		$template = 'guardian';
		break;
	case 'apply_list':
		$s_time = isset($_GET['stime']) ? $_GET['stime'] : date('Y-m').'-01 00:00:00';
		$e_time = isset($_GET['etime']) ? $_GET['etime'] : date('Y-m-d H:i:s');
		$_GET['stime'] = $s_time;
		$_GET['etime'] = $e_time;
		$param=array(
			'extparam'=>array('Tag'=>'GetApplyList','GroupId'=>$userGroupInfo['groupid'],'SearchData'=>$_GET),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10261,'ChildId'=>101,'Desc'=>'查看室主/艺人申请资料')
		);
		$res = request($param);
		$page = $res['Page'];
		$res = $res['Result'];
		$template = 'apply_list';

		$sign_status = array(1 => '已签约', 2 => '未签约');
		break;
}

$serviceType='signed_manage';
$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('signed_manage/'.$template.'.html',$tpl);