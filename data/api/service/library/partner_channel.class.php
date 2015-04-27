<?php

/**
 *   渠道操作接口
 *   文件: partner_channel.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class PartnerChannel
{
	//数据库指针
	protected $db = null;

	//构造函数
	public function __construct() {
		$this->db = db::connect(config('database','default'));
		$this->group_mysql_db = domain::main()->GroupDBConn();
	}
	
	public function setGuardian($roomid,$guardians){
		foreach($guardians as $key=>$val){
			$array[$val['Art']][$key] = $val;
			foreach($guardians as $k=>$v){
				if($val['Art'] == $v['Art'] && $key != $k && ($v['End'] >= $val['Start'] && $v['Start'] <= $val['End'])){
					return array('Flag'=>101,'FlagString'=>"{$val['Art']}的守护者在同一时间段内已经重复！");
				}
			}
		}
		$data = json_encode($guardians);
		$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET guardian='{$data}' WHERE id={$roomid}";
		
		if( ! $this->group_mysql_db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'保存失败');
		}else{
			return array('Flag'=>100,'FlagString'=>'保存成功');
		}
	}
	
	/**
	 *   站下渠道用户列表
	 *   @param	array $data 查询所需要的数据
	 *   @return array $array 返回需要查找站下渠道用户列表
	 */
	public function getSignedList($data){
		$result=$this->getChannelUser($data);
		if(empty($result['userList'])){
			return $result;
		}
		foreach($result['userList'] as $key=>$val){
			$role_id = $val['role_id'];
			if(!$role_id && $val['type'] == 9) $role_id = 10185;
			if(!$role_id && $val['type'] == 15) $role_id = 10186;
			$sql0 = "SELECT name FROM ".DB_NAME_TPL.".role WHERE id=".$role_id;
			$result['userList'][$key]['role_name'] = $this->db->get_var($sql0);
			
			$data=array(
				'extparam'=>array(
					'Tag'=>'GetUserBasicForUin',
					'Uin'=>$val['uid']
				)
			);
			$userInfo=httpPOST(SSO_API_PATH,$data);
			if(!empty($userInfo['baseInfo'])){
				$result['userList'][$key]['info']=$userInfo['baseInfo'];
			}
		}
		return $result;
	}
	
	/**
	 *   用户签约的渠道
	 *   @param	array $data 查询所需要的数据
	 *   @return array $array 返回用户签约的渠道列表
	 */
	public function getUserSignedList($data){
		$uin=$data['Uin'];
		$id=$data['Id'];
		if((!is_numeric($uin)||$uin<=0)&&(!is_numeric($id)||$id<=0)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$data=array(
			'Uid'=>$uin,
			'Type'=>array(9,15),
			'RoomId'=>'all',
			'Id'=>$id
		);
		$result=$this->getChannelUser($data);
		if(empty($result['userList'])){
			return $result;
		}
		foreach($result['userList'] as $key=>$val){
			$role_id = $val['role_id'];
			if(!$role_id && $val['type'] == 9) $role_id = 10185;
			if(!$role_id && $val['type'] == 15) $role_id = 10186;
			$sql0 = "SELECT name FROM ".DB_NAME_TPL.".role WHERE id=".$role_id;
			$result['userList'][$key]['role_name'] = $this->db->get_var($sql0);
			
			$sql="SELECT * FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id=".$val['room_id'];
			$roomInfo=$this->group_mysql_db->get_row($sql,"ASSOC");
			if(!empty($roomInfo)){
				$result['userList'][$key]['roomInfo']=$roomInfo;
			}
			$result['userList'][$key]['uptime']=date('Y-m-d H:i:s',$val['uptime']);
			
			$data=array(
				'extparam'=>array(
					'Tag'=>'GetUserBasicForUin',
					'Uin'=>$val['uid']
				)
			);
			$userInfo=httpPOST(SSO_API_PATH,$data);
			if(!empty($userInfo['baseInfo'])){
				$result['userList'][$key]['userInfo']=$userInfo['baseInfo'];
			}
		}
		
		return $result;
	}
	
	/**
	 *   渠道用户列表
	 *   @param	array $data 查询所需要的数据
	 *   @return array $array 返回需要查找渠道用户列表
	 */
	public function getChannelUser($data){
		$where='flag=1';
		if(intval($data['Id'])>0){
			$where.=" AND id=".$data['Id'];
		}
		if(intval($data['PartnerId'])>0){
			$where.=" AND partner_id=".$data['PartnerId'];
		}
		if(intval($data['RegionId'])>0){
			$where.=" AND region_id=".$data['RegionId'];
		}
		if(is_array($data['Type'])&&!empty($data['Type'])){
			$type=implode(',',$data['Type']);
			if(!empty($type)){
				$where.=" AND type IN ($type)";
			}
		}
		if(intval($data['Uid'])>0){
			$where.=" AND uid=".$data['Uid'];
		}
		if(intval($data['UpUid'])>0){
			$where.=" AND up_uid=".$data['UpUid'];
		}
		if($data['RoomId']==='all'){
			$where.=" AND room_id>0";
		}
		else{
			if(intval($data['RoomId'])>0){
				$where.=" AND room_id=".$data['RoomId'];
			}
		}
		if(intval($data['role_id']) > 0){//有些原始数据没有写入role_id,有些室主没有写入角色id
			$where .= " AND role_id=".$data['role_id'];	
		}
		
		$sql="SELECT COUNT(*) FROM ".DB_NAME_PARTNER.".channel_user WHERE ".$where;
		$count=$this->group_mysql_db->get_var($sql);
		if($count<=0){
			return array('Flag'=>100,'FlagString'=>'没有数据');
		}

		$pageArr=$this->showPage($count);
		
		$sql="SELECT * FROM ".DB_NAME_PARTNER.".channel_user WHERE ".$where." ORDER BY id DESC LIMIT ".$pageArr['limit'];
		//$sql="SELECT a.*,b.name as role_name from ".DB_NAME_PARTNER.".channel_user a left join ".DB_NAME_PERMISSION.".role b on a.role_id=b.id WHERE ".$where." ORDER BY ID DESC LIMIT ".$pageArr['limit'];
		$list=$this->group_mysql_db->get_results($sql,'ASSOC');
		
		return array('Flag'=>100,'FlagString'=>'渠道用户列表','userList'=>$list,'page'=>$pageArr['page'],'total'=>$count);
	}
	
	/**
	 *   签约室主或艺人,Type=1室主,Type=3艺人,数据库中type=8站长,type=9室主,type=15艺人
	 *   @param	array $data 签约需要的数据
	 *   @return array $array 返回结果
	 */
	public function partnerSigned($data,$isTransaction=true){
		if(!is_numeric($data['PartnerUin'])||$data['PartnerUin']<=0){
			return array('Flag'=>101,'FlagString'=>'ID不存在');
		}
		if(!is_numeric($data['RoomId'])||$data['RoomId']<=0){
			return array('Flag'=>101,'FlagString'=>'房间ID不能为空');
		}
		if(!in_array($data['Type'],array(1,3))){
			return array('Flag'=>101,'FlagString'=>'签约绑定类型不能为空');
		}
		if(!is_numeric($data['RoleId'])||$data['RoleId']<=0){
			return array('Flag'=>101,'FlagString'=>'签约绑定类型不能为空');
		}
		
		$roomCommon=new RoomCommon();
		$roomInfo=$roomCommon->get_roominfo($data['RoomId']);
		if(empty($roomInfo)){
			return array('Flag'=>102,'FlagString'=>'房间不存在或被冻结');
		}
		if($roomInfo['ownuin']>0&&$data['Type']==1){
			return array('Flag'=>103,'FlagString'=>'该房间已有室主');
		}
		if($roomInfo['ownuin']<=0&&$data['Type']==3){
			return array('Flag'=>103,'FlagString'=>'该房间没有室主,不能签约艺人');
		}
		
		//验证角色
		$module_id = domain::main()->GroupKeyVal($roomInfo['group'], "module_id");
        $sql = "SELECT cate_id FROM ".DB_NAME_TPL.".`role_cate` WHERE tpl_id = ".$module_id;
        $res = $this->db->get_results($sql,"ASSOC");
		$cate_id_arr = array();
		foreach($res as $one){
			$cate_id_arr[] = $one['cate_id'];
		}
        $sql = "SELECT id FROM ".DB_NAME_TPL.".`role` WHERE cate_id IN (".join(",", $cate_id_arr).")";
        $res = $this->db->get_results($sql, "ASSOC");
        $roles_id = array();
        foreach($res as $one){
            $roles_id[] = $one['id'];
        }
        if(!in_array($data['RoleId'],$roles_id)){
			return array('Flag'=>102,'FlagString'=>'该站没有这个角色');
		}
		//$partnerList=getChannelUserInfo($data['PartnerUin']);
		//if(empty($partnerList)){
		//	if($data['Type']==1){
		//		return array('Flag'=>104,'FlagString'=>'该用户不是室主');
		//	}
		//	elseif($data['Type']==3){
		//		return array('Flag'=>104,'FlagString'=>'该用户不是艺人');
		//	}
		//}
		//被签约者拥有的角色信息
		//$userRole=array(
		//	'id'=>0,//渠道用户表主键id,下面更新操作需要
		//	'storeManagerGroupId'=>'',//站长,站ID
		//	'isRoomManager'=>false,//是否签约了室主
		//	'managerRoomList'=>array(),//室主,房间id列表
		//	'entertainerRoomId'=>'',//艺人,房间id
		//	'entertainerGroupId'=>'',//艺人,站ID
		//	'isRoomEntertainer'=>false//是否签约了艺人
		//);
		//foreach($partnerList as $val){
		//	if($val['type']==8){
		//		$channelUserInfo=$val;//渠道用户信息
		//		$group=new GroupManage();
		//		$groupInfo=$group->getGroupInfo($data['PartnerUin']);
		//		if($groupInfo['Flag']==100){
		//			$userRole['storeManagerGroupId']=$groupInfo['Result']['groupid'];
		//		}
		//	}
		//	elseif($val['type']==9){
		//		$userRole['isRoomManager']=true;
		//		$channelUserInfo=$val;//渠道用户信息
		//		if($val['room_id']>0){
		//			$userRole['managerRoomList'][]=$val['room_id'];
		//		}
		//		if($data['Type']==1){
		//			$userRole['id']=$val['id'];
		//		}
		//	}
		//	elseif($val['type']==15){
		//		$userRole['isRoomEntertainer']=true;
		//		if($val['room_id']>0){
		//			$userRole['entertainerRoomId']=$val['room_id'];
		//		}
		//		if($val['up_uid']>0){
		//			$userRole['entertainerGroupId']=$val['up_uid'];
		//		}
		//		if($data['Type']==3){
		//			$userRole['id']=$val['id'];
		//		}
		//	}
		//}
		//用户有站长和室主身份，该室主应不能被签约到其他站的房间（艺人也相同处理）
		//if($userRole['storeManagerGroupId']>0&&$userRole['storeManagerGroupId']!=$roomInfo['group']){
		//	return array('Flag'=>105,'FlagString'=>'该用户是站长,不能被签约');
		//}

		//是否存在申请资料
		//require_once __ROOT__.'/api/rooms/library/join.class.php';
		//$join = new join();
		//$rst = $join->checkApply($data['PartnerUin']);
		//if($rst['Flag'] != 100){
		//	return array('Flag'=>102,'FlagString'=>'不存在申请资料');
		//}
		
		//签约室主
		if($data['Type']==1){
			//$hasRoomer = getChannelRoleInfo(9,$data['RoomId']);
			//if(!empty($hasRoomer)){
			//	return array('Flag'=>103,'FlagString'=>'该房间已经有室主');
			//}
			//if($userRole['isRoomManager']===false&&$userRole['storeManagerGroupId']<=0){
			//	return array('Flag'=>104,'FlagString'=>'该用户不是室主');		
			//}
			//除非签约的房间是申请的用户的,否则一个帐号只允许拥有一个室主或艺人的角色
			//if(!empty($userRole['managerRoomList'])&&$userRole['storeManagerGroupId']!==$roomInfo['group']){
			//	return array('Flag'=>105,'FlagString'=>'该用户已签约其他房间');
			//}

			if($isTransaction){
				$this->group_mysql_db->start_transaction();
			}
			
			//判断用户是否为该房间的站长,对channel_user进行不同的操作
			//if($userRole['storeManagerGroupId']===$roomInfo['group']){
				$channelUserInfo['partner_id'] = 100;
				$channelUserInfo['uid'] = $data['PartnerUin'];
				$channelUserInfo['room_id']=$data['RoomId'];
				$channelUserInfo['up_uid']=$roomInfo['group'];
				$channelUserInfo['type']=9;
				$channelUserInfo['name']='室主';
				$channelUserInfo['descr']='室主';
				$channelUserInfo['role_id']=$data['RoleId'];
				$channelUserInfo['flag'] = 1;
				//添加
				$result=$this->channelAdd($channelUserInfo);
				if($result['Flag']!=100){
					if($isTransaction){
						$this->group_mysql_db->rollback();
					}
					return array('Flag'=>111,'FlagString'=>'操作失败');
				}
			//}
			//else{
			//	$sql="UPDATE ".DB_NAME_PARTNER.".channel_user SET partner_id='100',up_uid='".$roomInfo['group']."',room_id='".$data['RoomId']."',uptime='".time()."',name='室主' WHERE id='".$userRole['id']."'";
			//	$sql="UPDATE ".DB_NAME_PARTNER.".channel_user SET partner_id='100',up_uid='".$roomInfo['group']."',room_id='".$data['RoomId']."',uptime='".time()."',name='".$data['role_name']."',role_id='".$data['RoleId']."' WHERE id='".$userRole['id']."'";
			//	if(!$this->group_mysql_db->query($sql)){
			//		if($isTransaction){
			//			$this->group_mysql_db->rollback();
			//		}
			//		return array('Flag'=>111,'FlagString'=>'操作失败');
			//	}
			//}
			//添加渠道关系
			$channelRelationInfo=array(
				'ChannelId'=>$data['RoomId'],
				'OwnUin'=>$data['PartnerUin'],
				'GroupId'=>$roomInfo['group']
			);			
			$sql="SELECT uid,up_uid,room_id FROM ".DB_NAME_PARTNER.".channel_user WHERE room_id='".$roomInfo['group']."' AND type=8";
            $grouprow=$this->group_mysql_db->get_row($sql,"ASSOC");
			$channelRelationInfo['GroupUin']=$grouprow['uid'];//站长id
			$channelRelationInfo['RegionId']=$grouprow['up_uid'];//地域ID
			$sql="SELECT uid,up_uid,room_id FROM ".DB_NAME_PARTNER.".channel_user WHERE room_id='".$grouprow['up_uid']."' AND type=7";
            //$regionrow=$this->group_mysql_db->get_row($sql,"ASSOC");
			//$channelRelationInfo['RegionUin']=$regionrow['uid'];//地域负责人ID
            //添加
			$result=$this->channelRelationAdd($channelRelationInfo);
			if($result['Flag']!=100){
				if($isTransaction){
					$this->group_mysql_db->rollback();
				}
				return array('Flag'=>111,'FlagString'=>'操作失败');
			}
			
			//添加角色
			$roleData=array(
				'extparam'=>array(
					'Tag'=>'AddGroupRole',
					'GroupId'=>$roomInfo['group'],
					'Uin'=>$data['PartnerUin'],
					'RoleId'=>intval($data['RoleId']),
					'RoomId'=>$data['RoomId']
				)
			);
			$res=httpPOST(ROLE_API_PATH,$roleData);
			if($res['Flag']!=100){
				if($isTransaction){
					$this->group_mysql_db->rollback();
				}
				return array('Flag'=>111,'FlagString'=>'操作失败');
			}
			
			//更新房间室主
			$sql="UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET ownuin='".$data['PartnerUin']."' WHERE id='".$data['RoomId']."'";
			if(!$this->group_mysql_db->query($sql)){
				if($isTransaction){
					$this->group_mysql_db->rollback();
				}
				return array('Flag'=>111,'FlagString'=>'操作失败');
			}
			
			$sql="INSERT INTO ".DB_NAME_NEW_ROOMS.".roommanager_tbl(room_id,uin) VALUES ('".$data['RoomId']."','".$data['PartnerUin']."')";
			if(!$this->group_mysql_db->query($sql)){
				if($isTransaction){
					$this->group_mysql_db->rollback();
				}
				return array('Flag'=>111,'FlagString'=>'操作失败');
			}
			if($isTransaction){
				$this->group_mysql_db->commit();
			}
			//增加代办事务
			if($roomInfo['group']>0/*&&empty($userRole['storeManagerGroupId'])*/){
				$group=new GroupManage();
				$groupInfo=$group->getGroupInfo(0,$roomInfo['group']);
				if($groupInfo['Result']['uin']>0){
					$ssoData=array(
						'extparam'=>array(
							'Tag'=>'GetUserBasicForUin',
							'Uin'=>$groupInfo['Result']['uin']
						)
					);
					$ssoInfo=httpPOST(SSO_API_PATH,$ssoData);
					if(!empty($ssoInfo['baseInfo'])){
						$handleMatter=new HandleMatter();
						$handleData=array(
							'uin'=>$data['PartnerUin'],
							'content'=>'您已被站长'.$groupInfo['Result']['uin'].'（'.$ssoInfo['baseInfo']['nick'].'）签约为房间'.$data['RoomId'].'（'.$roomInfo['name'].'）的'.$data['role_name'].'，请与站长取得联系。',
							'link'=>'/service/role_select.php?',
							'link_name'=>'查看'
						);
						$handleMatter->add($handleData);
					}
				}
			}
			return array('Flag'=>100,'FlagString'=>'操作成功');
		}
		//签约艺人
		elseif($data['Type']==3){
			if(getChannelType($data['PartnerUin'],$data['RoomId']) > 0){
				return array('Flag'=>105,'FlagString'=>'该用户已是该房间艺人');
			}
			//if($userRole['isRoomEntertainer']===false){
			//	return array('Flag'=>104,'FlagString'=>'该用户不是艺人');
			//}
			//if($userRole['entertainerGroupId']==$roomInfo['group']){
			//	return array('Flag'=>105,'FlagString'=>'该用户已是您的站签约人员');
			//}
			//if(!empty($userRole['entertainerRoomId'])){
			//	return array('Flag'=>105,'FlagString'=>'该用户已签约其他房间');
			//}
			//是否超出房间设定的签约艺人数
			$total=$this->getEntertainerTotal($data['RoomId']);
			if($total['Flag']!=100){
				//if($isTransaction){
				//	$this->group_mysql_db->rollback();
				//}
				return array('Flag'=>111,'FlagString'=>'操作失败');
			}
			if($total['Result']>=$roomInfo['entertainer_quota']){
				//if($isTransaction){
				//	$this->group_mysql_db->rollback();
				//}
				return array('Flag'=>106,'FlagString'=>'该房间签约艺人已满');
			}
			if($isTransaction){
				$this->group_mysql_db->start_transaction();
			}
			$channelUserInfo['partner_id'] = 100;
			$channelUserInfo['uid'] = $data['PartnerUin'];
			$channelUserInfo['room_id']=$data['RoomId'];
			$channelUserInfo['up_uid']=$roomInfo['group'];
			$channelUserInfo['type']=15;
			$channelUserInfo['name']='艺人';
			$channelUserInfo['descr']='艺人';
			$channelUserInfo['role_id']=$data['RoleId'];
			$channelUserInfo['flag'] = 1;
			//添加
			$result=$this->channelAdd($channelUserInfo);
			if($result['Flag']!=100){
				if($isTransaction){
					$this->group_mysql_db->rollback();
				}
				return array('Flag'=>111,'FlagString'=>'操作失败');
			}
			
			//添加角色
			$roleData=array(
				'extparam'=>array(
					'Tag'=>'AddGroupRole',
					'GroupId'=>$roomInfo['group'],
					'Uin'=>$data['PartnerUin'],
					'RoleId'=>intval($data['RoleId']),
					'RoomId'=>$data['RoomId']
				)
			);
			$res=httpPOST(ROLE_API_PATH,$roleData);
			if($res['Flag']!=100){
				if($isTransaction){
					$this->group_mysql_db->rollback();
				}
				return array('Flag'=>111,'FlagString'=>'操作失败');
			}
			
			//$sql="UPDATE ".DB_NAME_PARTNER.".channel_user SET partner_id='100',up_uid='".$roomInfo['group']."',room_id='".$data['RoomId']."',uptime='".time()."',name='艺人' WHERE id='".$userRole['id']."'";
			//$sql="UPDATE ".DB_NAME_PARTNER.".channel_user SET partner_id='100',up_uid='".$roomInfo['group']."',room_id='".$data['RoomId']."',uptime='".time()."',name='".$data['role_name']."',role_id='".$data['RoleId']."' WHERE id='".$userRole['id']."'";
			//if(!$this->group_mysql_db->query($sql)){
			//	if($isTransaction){
			//		$this->group_mysql_db->rollback();
			//	}
			//	return array('Flag'=>111,'FlagString'=>'操作失败');
			//}
			if($isTransaction){
				$this->group_mysql_db->commit();
			}
			//增加代办事务
			if($roomInfo['group']>0/*&&empty($userRole['storeManagerGroupId'])*/){
				$group=new GroupManage();
				$groupInfo=$group->getGroupInfo(0,$roomInfo['group']);
				if($groupInfo['Result']['uin']>0){
					$ssoData=array(
						'extparam'=>array(
							'Tag'=>'GetUserBasicForUin',
							'Uin'=>$groupInfo['Result']['uin']
						)
					);
					$ssoInfo=httpPOST(SSO_API_PATH,$ssoData);
					if(!empty($ssoInfo['baseInfo'])){
						$handleMatter=new HandleMatter();
						$handleData=array(
							'uin'=>$data['PartnerUin'],
							'content'=>'您已被站长'.$groupInfo['Result']['uin'].'（'.$ssoInfo['baseInfo']['nick'].'）签约为房间'.$data['RoomId'].'（'.$roomInfo['name'].'）的'.$data['role_name'].'，请与站长取得联系。',
							'link'=>'/service/role_select.php?',
							'link_name'=>'查看'
						);
						$handleMatter->add($handleData);
					}
				}
			}
			return array('Flag'=>100,'FlagString'=>'操作成功');
		}
	}
	
	/**
	 *   解约记录列表
	 *   @param	array $data 查询所需要的数据
	 *   @return array $array 返回需要查找的记录列表
	 */
	public function getTerminationRecordsList($data){
		if(intval($data['Uin'])<=0&&intval($data['RoomId'])<=0&&intval($data['GroupId'])<=0&&intval($data['GroupUin'])<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		foreach($data as $key=>$val){
			$data[$key]=intval($val);
		}
		$where='1';
		if($data['Uin']>0){
			$where.=" AND uin=".$data['Uin'];
		}
		if($data['RoomId']>0){
			$where.=" AND room_id=".$data['RoomId'];
		}
		if($data['GroupId']>0){
			$where.=" AND group_id=".$data['GroupId'];
		}
		if($data['GroupUin']>0){
			$where.=" AND group_uin=".$data['GroupUin'];
		}
		if($data['role_id']>0){
			$where.=" AND role_id=".$data['role_id'];
		}
		
		$sql="SELECT COUNT(*) FROM ".DB_NAME_PARTNER.".termination_records WHERE ".$where;
		$count=$this->group_mysql_db->get_var($sql);
		if($count<=0){
			return array('Flag'=>100,'FlagString'=>'没有数据');
		}

		$pageArr=$this->showPage($count);
		
		$sql="SELECT * FROM ".DB_NAME_PARTNER.".termination_records WHERE ".$where." ORDER BY id DESC LIMIT ".$pageArr['limit'];
		
		$list=$this->group_mysql_db->get_results($sql,'ASSOC');
		
		foreach($list as $key=>$val){
			$data=array(
				'extparam'=>array(
					'Tag'=>'GetUserBasicForUin',
					'Uin'=>$val['uin']
				)
			);
			$userInfo=httpPOST(SSO_API_PATH,$data);
			if(!empty($userInfo['baseInfo'])){
				$list[$key]['userInfo']=$userInfo['baseInfo'];
			}
		}
		
		return array('Flag'=>100,'FlagString'=>'解约记录列表','recordsList'=>$list,'page'=>$pageArr['page'],'total'=>$count);
		
	}
	
	/**
	 *   解约记录详情
	 *   @param	int $id 查询所需要的主键id
	 *   @return array $array 返回需要查找记录信息
	 */
	public function getTerminationRecordsInfo($id){
		if(!is_numeric($id)||$id<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql="SELECT * FROM ".DB_NAME_PARTNER.".termination_records WHERE id=".$id;
		$records=$this->group_mysql_db->get_row($sql,'ASSOC');
		if(empty($records)){
			return array('Flag'=>102,'FlagString'=>'没有数据');
		}
		$data=array(
			'extparam'=>array(
				'Tag'=>'GetUserBasicForUin',
				'Uin'=>$records['uin']
			)
		);
		$userInfo=httpPOST(SSO_API_PATH,$data);
		if(!empty($userInfo['baseInfo'])){
			$records['user_nick']=$userInfo['baseInfo']['nick'];
		}
		$sql="SELECT name FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id=".$records['room_id'];
		$records['room_name']=$this->group_mysql_db->get_var($sql);
		$sql="SELECT name FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid=".$records['group_id'];
		$records['group_name']=$this->db->get_var($sql);
		return array('Flag'=>100,'FlagString'=>'成功','Records'=>$records);
	}
	
	/**
	 *   解约室主或艺人,数据库中type=8站长,type=9室主,type=15艺人
	 *   @param	array $data 解约需要的数据
	 *   @return array $array 返回结果
	 */
	public function partnerTermination($data){
		if(!is_numeric($data['ChannelId'])||$data['ChannelId']<=0){
			return array('Flag'=>101,'FlagString'=>'渠道ID不能为空');
		}
		if(!in_array($data['Type'],array(1,2))){
			return array('Flag'=>101,'FlagString'=>'解约类型不能为空');
		}
		
		$sql="SELECT * FROM ".DB_NAME_PARTNER.".channel_user WHERE id=".$data['ChannelId'];
		$channelInfo=$this->group_mysql_db->get_row($sql,'ASSOC');
		if(empty($channelInfo)||!in_array($channelInfo['type'],array(9,15))||empty($channelInfo['uid'])||empty($channelInfo['up_uid'])||empty($channelInfo['room_id'])){
			return array('Flag'=>110,'FlagString'=>'操作失败,未知错误');
		}
		
		//站长ID
		$sql="SELECT uin FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid=".$channelInfo['up_uid'];
		$groupUin=$this->db->get_var($sql);
		
		//如果是站长主动解约
		if($data['Type']==1){
			if(empty($data['GroupId'])||$data['GroupId']!=$channelInfo['up_uid']){
				return array('Flag'=>101,'FlagString'=>'站ID不存在');
			}
			//如果解约的室主,需要新的室主
// 			if($channelInfo['type']==9){
// 				if(!is_numeric($data['NewPartnerUin'])||empty($data['NewPartnerUin'])){
// 					return array('Flag'=>101,'FlagString'=>'新室主ID不存在');
// 				}
// 				if($data['NewPartnerUin']==$channelInfo['uid']){
// 					return array('Flag'=>101,'FlagString'=>'新室主ID不能和当前室主ID相同');
// 				}
// 			}
		}
		//如果是室主艺人主动解约
		elseif($data['Type']==2){
			if(empty($data['PartnerUin'])||$data['PartnerUin']!=$channelInfo['uid']){
				return array('Flag'=>101,'FlagString'=>'解约人员ID不存在');
			}		
			//站长和室主是同一个人的话，直接返回成功
			if($data['PartnerUin']==$groupUin&&$channelInfo['type']==9){
				$roleId = $channelInfo['role_id']?$channelInfo['role_id']:10185;
				$role_name = $this->db->get_var("SELECT name from ".DB_NAME_TPL.".role where id=".$roleId);
				//解约记录
				$sql="INSERT INTO ".DB_NAME_PARTNER.".termination_records (uin,room_id,group_id,group_uin,channel_type,type,times,role_id,role_name) VALUES ('".$channelInfo['uid']."','".$channelInfo['room_id']."','".$channelInfo['up_uid']."','".$groupUin."','".$channelInfo['type']."','".$data['Type']."','".time()."','".$roleId."','".$role_name."')";
				if(!$this->group_mysql_db->query($sql)){
					$this->group_mysql_db->rollback();
					return array('Flag'=>111,'FlagString'=>'操作失败');
				}
				$recordsId=$this->group_mysql_db->insert_id();
		
				return array('Flag'=>100,'FlagString'=>'操作成功','recordsId'=>$recordsId);
			}
		}
		
		$this->group_mysql_db->start_transaction();
		//解约室主
		if($channelInfo['type']==9){
			//$roleId=10185;
			$roleId = $channelInfo['role_id']?$channelInfo['role_id']:10185;
			//如果是室主主动解约,新的室主是站长
// 			if(empty($data['NewPartnerUin'])){
// 				$data['NewPartnerUin']=$groupUin;
// 			}
			$sqlD1="DELETE FROM ".DB_NAME_PARTNER.".channel_relation WHERE ChannelId=".$channelInfo['room_id']." AND OwnUin=".$channelInfo['uid']." AND GroupId=".$channelInfo['up_uid']." LIMIT 1";
			//如果被解约人是站长
			//if($groupUin==$channelInfo['uid']){
				$sqlD2="DELETE FROM ".DB_NAME_PARTNER.".channel_user WHERE id=".$data['ChannelId'];
			//}
			//else{
				//$sqlD2="UPDATE ".DB_NAME_PARTNER.".channel_user SET up_uid=0,room_id=0 WHERE id=".$data['ChannelId'];
			//	$sqlD2="UPDATE ".DB_NAME_PARTNER.".channel_user SET up_uid=0,room_id=0,role_id=0 WHERE id=".$data['ChannelId'];
			//}
			$sqlD3="DELETE FROM ".DB_NAME_NEW_ROOMS.".roommanager_tbl WHERE room_id=".$channelInfo['room_id'];
			$sqlD4="UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET ownuin='0' WHERE id='".$channelInfo['room_id']."'";;
			if(!$this->group_mysql_db->query($sqlD1)||!$this->group_mysql_db->query($sqlD2)||!$this->group_mysql_db->query($sqlD3)||!$this->group_mysql_db->query($sqlD4)){
				$this->group_mysql_db->rollback();
				return array('Flag'=>111,'FlagString'=>'操作失败');
			}
			/*
			//添加新的室主
			$role_name = $this->db->get_var("SELECT name from ".DB_NAME_PERMISSION.".role where id=".$roleId);
			$signedDate=array(
				'PartnerUin'=>$data['NewPartnerUin'],
				'RoomId'=>$channelInfo['room_id'],
				'Type'=>1,
				'RoleId'=>$roleId,
				'role_name'=>$role_name
			);
			$result=$this->partnerSigned($signedDate,false);
			if($result['Flag']!=100){
				$this->group_mysql_db->rollback();
				return $result;
			}
			*/
		}
		//解约艺人
		elseif($channelInfo['type']==15){
			//$roleId=10186;
			$roleId = $channelInfo['role_id']?$channelInfo['role_id']:10186;
			//$sql="UPDATE ".DB_NAME_PARTNER.".channel_user SET up_uid=0,room_id=0 WHERE id=".$data['ChannelId'];
			$sql="DELETE FROM ".DB_NAME_PARTNER.".channel_user WHERE id=".$data['ChannelId'];
			//$sql="UPDATE ".DB_NAME_PARTNER.".channel_user SET up_uid=0,room_id=0,role_id=0 WHERE id=".$data['ChannelId'];
			if(!$this->group_mysql_db->query($sql)){
				$this->group_mysql_db->rollback();
				return array('Flag'=>111,'FlagString'=>'操作失败');
			}
		}
		
		//验证角色
		$roleData=array(
			'extparam'=>array(
				'Tag'=>'UinRole',
				'GroupId'=>$channelInfo['up_uid']
			)
		);
		$roles=httpPOST(ROLE_API_PATH,$roleData);
		if($roles['Flag']!=100){
			return $roles;
		}
		if(!in_array($roleId,$roles['Roles'])){
			$this->group_mysql_db->rollback();
			return array('Flag'=>102,'FlagString'=>'该站没有这个角色');
		}
		
		//删除站角色
		$roleData=array(
			'extparam'=>array(
				'Tag'=>'DeleteGroupRole',
				'GroupId'=>$channelInfo['up_uid'],
				'Uin'=>$channelInfo['uid'],
				'RoleId'=>array($roleId),
				'RoomId'=>$channelInfo['room_id']
			)
		);
		$res=httpPOST(ROLE_API_PATH,$roleData);
		if($res['Flag']!=100){
			$this->group_mysql_db->rollback();
			return array('Flag'=>111,'FlagString'=>'操作失败');
		}
		
		//解约记录
		//$sql="INSERT INTO ".DB_NAME_PARTNER.".termination_records (uin,room_id,group_id,group_uin,channel_type,type,times) VALUES ('".$channelInfo['uid']."','".$channelInfo['room_id']."','".$channelInfo['up_uid']."','".$groupUin."','".$channelInfo['type']."','".$data['Type']."','".time()."')";
		$role_name = $this->db->get_var("SELECT name from ".DB_NAME_TPL.".role where id=".$roleId);
		$sql="INSERT INTO ".DB_NAME_PARTNER.".termination_records (uin,room_id,group_id,group_uin,channel_type,type,times,role_id,role_name) VALUES ('".$channelInfo['uid']."','".$channelInfo['room_id']."','".$channelInfo['up_uid']."','".$groupUin."','".$channelInfo['type']."','".$data['Type']."','".time()."','".$roleId."','".$role_name."')";
		if(!$this->group_mysql_db->query($sql)){
			$this->group_mysql_db->rollback();
			return array('Flag'=>111,'FlagString'=>'操作失败');
		}
		$recordsId=$this->group_mysql_db->insert_id();
		$this->group_mysql_db->commit();
		
		return array('Flag'=>100,'FlagString'=>'操作成功','recordsId'=>$recordsId);
	}
	
	/**
	 *   添加渠道
	 *   @param	array $data 需要的数据
	 *   @return array $array 返回状态及信息
	 */
	public function channelAdd($data){
		if($data['type']<=0||$data['uid']==''){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//地域负责人只能有一个渠道角色,且该区域只存在一个地域负责人
		if($data['type']==7){
			if(empty($data['region_id'])||$data['idcard']==''||$data['other_info']==''){
				return array('Flag'=>101,'FlagString'=>'参数错误');
			}
			$data['room_id']=$data['region_id'];
			$total=$this->group_mysql_db->get_var("SELECT COUNT(*) FROM ".DB_NAME_PARTNER.".channel_user WHERE region_id=".$data['region_id']." AND type=".$data['type']);
			if($total>0){
				return array('Flag'=>101,'FlagString'=>'该区域已存在地域负责人');
			}
		}
		$sql="INSERT INTO ".DB_NAME_PARTNER.".channel_user (partner_id,type,uid,region_id,up_uid,room_id,name,descr,pact_id,bank_name,bank_id,have_salary,salary,have_tax,tax,have_push_money,push_money,uptime,flag,real_name,idcard,other_info,role_id) VALUES ('".$data['partner_id']."','".$data['type']."','".$data['uid']."','".$data['region_id']."','".$data['up_uid']."','".$data['room_id']."','".$data['name']."','".$data['descr']."','".$data['pact_id']."','".$data['bank_name']."','".$data['bank_id']."','".$data['have_salary']."','0','".$data['have_tax']."','0','".$data['have_push_money']."','0','".time()."','".$data['flag']."','".$data['real_name']."','".$data['idcard']."','".$data['other_info']."','".$data['role_id']."')";
        if($this->group_mysql_db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'添加成功');
		}
		else{
			return array('Flag'=>102,'FlagString'=>'添加失败');
		}
	}
	
	/**
	 *   添加渠道关系
	 *   @param	array $data 需要的数据
	 *   @return array $array 返回状态及信息
	 */
	public function channelRelationAdd($data){
		if(!is_numeric($data['ChannelId'])||$data['ChannelId']<0||!is_numeric($data['OwnUin'])||$data['OwnUin']<0||!is_numeric($data['GroupId'])||$data['GroupId']<0||!is_numeric($data['GroupUin'])||$data['GroupUin']<0||!is_numeric($data['RegionId'])||$data['RegionId']<0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql="INSERT INTO ".DB_NAME_PARTNER.".channel_relation (ChannelId,OwnUin,GroupId,GroupUin,RegionId,RegionUin) VALUES ('".$data['ChannelId']."','".$data['OwnUin']."','".$data['GroupId']."','".$data['GroupUin']."','".$data['RegionId']."','".$data['RegionUin']."')";
		if($this->group_mysql_db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'添加成功');
		}
		else{
			return array('Flag'=>102,'FlagString'=>'添加失败');
		}
	}
	
	/**
	 *   获取房间或站艺人数
	 *   @param	int $uin 用户uin
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回状态及信息
	 */
	public function getEntertainerTotal($roomId=0,$groupId=0){
		$roomId=intval($roomId);
		$groupId=intval($groupId);
		if($roomId<=0&&$groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$where='1';
		if($roomId>0){
			$where.=" AND room_id=$roomId";
		}
		if($groupId>0){
			$where.=" AND up_uid=$groupId";
		}
		$sql="SELECT COUNT(*) FROM ".DB_NAME_PARTNER.".channel_user"." WHERE $where AND type=15";
		$total=$this->group_mysql_db->get_var($sql);
		return array('Flag'=>100,'FlagString'=>'艺人数','Result'=>$total);
	}

	public function getApplyList($group_id,$search_data=array()){
		$where = " a.group_id={$group_id} AND a.type IN (2,3)";
		if(isset($search_data['uin']) && $search_data['uin'] > 0){
			$uin = intval($search_data['uin']);
			$where .= " AND a.uin={$uin}";
		}
		if(isset($search_data['stime']) && !empty($search_data['stime'])){
			$stime = strtotime($search_data['stime']);
			$where .= " AND a.apply_time>={$stime}";
		}
		if(isset($search_data['etime']) && !empty($search_data['etime'])){
			$etime = strtotime($search_data['etime']);
			$where .= " AND a.apply_time<={$etime}";
		}
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_IM.".apply a WHERE {$where}";
		if(($total = $this->group_mysql_db->get_var($sql)) > 0){
			$page_arr = $this->showPage($total);
			$sql = "SELECT a.*,b.name AS name,b.nick AS nick,b.phone AS phone,b.qq AS qq FROM ".DB_NAME_IM.".apply a LEFT JOIN ".DB_NAME_IM.".basic_tbl b ON a.uin=b.uin WHERE {$where} LIMIT {$page_arr['limit']}";
			$res = $this->group_mysql_db->get_results($sql,ASSOC);
			foreach ($res as $key => $val) {
				$channelInfo = getChannelUserInfo($val['uin']);
				if(getChannelUserInfo($val['uin'])){
					$res[$key]['sign_status'] = 1;
				}else{
					$res[$key]['sign_status'] = 2;
				}
			}
		}
		if(isset($res) && !empty($res)){
			return array('Flag'=>100,'FlagString'=>'成功','Result'=>$res,'Page'=>$page_arr['page']);
		}
		return array('Flag'=>101,'FlagString'=>'失败');
	}
	
	//分页
	private function showPage($total,$perpage=20){
		if($total>0){
			$page=new extpage(array (
				'total'=>$total,
				'perpage'=>$perpage
			));
			$page_arr['page']=$page->show();
			$page_arr['limit']=$page->limit();
			unset($page);
		}
		return $page_arr;
	}
}


