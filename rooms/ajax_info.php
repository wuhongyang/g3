<?php
require_once '../library/global.fun.php';

if($_POST['Tag'] == 'GetLoginUser'){
	$user = httpPOST(SSO_API_PATH,array('param'=>array('GroupId'=>intval($_POST['GroupId'])),'extparam'=>array('Tag'=>'GetLogin')));
    $info = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$user['Uin'],'Status'=>1)));
    $user = array_merge($info, $user);      
	if($user['Flag']==100){
		$user['Money'] = get_money($user['Uin']);
		//获取会员等级
		$userInfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$user['Uin'])));
		$user['VipGrade'] = (int)$userInfo['Vip'];
		$user['Nick'] = stripcslashes($user['Nick']);
		$user['Face70'] = cdn_url(PIC_API_PATH."/uin/{$user['Uin']}/70/70.jpg");
		if($_POST['GroupId']){
			$user['Voucher']=get_money($user['Uin'],intval($_POST['GroupId']));
		}
		//获取渠道角色
		$channelInfo = getChannelUserInfo($user['Uin']);
		$channelCategory = array();
		if(is_array($channelInfo) && !empty($channelInfo)){
			foreach($channelInfo as $key => $val){
				//站长和代理不管
				if(in_array($val['type'], array(7,16))){
					continue;
				}
				$channelCategory[] = $val['type'];
				if($val['type']==15){
					$user['ArtistRoomId']=$val['room_id'];
				}
			}
			sort($channelCategory, SORT_NUMERIC);
			$user['ChannelType'] = $channelCategory[0];
		}
		//得到未读消息
		$param = array(
			'extparam' => array('Tag'=>'Count'),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10035,'ParentId'=>10250,'ChildId'=>101)
		);
		$countOfHandleMatter = request($param);
		$countOfHandleMatter = intval($countOfHandleMatter['Count']);
		$user['Count'] = $countOfHandleMatter;
	}
	echo json_encode($user);


}elseif($_POST['Tag'] == 'GetGroupLoginUser'){
	$groupId=intval($_POST['GroupId']);
	$user = httpPOST(SSO_API_PATH,array('param'=>array('GroupId'=>$groupId),'extparam'=>array('Tag'=>'GetLogin')));
	if($user['Flag']==100){
		$user['Money'] = get_money($user['Uin']);
		$user['Nick'] = stripcslashes($user['Nick']);
		$user['Face70'] = cdn_url(PIC_API_PATH."/uin/{$user['Uin']}/70/70.jpg");
		//哪些数据需要返回
		$flagData=array();
		$flagData['Voucher']=$groupId>0&&intval($_POST['Voucher'])==1?true:false;//返回金币
		$flagData['ArtistRoomId']=intval($_POST['ArtistRoomId'])==1?true:false;//返回艺人所在房间
		$flagData['UnreadMessages']=intval($_POST['UnreadMessages'])==1?true:false;//返回未读消息
		$flagData['FocusUser']=intval($_POST['FocusUser'])==1?true:false;//返回关注
		$flagData['FocusUserArtistRankRuleId']=intval($_POST['FocusUserArtistRankRuleId']);//返回关注用户详情
		$flagData['CollectRoom']=intval($_POST['CollectRoom'])==1?true:false;//返回收藏房间
		$flagData['HistoryRoom']=intval($_POST['HistoryRoom'])==1?true:false;//返回房间观看记录
		$flagData['ArtistRankRuleId']=intval($_POST['ArtistRankRuleId']);//返回艺人角色信息
		$flagData['RichRankRuleId']=intval($_POST['RichRankRuleId']);//返回富豪角色信息
		
		if($flagData['Voucher']){
			$user['Voucher']=get_money($user['Uin'],$groupId);
		}
		if($flagData['ArtistRoomId']){
			$channelInfo=getChannelInfo($user['Uin']);
			$user['ArtistRoomId']=$channelInfo['room_id'];
		}
		if($flagData['UnreadMessages']){
			$param = array(
				'extparam' => array('Tag'=>'Count'),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10035,'ParentId'=>10250,'ChildId'=>101)
			);
			$countOfHandleMatter = request($param);
			$countOfHandleMatter = intval($countOfHandleMatter['Count']);
			$user['Count'] = $countOfHandleMatter;
		}
		if($flagData['FocusUser']){
			$param = array(
				'extparam' => array('Tag'=>'GetFollow'),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10037,'ChildId'=>102,'Uin'=>$user['Uin'])
			);
			$result=request($param);
			if($result['Flag']==100){
				$result=$result['Result'];
				if($flagData['FocusUserArtistRankRuleId']>0){
					$uinArr=array();
					$followUser=array();
					foreach($result as $val){
						$channelInfo=getChannelInfo($val['following']);
						if($channelInfo['room_id']>0){
							$uinArr[]=$val['following'];
						}
					}
					$param=array(
						'extparam'=>array('Tag'=>'ArtistDetail','GroupId'=>$groupId,'RuleId'=>$flagData['FocusUserArtistRankRuleId'],'UinArr'=>$uinArr),
						'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站艺人信息')
					);
					$result=request($param);
					if(!empty($result['Data'])){
						$followUser['UserList']=$result['Data'];
					}
				}
				else{		
					$followUser=array();
					foreach($result as $key=>$val){
						$followUser['UserList'][$val['following']] = $val['following'];
						$data=array(
							'extparam'=>array(
								'Tag'=>'GetUserBasicForUin',
								'Uin'=>$val['following']
							)
						);
						$userInfo=httpPOST(SSO_API_PATH,$data);
						$followUser['UserList'][$val['following']]=array(
							'uin'=>$val['following'],
							'nick'=>$userInfo['baseInfo']['nick']
						);
					}
				}
				$followUser['Total']=count($followUser['UserList']);
				$user['FollowUser']=$followUser;
			}
		}
		if($flagData['CollectRoom']){
			$param=array(
			   'extparam'=>array('Tag'=>'GetCollect'),
			   'param'=>array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10574,'ChildId'=>102,'Uin'=>$user['Uin'])
			);
			$result=request($param);
			if($result['Flag']==100){
				$collectRoom=array();
				foreach($result['roomList'] as $val){
					$collectRoom['RoomList'][$val['id']]=array(
						'id'=>$val['id'],
						'name'=>$val['name'],
						'curuser'=>$val['curuser'],
						'hasplay'=>$val['hasplay'],
						'video_num'=>$val['video_num']
					);
				}
				$collectRoom['Total']=count($result['roomList']);
				$user['CollectRoom']=$collectRoom;
			}
		}
		if($flagData['HistoryRoom']){
			$param = array(
				'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10157,'ChildId'=>101),
				'extparam' => array('Tag'=>'GetHistoryAccess','Uin'=>$user['Uin'],'GroupId'=>$groupId)
			);
			$result=request($param);
			if($result['Flag']==100){
				$result=(array)$result['HistoryAccess'];
				$result=array_slice($result,0,3);
				$footPrint=array();
				foreach($result as $val){
					$footPrint['RoomList'][$val['id']]=array(
						'id'=>$val['id'],
						'name'=>$val['name'],
						'curuser'=>$val['curuser'],
						'hasplay'=>$val['hasplay'],
						'video_num'=>$val['video_num']
					);
				}
				$footPrint['Total']=count($footPrint['RoomList']);
				$user['FootPrint']=$footPrint;
			}
		}
		if($flagData['ArtistRankRuleId']>0){
			$param=array(
				'extparam'=>array('Tag'=>'GetScoreDiff','UinId'=>$user['Uin'],'ExtendUin'=>$groupId,'Ruleid'=>$flagData['ArtistRankRuleId'],"Period"=>"total"),
				'param'=>array('BigCaseId'=>10001,'CaseId'=>10027,'ParentId'=>10074,'ChildId'=>118)
			);
			$result=request($param);
			if($result['Flag']==100){
				$user['ArtistRule']=array(
					'RolesName'=>$result['RolesName'],
					'RolesImg'=>$result['RolesImg'],
					'Weight'=>$result['Weight'],
					'ThisLevelWeight'=>$result['ThisLevelWeight'],
					'NextLevelWeight'=>$result['NextLevelWeight'],
					'Diff'=>$result['Diff']
				);
			}
		}
		if($flagData['RichRankRuleId']>0){
			$param=array(
				'extparam'=>array('Tag'=>'GetScoreDiff','UinId'=>$user['Uin'],'ExtendUin'=>$groupId,'Ruleid'=>$flagData['RichRankRuleId'],"Period"=>"total"),
				'param'=>array('BigCaseId'=>10001,'CaseId'=>10027,'ParentId'=>10074,'ChildId'=>118)
			);
			$result=request($param);
			if($result['Flag']==100){
				$user['RichRule']=array(
					'RolesName'=>$result['RolesName'],
					'RolesImg'=>$result['RolesImg'],
					'Weight'=>$result['Weight'],
					'ThisLevelWeight'=>$result['ThisLevelWeight'],
					'NextLevelWeight'=>$result['NextLevelWeight'],
					'Diff'=>$result['Diff']
				);
			}
		}
	}
	echo json_encode($user);
	exit;
}elseif($_POST['Tag'] == 'GetRoomLoginUser'){
	$user = httpPOST(SSO_API_PATH,array('param'=>array('GroupId'=>$_POST['GroupId']),'extparam'=>array('Tag'=>'GetLogin')));
	// require 'site.inc.php';
	if($user['Flag'] != 100){
		if(isset($_COOKIE['GUEST_LOGIN_TOKEN']) && empty($user['Uin'])){
			$user = json_decode($_COOKIE['GUEST_LOGIN_TOKEN'],true);
			setcookie('GUEST_LOGIN_TOKEN',$_COOKIE['GUEST_LOGIN_TOKEN'],time()+86400*30,'/');
			$user['Token'] = $user['SessionKey'];
		}else{
			$user = array('Flag'=>101,'FlagString'=>'fail');
		}
	}
	// $user = array_merge($user,$site);
	echo json_encode($user);
}elseif($_POST['Tag'] == 'addFocus'){
	$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
	if($user['Flag']!= 100){
		echo json_encode(array('Flag'=>102,'FlagString'=>'请登录'));exit;
	}
	$param = array(
		'extparam' => array('Tag'=>'AddFollow','Uin'=>$user['Uin']),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10037,'ChildId'=>104,'Uin'=>$user['Uin'],'TargetUin'=>array($_POST['Uin']))
	);
	$res = request($param);
	echo json_encode(array('Flag'=>100,'FlagString'=>'关注成功'));exit;
}elseif($_POST['Tag'] == 'cancelFocus'){
	$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
	if($user['Flag']!= 100){
		echo json_encode(array('Flag'=>102,'FlagString'=>'请登录'));exit;
	}
	$param = array(
		'extparam' => array('Tag'=>'MoveFollow','Uin'=>$user['Uin']),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10037,'ChildId'=>101,'Uin'=>$user['Uin'],'TargetUin'=>array($_POST['Uin']))
	);
	$res = request($param);
	if($res['Flag'] == 100){
		$res['FlagString'] = '取消关注成功';
		echo json_encode($res);exit;
	}else{
		echo json_encode(array('Flag'=>102,'FlagString'=>'取消关注失败'));exit;
	}
}elseif($_POST['Tag'] == 'getFocusStatus'){
	$uins = json_decode($_POST['Uins'],true);
	$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
	if($user['Flag']!= 100){
		foreach($uins as $uin){
			$ret[$uin] = 0;
		}
		echo json_encode($ret);exit;
	}
	foreach($uins as $uin){
		if($uin == $user['Uin']){
			$ret[$uin] = -1;
		}else{
			$param = array(
				'extparam' => array('Tag'=>'GetFollowNum','Uin'=>$user['Uin'],'OtherUin'=>$uin),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10036,'ChildId'=>102)
			);
			$fans = request($param);
			$ret[$uin] = intval($fans['Num']);
		}
	}
	echo json_encode($ret);exit;
}elseif($_POST['Tag'] == 'GetFollow'){
	$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
	if($user['Flag']!= 100){
		echo json_encode($user);exit;
	}
	$param = array(
		'extparam' => array('Tag'=>'GetFollow'),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10037,'ChildId'=>102,'Uin'=>$user['Uin'],'TargetUin'=>$_POST['OtherUin'])
	);
	$fans = request($param);
	$fans['Uin'] = $user['Uin'];
	if($fans['Flag'] !== 100){
		echo json_encode($fans);exit;
	}
	$f_result = $fans['Result'];
	unset($fans['Result']);
	$userList=array();
	foreach($f_result as $key=>$value){
		$fans['Result'][$value['following']] = $value['following'];
		$data=array(
			'extparam'=>array(
				'Tag'=>'GetUserBasicForUin',
				'Uin'=>$value['following']
			)
		);
		$userInfo=httpPOST(SSO_API_PATH,$data);
		$userList[$value['following']]=array(
			'uin'=>$value['following'],
			'nick'=>$userInfo['baseInfo']['nick']
		);
	}
	$fans['total']=count($fans['Result']);
	$fans['userList']=$userList;
	echo json_encode($fans);exit;
}elseif($_POST['Tag'] == 'getCities'){ 
	$province_id = intval($_POST['province_id']);
	$c = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCities','ProvinceId'=>$province_id)));
	$cities = json_encode((array)$c['Result']);
	exit($cities);
}elseif($_POST['Tag'] == 'GetSsoInfo'){
	$uin=intval($_POST['Uin']);
	$data=array(
		'extparam'=>array(
			'Tag'=>'GetUserBasicForUin',
			'Uin'=>$uin
		)
	);
	$userInfo=httpPOST(SSO_API_PATH,$data);
	$userInfo['Info']=$userInfo['baseInfo'];
	unset($userInfo['baseInfo']);
	echo json_encode($userInfo);
	exit;
}elseif($_POST['Tag'] == 'GetCollect'){ 
	$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
	if($user['Flag']!= 100){
		echo json_encode(array('Flag'=>112,'FlagString'=>'请登录'));
		exit;
	}
	$param=array(
	   'extparam'=>array('Tag'=>'GetCollect'),
	   'param'=>array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10574,'ChildId'=>102,'Uin'=>$user['Uin'])
	);
	$result=request($param);
	$roomList=array();
	foreach($result['roomList'] as $val){
		$roomList[$val['id']]=$val;
	}
	$result['roomList']=$roomList;
	$result['total']=count($result['roomList']);
	echo json_encode($result);
	exit;
}elseif($_POST['Tag'] == 'CollectRoom'){ 
	$roomId=intval($_POST['RoomId']);
	$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
	if($user['Flag']!= 100){
		echo json_encode(array('Flag'=>112,'FlagString'=>'请登录'));
		exit;
	}
	$param=array(
	   'extparam'=>array('Tag'=>'CollectRoom'),
	   'param'=>array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10574,'ChildId'=>102,'ChannelId'=>$roomId,'Uin'=>$user['Uin'])
	);
	$result=request($param);
	echo json_encode($result);
	exit;
}elseif($_POST['Tag'] == 'CancelRoom'){ 
	$roomId=intval($_POST['RoomId']);
	$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
	if($user['Flag']!= 100){
		echo json_encode(array('Flag'=>112,'FlagString'=>'请登录'));
		exit;
	}
	$param=array(
	   'extparam'=>array('Tag'=>'CancelRoom'),
	   'param'=>array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10574,'ChildId'=>103,'ChannelId'=>$roomId,'Uin'=>$user['Uin'])
	);
	$result=request($param);
	echo json_encode($result);
	exit;
}
elseif($_POST['Tag'] == 'GetUserGroupRole'){
	$groupId=intval($_POST['GroupId']);
	$uin=intval($_POST['Uin']);
	$ruleid=intval($_POST['Ruleid']);
	$roleData=array(
		'extparam'=>array(
			'Tag'=>'GetRole',
			'GroupId'=>$groupId,
			'Uin'=>$uin,
			'Ruleid'=>$ruleid
		)
	);
/*	if($_POST['Type']=='artist'){
		$channelInfo=getChannelInfo($uin,0,15);
		if($channelInfo['Flag']!=100){
			$result=array('Flag'=>101,'FlagString'=>'失败');
		}
		$roleData['extparam']['ChannelId']=$channelInfo['room_id'];
	}*/
	$result=httpPOST(ROLE_API_PATH,$roleData);
	echo json_encode($result);
	exit;
}
elseif($_POST['Tag'] == 'GetUserGroupPoint'){
	$groupId=intval($_POST['GroupId']);
	$uin=intval($_POST['Uin']);
	$ruleid=intval($_POST['Ruleid']);
	$param=array(
		'extparam'=>array('Tag'=>'GetScoreDiff','UinId'=>$uin,'ExtendUin'=>$groupId,'Ruleid'=>$ruleid,"Period"=>"total"),
		'param'=>array('BigCaseId'=>10001,'CaseId'=>10027,'ParentId'=>10074,'ChildId'=>118)
	);
	$result=request($param);
	echo json_encode($result);
	exit;
}