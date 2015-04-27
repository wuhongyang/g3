<?php
header("cache-control:no-cache,must-revalidate");
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';
$GroupData = domain::main()->GroupData();
$user = checkLogin();
$group_id = intval($GroupData['groupid']);
//模板
$themes = $GroupData['Template']!='' ? $GroupData['Template'] : 'default';

//扩展信息
$GroupData['EXT']=json_decode($GroupData['EXT'], true);

if($GroupData['Template'] == 'aisvv' || $GroupData['Template'] == 'kaichang'){
	$res = httpPOST(ROLE_API_PATH,array('extparam'=>array('Tag'=>'GetRole','Uin'=>$user['Uin'],'GroupId'=>$group_id, "RoleId"=>array(10534, 10185))));
	$user_fund_role = $res['Roles']?$res['Roles']:array();
}elseif($GroupData['Template'] == 'cc51'){
	$res = httpPOST(ROLE_API_PATH,array('extparam'=>array('Tag'=>'GetRole','Uin'=>$user['Uin'],'GroupId'=>$group_id, "RoleId"=>array(10535))));
	$user_fund_role = $res['Roles']?$res['Roles']:array();
}

//渠道身份
$channelList=getChannelUserInfo($user['Uin']);
$userChannel=array();
if(!empty($channelList)){
	foreach($channelList as $val){
		if($val['type']==8){
			$userChannel['GroupManage']=true;
		}
		if($val['up_uid']==$group_id&&($val['type']==9||$val['type']==15)){
			$param=array(
				'extparam'=>array('Tag'=>"RoomInfo",'RoomId'=>$val['room_id']),
				'param'=>array('GroupId'=>$group_id,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10099,'ChildId'=>101,'Desc'=>"房间信息查询")
			);
			$result=request($param);
			if($result['Flag']==100){
				if($val['type']==9){
					$userChannel['ManageRoom'][]=array(
						'id'=>$result['RoomInfo']['id'],
						'name'=>$result['RoomInfo']['name']
					);
				}
				elseif($val['type']==15){
					$userChannel['ArtistRoom'][]=array(
						'id'=>$result['RoomInfo']['id'],
						'name'=>$result['RoomInfo']['name']
					);
				}
			}
		}
	}
}
unset($channelList);
//是否艺人
$isArtist = empty($userChannel['ArtistRoom']) ? false : true;