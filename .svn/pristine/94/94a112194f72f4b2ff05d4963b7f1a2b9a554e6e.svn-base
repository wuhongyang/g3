<?php

/**
 *   群组操作接口
 *   文件: group.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 /*
class join
{
	//数据库指针
	protected $db = null;

	//构造函数
	public function __construct() {
		$this->db = db::connect(config('database','default'));
	}

	//列表显示
	public function joinList($info){
		$where = 1;
		if(!empty($info['uin'])){
			$where .= " AND a.`uin`='{$info['uin']}'";
		}
		if((isset($info['start']) && isset($info['end'])) && ($info['start'] < $info['end'])){
			$start = strtotime($info['start']);
			$end = strtotime($info['end']);
			$where .= " AND a.apply_time >= {$start} && a.apply_time <= {$end}";
		}
		if(isset($info['status']) && $info['status'] >= 0){
			$where .= " AND a.status={$info['status']}";
		}
		if(isset($info['type']) && $info['type'] >= 0){
			$where .= " AND a.type={$info['type']}";
		}
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_IM.".apply a WHERE {$where}";
		$total = $this->db->get_var($sql);
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = "SELECT a.id,a.uin,u.name,a.type,a.apply_time,u.permanent_city,a.status FROM ".DB_NAME_IM.".apply a LEFT JOIN ".DB_NAME_IM.".new_username u USING(uin) WHERE {$where} LIMIT {$page_arr['limit']}";
			$list = $this->db->get_results($sql,'ASSOC');
			$page = $page_arr['page'];
		}
		return array('Flag'=>100,'FlagString'=>'成功','List'=>$list,'Page'=>$page_arr['page']);
	}

	public function joinInfo($id){
		if($id > 0){
			$sql = "SELECT u.name,u.qq,u.idcard,u.permanent_city AS city_json,a.* FROM ".DB_NAME_IM.".apply a LEFT JOIN ".DB_NAME_IM.".new_username u USING(uin) WHERE a.id={$id}";
			$joinInfo = $this->db->get_row($sql,'ASSOC');
			if(!empty($joinInfo)){
				$channel = getChannelInfo($joinInfo['uin'],0,8);
			}
			return array('Flag'=>100,'FlagString'=>'成功','Info'=>$joinInfo);
		}
		return array('Flag'=>101,'FlagString'=>'不存在的ID');
	}
	
	public function roomInfo($info){
		if($info['Roomid'] <=0) return array('Flag'=>101,'FlagString'=>'参数错误');
		$rooms = new room();
		$roominfo = $rooms->getRoomRec(array('Roomid'=>$info['Roomid']));
		if(empty($roominfo['Data'])) return array('Flag'=>102,'FlagString'=>'房间不存在');
		if($roominfo['Data']['ownuin'] > 0) return array('Flag'=>103,'FlagString'=>'房间存在有室主','OwnUin'=>$roominfo['Data']['ownuin']);
		return array('Flag'=>100,'FlagString'=>'房间存在无室主','OwnUin'=>$roominfo['Data']['ownuin']);
	}

	/*
	public function joinUpdate($info){
		if(empty($info) || $info['id']<0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$time = time();

		//获得申请信息
		$applyInfo = $this->joinInfo($info['id']);
		if($applyInfo['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'这个站长申请不存在');
		}
		$applyInfo = $applyInfo['Info'];
		
		if($info['status'] == 1){
			$roomerInfo = getChannelInfo($applyInfo['uin'],0,9);
			if($roomerInfo['room_id'] > 0){
				return array('Flag'=>104,'FlagString'=>'该帐号已有绑定的房间，无法审核通过');
			}
			$artistInfo = getChannelInfo($applyInfo['uin'],0,15);
			if($artistInfo['room_id'] > 0){
				return array('Flag'=>104,'FlagString'=>'该帐号已有绑定的房间，无法审核通过');
			}
		}
		
		
		//事务开始
		$this->db->start_transaction();

		//审核不通过
		if($info['status'] != 1){
			$sql = "UPDATE ".DB_NAME_IM.".apply SET `status`={$info['status']},uptime={$time},reason='{$info['desc']}' WHERE `id`={$info['id']}";
			if(!$this->db->query($sql)){
				$this->db->rollback();
				return array('Flag'=>102,'FlagString'=>'保存信息失败');
			}

			//写入信息到待办事项
			$handleMatterInfo = array(
				'uin'=>$applyInfo['uin'],
				'content'=>'很抱歉，您的站长申请未能通过。原因如下：'.$info['desc'],
				'link'=>'',
				'link_name'=>''
			);
			if($this->set2HandleMatter($handleMatterInfo) != 1){
				$this->db->rollback();
				return array('Flag'=>103,'FlagString'=>'写入待办事项失败');
			}
			$this->db->commit();
			if($info['msg_tip'] == 1){
				sendSMS($applyInfo['phone'],$info['message'],'站长审核');
			}
			return array('Flag'=>100,'FlagString'=>'保存信息成功');
		}

		$region_id = $info['area'] >0 ? $info['area'] : $info['city'];

		//开站 DB_NAME_GROUP.'.tbl_groups';
		$group = new group();
		$groupopen = $group->addGroup(array('Province'=>$info['province'],'City'=>$info['city'],'Area'=>$info['area'],'Uin'=>$applyInfo['uin'],'GroupId'=>$applyInfo['uin'],'GroupName'=>$applyInfo['uin']));
		if($groupopen['Flag'] != 100){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>$groupopen['FlagString']);
		}

		//渠道用户表添加站长
		$partner_channel = new partner_channel();
		$data = array('type'=>8,'uid'=>$applyInfo['uin'],'region_id'=>$region_id,'up_uid'=>$region_id,'roomid'=>$applyInfo['uin'],'province'=>$info['province'],'city'=>$info['city'],'area'=>$info['area']);
		$rst = $partner_channel->addChannel($data);
		if($rst['Flag'] != 100){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>$rst['FlagString']);
		}

		//开通房间并成为室主
		$rooms = new room();
		//20130001~20139999
		$sql = "SELECT id FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id BETWEEN 5880001 AND 9999999 ORDER BY id DESC LIMIT 1";
		$row = $this->db->get_var($sql,"ASSOC");
		$roomid = empty($row) ? 5880001 : $row+1;
		$roomopen = $rooms->openRoomNoTransaction(array('province'=>$info['province'],'city'=>$info['city'],'area'=>$info['area'],'maxuser'=>100,'expire'=>365,'id'=>$roomid,'ownuin'=>$applyInfo['uin'],'region_id'=>$region_id,'group'=>$applyInfo['uin']));
		if($roomopen['Flag'] != 100) {
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>$roomopen['FlagString']);
		}
		
		//添加角色
		$roleData=array(
			'extparam'=>array(
				'Tag'=>'AddGroupRole',
				'GroupId'=>$applyInfo['uin'],
				'Uin'=>$applyInfo['uin'],
				'RoleId'=>10185,
				'RoomId'=>$roomid,
				'NewGroup'=>true
			)
		);
		$res=httpPOST(ROLE_API_PATH,$roleData);
		if($res['Flag']!=100){
			$this->db->rollback();
			return array('Flag'=>111,'FlagString'=>'操作失败');
		}

		//渠道用户表添加室主
		$roomInfo = array('region_id'=>$region_id,'type'=>9,'uid'=>$applyInfo['uin'],'up_uid'=>$applyInfo['uin'],'roomid'=>$roomid,'province'=>$info['province'],'city'=>$info['city'],'area'=>$info['area']);
		$rst = $partner_channel->addChannel($roomInfo);
		if($rst['Flag'] != 100){
			$this->db->rollback();
			return array('Flag'=>105,'FlagString'=>'添加室主失败');
		}

		//得到地域负责人ID
		$regionInfo = $partner_channel->getInfoByChannelIdAndType($region_id,7);
		$regionInfo = $regionInfo['Info'];
		//地域负责人ID默认为10000
		$regionInfo['uid'] = empty($regionInfo['uid']) ? 10000 : $regionInfo['uid'];

		//同步渠道关系
		$array = array('ChannelId'=>$roomid,'OwnUin'=>$applyInfo['uin'],'GroupId'=>$applyInfo['uin'],'GroupUin'=>$applyInfo['uin'],'RegionId'=>$region_id,'RegionUin'=>$regionInfo['uid']);
		$rst = $partner_channel->channelRelationSync($array);
		if($rst['Flag'] != 100){
			$this->db->rollback();
			return array('Flag'=>106,'FlagString'=>'同步渠道关系失败');
		}

		//更新通行证表里的channel_id字段
		$sql = "UPDATE ".DB_NAME_IM.".apply SET `status`=1,uptime={$time},province={$info['province']},city={$info['city']},area={$info['area']} WHERE `id`={$info['id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>108,'FlagString'=>'审核失败');
		}
		$sql = "UPDATE ".DB_NAME_IM.".sso_user_relate SET group_id ={$applyInfo['uin']} WHERE uin={$applyInfo['uin']} AND uid={$applyInfo['uid']} LIMIT 1";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>109,'FlagString'=>'通行证绑定UIN失败');
		}
		//写入信息到待办事项
		$handleMatterInfo = array(
			'uin'=>$applyInfo['uin'],
			'content'=>'您的站点已开通，为了确保站收入准确打入您的银行账户，请完善您的财务信息。',
			'link'=>'/service/manage_imformation.php?module=bind_account',
			'link_name'=>'立即完善'
		);
		if($this->set2HandleMatter($handleMatterInfo) != 1){
			$this->db->rollback();
			return array('Flag'=>110,'FlagString'=>'写入待办事项失败');
		}
		
		//添加角色
		$roleData=array(
			'extparam'=>array(
				'Tag'=>'AddGroupRole',
				'GroupId'=>$applyInfo['uin'],
				'Uin'=>$applyInfo['uin'],
				'RoleId'=>10187,
				'RoomId'=>0,
				'NewGroup'=>true
			)
		);
		$res=httpPOST(ROLE_API_PATH,$roleData);
		if($res['Flag']!=100){
			$this->db->rollback();
			return array('Flag'=>111,'FlagString'=>'操作失败');
		}
		
		$this->db->commit();
		if($info['msg_tip'] == 1){
			sendSMS($applyInfo['phone'],$info['message'],'站长审核');
		}
		return array('Flag'=>100,'FlagString'=>'审核成功');
	}
	
	public function joinAdd($info){
		//获得申请信息
		$applyInfo = $info['apply'];
		if(isset($applyInfo['uin']) && !is_numeric($applyInfo['uin'])){
			return array('Flag'=>101,'FlagString'=>'申请ID必须为数字');
		}
		if(isset($applyInfo['city']) && !is_numeric($applyInfo['city'])){
			return array('Flag'=>101,'FlagString'=>'请选择常驻城市');
		}
		if(strlen($applyInfo['idcard']) && strlen($applyInfo['idcard']) != 18){
			return array('Flag'=>101,'FlagString'=>'身份证不正确');
		}
		if(isset($info['city']) && !is_numeric($info['city'])){
			return array('Flag'=>101,'FlagString'=>'请选择分配站点');
		}
		if(isset($info['service']) && !is_numeric($info['service'])){
			return array('Flag'=>101,'FlagString'=>'请选择专属服务人员');
		}
		
		$imInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'UinExist','Uin'=>$applyInfo['uin'])));
		//$sql = "SELECT uid,group_id FROM ".DB_NAME_IM.".sso_user_relate WHERE uin={$applyInfo['uin']} AND is_use=1";
		//$imInfo = $this->db->get_row($sql,'ASSOC');
		if($imInfo['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'没有这个用户ID');
		}
		
		$sql = "SELECT uin FROM ".DB_NAME_IM.".apply WHERE uin={$applyInfo['uin']} AND type=1";
		$count = $this->db->get_var($sql);
		if($count > 0){
			return array('Flag'=>104,'FlagString'=>'该帐号通行证下已进行了站长申请');
		}
		
		//验证身份证
		//$sql = "SELECT idcard FROM ".DB_NAME_IM.".username WHERE idcard='{$applyInfo['idcard']}' AND uid!={$imInfo['uid']}";
		//$count = $this->db->get_var($sql);
		$rst = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetInfoByIdcard','Idcard'=>$applyInfo['idcard'],'IsNeedGroup'=>false)));
		if(!empty($rst['Info']) && $rst['Info']['uin'] != $applyInfo['uin']){
			return array('Flag'=>105,'FlagString'=>'身份证已被其他人占用，无法提交申请');
		}

		$roomerInfo = getChannelInfo($applyInfo['uin'],0,9);
		if($roomerInfo['room_id'] > 0){
			return array('Flag'=>106,'FlagString'=>'该帐号已有绑定的房间，无法审核通过');
		}
		$artistInfo = getChannelInfo($applyInfo['uin'],0,15);
		if($artistInfo['room_id'] > 0){
			return array('Flag'=>106,'FlagString'=>'该帐号已有绑定的房间，无法审核通过');
		}
		
		//事务开始
		//$this->db->start_transaction();

		//开站 DB_NAME_GROUP.'.tbl_groups';
		$group = new group();
		$groupopen = $group->addGroup(array('Province'=>$info['province'],'City'=>$info['city'],'Area'=>$info['area'],'Uin'=>$applyInfo['uin'],'GroupId'=>$applyInfo['uin'],'GroupName'=>$applyInfo['uin']));
		if($groupopen['Flag'] != 100){
			//$this->db->rollback();
			return array('Flag'=>109,'FlagString'=>$groupopen['FlagString']);
		}
		
		//更新通行证
		$array = array(
			'provinceId' => intval($applyInfo['province']),
			'cityId' => intval($applyInfo['city'])
		);
		$json = json_encode($array);
		$data = array('name'=>$applyInfo['name'],'qq'=>$applyInfo['qq'],'idcard'=>$applyInfo['idcard'],'permanent_city'=>$json,'group_id'=>$applyInfo['uin']);
		$rst = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'EditPassport','Data'=>$data,'Uin'=>$applyInfo['uin'])));
		//$sql = "UPDATE ".DB_NAME_IM.".username set name='{$applyInfo['name']}',qq='{$applyInfo['qq']}',idcard='{$applyInfo['idcard']}',permanent_city='{$json}' WHERE uid={$imInfo['uid']}";
		if($rst['Flag'] != 100){
			//$this->db->rollback();
			return array('Flag'=>107,'FlagString'=>$rst['Flag'].'保存通行证信息失败');
		}
		
		//插入申请数据
		$time = time();
		$sql = "INSERT INTO ".DB_NAME_IM.".apply(uin,`province`,`city`,`area`,`type`,`status`,`apply_time`) VALUES({$applyInfo['uin']},'{$info['province']}','{$info['city']}','{$info['area']}',1,1,{$time})";
		if(!$this->db->query($sql)){
			//$this->db->rollback();
			return array('Flag'=>108,'FlagString'=>'申请数据插入失败');
		}

		$region_id = $info['area'] >0 ? $info['area'] : $info['city'];

		//渠道用户表添加站长
		$partner_channel = new partner_channel();
		$data = array('type'=>8,'uid'=>$applyInfo['uin'],'region_id'=>$region_id,'up_uid'=>$region_id,'roomid'=>$applyInfo['uin'],'province'=>$info['province'],'city'=>$info['city'],'area'=>$info['area']);
		$rst = $partner_channel->addChannel($data);
		if($rst['Flag'] != 100){
			//$this->db->rollback();
			return array('Flag'=>110,'FlagString'=>$rst['FlagString']);
		}

		//更新通行证表里的channel_id字段
		//$sql = "UPDATE ".DB_NAME_IM.".sso_user_relate SET group_id ={$applyInfo['uin']} WHERE uin={$applyInfo['uin']} AND uid={$imInfo['uid']} LIMIT 1";
		//if(!$this->db->query($sql)){
		//	$this->db->rollback();
		//	return array('Flag'=>112,'FlagString'=>'通行证绑定UIN失败');
		//}
		//写入信息到待办事项
		$handleMatterInfo = array(
			'uin'=>$applyInfo['uin'],
			'content'=>'您的站点已开通，为了确保站收入准确打入您的银行账户，请完善您的财务信息。',
			'link'=>'/service/manage_imformation.php?module=bind_account',
			'link_name'=>'立即完善'
		);
		if($this->set2HandleMatter($handleMatterInfo) != 1){
			//$this->db->rollback();
			return array('Flag'=>113,'FlagString'=>'写入待办事项失败');
		}
		
		//添加角色
		$roleData=array(
			'extparam'=>array(
				'Tag'=>'AddGroupRole',
				'GroupId'=>$applyInfo['uin'],
				'Uin'=>$applyInfo['uin'],
				'RoleId'=>10187,
				'RoomId'=>0,
				'NewGroup'=>true
			)
		);
		$res=httpPOST(ROLE_API_PATH,$roleData);
		if($res['Flag']!=100){
			//$this->db->rollback();
			return array('Flag'=>114,'FlagString'=>'操作失败');
		}
		
		//$this->db->commit();
		
		//if($info['msg_tip'] == 1){
		//	sendSMS($applyInfo['phone'],$info['message'],'站长审核');
		//}
		return array('Flag'=>100,'FlagString'=>'添加成功');
	}

	private function set2HandleMatter($data){
		$time = time();
		$data['content'] = addslashes($data['content']);
		$sql = "INSERT INTO ".DB_NAME_IM.".handle_matter(uin,content,link,link_name,uptime) VALUES({$data['uin']},'{$data['content']}','{$data['link']}','{$data['link_name']}',{$time})";
		if($this->db->query($sql)){
			return 1;
		}
		return -1;
	}
	
	private function showPage($total, $perpage = 15) {
		if ($total > 0) {
			$page = new extpage(array (
				'total' => $total,
				'perpage' => $perpage
			));
			$page_arr['page'] = $page->show();
			$page_arr['limit'] = $page->limit();
			unset ($page);
		}
		return $page_arr;
	}
}
*/