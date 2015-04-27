<?php

/**
 *   群组操作接口
 *   文件: partner_channel.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class partner_channel
{
	//数据库指针
	protected $db = null;

	//构造函数
	public function __construct($group_id){
		$this->db = db::connect(config('database','default'));
		$this->groupMysql = ($group_id==10000) ? db::connect(config('database','default')) : domain::main()->GroupDBConn('mysql', $group_id);
	}
	
	//得到所有开放的城市
	private function getOPenCity(){
		$open_citys = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetOpenCity')));
		return $open_citys;
	}
	
	//得到合作商的名称
	private function getPartners(){
		$sql = 'SELECT name,id FROM '.DB_NAME_PARTNER.'.partner_list';
		return $this->groupMysql->get_results($sql,'ASSOC');
	}
	
	//获得渠道分类
	private function getChannelCategory(){
		$sql = 'SELECT id,name FROM '.DB_NAME_PARTNER.'.channel_category WHERE `status`="1"';
		return $this->db->get_results($sql,'ASSOC');
	}
	
	//合作商列表
	public function partnerList($data){
		//查询条件
		if(!empty($data['keyword'])){
			$where .= ' AND '.$data['option'].' LIKE "'.$data['keyword'].'%"';
		}
		if($data['type']=='0' || $data['type']==1){
			$where .= ' AND `type`="'.intval($data['type']).'"';
		}
		if($data['province'] > -1){
			$where .= ' AND province_id="'.intval($data['province']).'"';
		}
		if($data['city'] > -1){
			$where .= ' AND city_id="'.intval($data['city']).'"';
		}
		//得到数量用于分页
		$sql = 'SELECT COUNT(*) FROM '.DB_NAME_PARTNER.'.partner_list WHERE TRUE '.$where;
		$total = $this->groupMysql->get_var($sql);
		if($total > 0){
			$page_arr = $this->showPage($total);
			//id,card_id,name,city,uptime,type,flag
			$sql = 'SELECT * FROM '.DB_NAME_PARTNER.'.partner_list WHERE TRUE '.$where.' LIMIT '.$page_arr['limit'];
			$list = $this->groupMysql->get_results($sql,'Assoc');
		}
		return array('li'=>$list,'region'=>$this->getOPenCity(),'page'=>$page_arr['page'],'Flag'=>100,'FlagString'=>'合作商列表');
	}
	
	//渠道列表
	public function channelList($data){
		//查询条件
		if(!empty($data['keyword'])){
			if($data['option'] == 'uid' || $data['option'] == 'room_id'){
				$where .= ' AND  a.'.$data['option'].' = "'.$data['keyword'].'"';
			}else{
				$where .= ' AND  a.'.$data['option'].' LIKE "'.$data['keyword'].'%"';
			}
		}
		if(isset($data['up_uid']) && intval($data['up_uid']) > 0){
			$where .= ' AND a.up_uid="'.$data['up_uid'].'"';
		}
		if(isset($data['type']) && intval($data['type']) > 0){
			$where .= ' AND a.type="'.$data['type'].'"';
		}
		if(isset($data['partner_id']) && intval($data['partner_id']) > 0){
			$where .= ' AND a.partner_id="'.$data['partner_id'].'"';
		}
		if($data['partner'] > -1){
			$where .= ' AND a.partner_id="'.$data['partner'].'"';
		}
		if($data['status'] > -1){
			$where .= ' AND a.flag="'.$data['status'].'"';
		}
		//得到数量用于分页
		$sql = 'SELECT COUNT(*) FROM '.DB_NAME_PARTNER.'.channel_user as a,'.DB_NAME_PARTNER.'.partner_list as b WHERE a.partner_id=b.id '.$where;
		$total = $this->groupMysql->get_var($sql);
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = 'SELECT a.*,b.name AS partner_name FROM '.DB_NAME_PARTNER.'.channel_user AS a,'.DB_NAME_PARTNER.'.partner_list AS b WHERE a.partner_id=b.id '.$where.' LIMIT '.$page_arr['limit'];
			$list = $this->groupMysql->get_results($sql,'ASSOC');
		}
		return array('li'=>$list,'page'=>$page_arr['page'],'partners'=>$this->getPartners(),'channelCategory'=>$this->getChannelCategory(),'Flag'=>100,'FlagString'=>'渠道列表');
	}
	
	//得到渠道详情
	public function getChannelInfo($data){
		$sql = 'SELECT a.*,b.id as partner_id,b.name AS partner_name FROM '.DB_NAME_PARTNER.'.channel_user AS a,'.DB_NAME_PARTNER.'.partner_list AS b WHERE a.partner_id=b.id AND a.id="'.$data['id'].'"';
		if($data['type'] == 'add'){
			$sql = 'SELECT id as partner_id,name AS partner_name FROM '.DB_NAME_PARTNER.'.partner_list WHERE id="'.$data['id'].'"';
		}
		$info = $this->groupMysql->get_row($sql,'ASSOC');

		//得到银行信息
		$sql = "SELECT bank_id,bank_name,bank_address FROM ".DB_NAME_PARTNER.".account WHERE uin={$info['uid']}";
		$info['bankInfo'] = $this->groupMysql->get_row($sql,ASSOC);
		//得到渠道类别
		$channl_categories = $this->getChannelCategory();

		return array('info'=>$info,'channl_categories'=>$channl_categories,'Flag'=>100,'FlagString'=>'渠道详情');
	}

	//根据渠道ID和类型获得其他信息
	public function getInfoByChannelIdAndType($channelId,$type){
		if($channelId<1 || $type<1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_PARTNER.".channel_user WHERE room_id={$channelId} AND `type`={$type}";
		$info = $this->groupMysql->get_row($sql);
		if(empty($info)){
			return array('Flag'=>102,'FlagString'=>'获取信息失败');
		}
		return array('Flag'=>100,'FlagString'=>'获取信息成功','Info'=>$info);
	}

	//室主同步
	public function channelRelationSync($array){
		if($array['ChannelId'] <= 0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//渠道关系是否存在
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_PARTNER.".channel_relation WHERE ChannelId = {$array['ChannelId']}";
		$count = $this->groupMysql->get_var($sql);
		if($count > 0){
			$sql = "UPDATE ".DB_NAME_PARTNER.".channel_relation SET OwnUin={$array['OwnUin']},GroupId={$array['GroupId']},GroupUin={$array['GroupUin']},RegionId={$array['RegionId']},RegionUin={$array['RegionUin']} WHERE ChannelId = {$array['ChannelId']}";
		}else{
			$sql = "INSERT INTO ".DB_NAME_PARTNER.".channel_relation (ChannelId ,OwnUin ,GroupId,GroupUin,RegionId,RegionUin) VALUES ({$array['ChannelId']},{$array['OwnUin']},{$array['GroupId']},{$array['GroupUin']},{$array['RegionId']},{$array['RegionUin']})";
		}
		if($this->groupMysql->query($sql)){
			return array('Flag'=>100,'FlagString'=>'渠道关系同步室主成功');
		}
		return array('Flag'=>102,'FlagString'=>'渠道关系同步室主失败');
	}

	public function addChannel($data){
		$time = time();
		$sql = "INSERT INTO ".DB_NAME_PARTNER.".channel_user(partner_id,`type`,uid,up_uid,room_id,uptime,role_id) VALUES(100,{$data['type']},{$data['uid']},{$data['up_uid']},{$data['roomid']},{$time},{$data['role_id']})";
		if(!$this->groupMysql->query($sql)){
			return array('Flag'=>101,'FlagString'=>'添加渠道关系失败');
		}
		return array('Flag'=>100,'FlagString'=>'添加渠道关系成功');
	}
	
	public function channelAdd($data){
		//检测参数|| $data['descr']=='' || $data['pact_id']==''  || $data['bank_id']==''
		$data['uid'] = intval($data['uid']);
		if($data['type']==-1 || $data['uid']<1 || $data['name']=='' || $data['region_id']<0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		$uinInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'UinExist','Uin'=>$data['uid'])));
		if($uinInfo['Flag'] == 102){
			return array('Flag'=>103,'FlagString'=>'不存在这样的用户ID');
		}
		
		if($data['type']==7){//地域负责人只能有一个渠道角色,且该区域只存在一个地域负责人
			$data['room_id'] = $data['region_id'];
			/*$sql = 'SELECT COUNT(*) FROM '.DB_NAME_PARTNER.'.channel_user WHERE uid='.intval($data['uid']);
			$total = $this->groupMysql->get_var($sql);
			if($total>0){
				return array('Flag'=>101,'FlagString'=>'用户拥有其他渠道角色');
			}*/
			$s_total = $this->groupMysql->get_var('SELECT COUNT(*) FROM '.DB_NAME_PARTNER.'.channel_user WHERE region_id = '.$data['region_id'].' AND `type` = '.$data['type']);
			if($s_total > 0) return array('Flag'=>101,'FlagString'=>'该区域已存在地域负责人');
		}
		$sql = 'INSERT INTO '.DB_NAME_PARTNER.'.channel_user(`partner_id`,`type`,`uid`,`region_id`,`up_uid`,`name`,`descr`,`pact_id`,`bank_name`,`bank_id`,`have_salary`,`salary`,`have_tax`,`tax`,`have_push_money`,`push_money`,`uptime`,`flag`,`room_id`,`real_name`,`idcard`,`other_info`) VALUES("'.$data['partner_id'].'","'.$data['type'].'","'.$data['uid'].'","'.$data['region_id'].'","'.$data['up_uid'].'","'.$data['name'].'","'.$data['descr'].'","'.$data['pact_id'].'","'.$data['bank_name'].'","'.$data['bank_id'].'","'.$data['have_salary'].'",0,"'.$data['have_tax'].'",0,"'.$data['have_push_money'].'",0,"'.time().'","'.$data['status'].'","'.$data['room_id'].'","'.$data['real_name'].'","'.$data['idcard'].'",\''.$data['other_info'].'\')';
		if($this->groupMysql->query($sql)){
			return array('Flag'=>100,'FlagString'=>'添加成功');
		}
		return array('Flag'=>102,'FlagString'=>'添加失败');
	}

	//站长
	private function stationHeadUpdate($info,$id){
		if($id < 1 || empty($info)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//得到站长ID
		$sql = "SELECT uid,room_id FROM ".DB_NAME_PARTNER.".channel_user WHERE `id`={$id}";
		$row = $this->groupMysql->get_row($sql,ASSOC);
		$uid = $row['uid'];
		$groupId = $row['room_id'];

		//得到地域负责人ID
		$sql = "SELECT uid FROM ".DB_NAME_PARTNER.".channel_user WHERE room_id={$info['region_id']} AND `type`=7";
		$regionUin = $this->groupMysql->get_var($sql);
		$regionUin = intval($regionUin);

		$sql1 = "UPDATE ".DB_NAME_PARTNER.".channel_user SET region_id={$info['region_id']},up_uid={$info['region_id']},other_info='{$info['other_info']}',`flag`={$info['status']} WHERE `id`={$id}";
		$sql2 = "UPDATE ".DB_NAME_PARTNER.".channel_relation SET RegionId={$info['region_id']},RegionUin={$regionUin} WHERE GroupUin={$uid}";
		//$sql3 = "UPDATE ".DB_NAME_GROUP.".tbl_groups SET province={$info['province']},city={$info['city']},area={$info['area']},region_id={$info['region_id']} WHERE uin={$uid}";

		$this->groupMysql->start_transaction();
		if(!$this->groupMysql->query($sql1)){
			$this->groupMysql->rollback();
			return array('Flag'=>101,'FlagString'=>'修改失败');
		}
		if(!$this->groupMysql->query($sql2)){
			$this->groupMysql->rollback();
			return array('Flag'=>102,'FlagString'=>'同步失败');
		}/*
		if(!$this->db->query($sql3)){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'同步失败');
		}*/
		require_once 'room.class.php';
		$r = new room();
		$roomRst = $r->updateRoomRegion(array('province'=>$info['province'],'city'=>$info['city'],'area'=>$info['area'],'regionId'=>$info['region_id'],'groupId'=>$groupId));
		if($roomRst['Flag'] != 100){
			$this->groupMysql->rollback();
			return array('Flag'=>104,'FlagString'=>'更新房间地域信息失败');
		}
		
		if($info['status']==0){
			$roleData=array(
				'extparam'=>array(
					'Tag'=>'DeleteGroupRole',
					'GroupId'=>$groupId,
					'Uin'=>$uid,
					'RoleId'=>array(10187)
				)
			);
			$res=httpPOST(ROLE_API_PATH,$roleData);
			if($res['Flag']!=100){
				$this->groupMysql->rollback();
				return array('Flag'=>111,'FlagString'=>'删除站长角色失败');
			}
		}
		else{
			$roleData=array(
				'extparam'=>array(
					'Tag'=>'DeleteGroupRole',
					'GroupId'=>$groupId,
					'Uin'=>$uid,
					'RoleId'=>array(10187)
				)
			);
			$res=httpPOST(ROLE_API_PATH,$roleData);
			if($res['Flag']!=100){
				$this->groupMysql->rollback();
				return array('Flag'=>111,'FlagString'=>'删除站长角色失败');
			}
			$roleData=array(
				'extparam'=>array(
					'Tag'=>'AddGroupRole',
					'GroupId'=>$groupId,
					'Uin'=>$uid,
					'RoleId'=>10187
				)
			);
			$res=httpPOST(ROLE_API_PATH,$roleData);
			if($res['Flag']!=100){
				$this->groupMysql->rollback();
				return array('Flag'=>111,'FlagString'=>'写入站长角色失败');
			}
		}

		$this->groupMysql->commit();
		return array('Flag'=>100,'FlagString'=>'修改成功');
	}

	//室主
	private function roomerUpdate($id,$info,$uin,$original_roomid){
		if($id < 1 || $info['roomid'] < 1 || $uin < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$room = new room();
		$hasRoom = $room->roomExists($info['roomid']);
		if($hasRoom <= 0){
			return array('Flag'=>102,'FlagString'=>'房间不存在');
		}

		//得到房间上级ID
		$roomInfo = $room->getRoomInfo($info['roomid']);
		$roomInfo = $roomInfo['Info'];

		$this->groupMysql->start_transaction();
		$sql = "UPDATE ".DB_NAME_PARTNER.".channel_user SET room_id={$info['roomid']},up_uid={$roomInfo['group']},`flag`={$info['status']} WHERE `id`={$id}";
		if(!$this->groupMysql->query($sql)){
			$this->groupMysql->rollback();
			return array('Flag'=>103,'FlagString'=>'更换房间失败');
		}
		$sql = "UPDATE ".DB_NAME_PARTNER.".channel_relation SET ChannelId={$info['roomid']} WHERE OwnUin={$uin} AND ChannelId={$original_roomid}";
		if(!$this->groupMysql->query($sql)){
			$this->groupMysql->rollback();
			return array('Flag'=>104,'FlagString'=>'同步渠道关系失败');
		}

		$rst = $room->bindRoomer($info['roomid'],$uin);
		if($rst['Flag'] != 100){
			$this->groupMysql->rollback();
			return array('Flag'=>105,'FlagString'=>'同步房间信息失败');
		}
		$this->groupMysql->commit();
		return array('Flag'=>100,'FlagString'=>'修改成功');
	}

	//艺人
	private function artistUpdate($id,$info){
		if($id < 1 || $info['roomid'] < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		//房间是否存在
		$room = new room();
		$hasRoom = $room->roomExists($info['roomid']);
		if($hasRoom <= 0){
			return array('Flag'=>102,'FlagString'=>'房间不存在');
		}

		//得到房间上级ID
		$roomInfo = $room->getRoomInfo($info['roomid']);
		$roomInfo = $roomInfo['Info'];

		$sql = "UPDATE ".DB_NAME_PARTNER.".channel_user SET room_id={$info['roomid']},up_uid={$roomInfo['group']},`flag`={$info['status']} WHERE `id`={$id}";
		if($this->groupMysql->query($sql)){
			return array('Flag'=>100,'FlagString'=>'修改成功');
		}
		return array('Flag'=>103,'FlagString'=>'修改失败');
	}

	private function otherUpdate($id,$info){
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$setting = "`flag`={$info['status']}";
		if(isset($info['uid'])){
			$info['uid'] = intval($info['uid']);
			if($info['uid']>0){
				$uinInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'UinExist','Uin'=>$info['uid'])));
				if($uinInfo['Flag'] == 102){
					return array('Flag'=>103,'FlagString'=>'不存在这样的用户ID');
				}
				$setting .= ",uid={$info['uid']}";
			}else{
				return array('Flag'=>101,'FlagString'=>'参数错误');
			}
			
		}
		$sql = "UPDATE ".DB_NAME_PARTNER.".channel_user SET {$setting} WHERE `id`={$id}";
		if(!$this->groupMysql->query($sql)){
			return array('Flag'=>102,'FlagString'=>'更新失败');
		}
		return array('Flag'=>100,'FlagString'=>'更新成功');
	}

	private function delLoginSetting($uin,$type){
		if($uin < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "DELETE FROM ".DB_NAME_PARTNER.".room_login_role WHERE uid={$uin} AND `type`={$type}";
		if($this->groupMysql->query($sql)){
			return array('Flag'=>100,'FlagString'=>'成功');
		}
		return array('Flag'=>102,'FlagString'=>'失败');
	}
	
	public function channelUpdate($data,$id){
		$sql = "SELECT room_id,uid,`type` FROM ".DB_NAME_PARTNER.".channel_user WHERE `id`={$id}";
		$row = $this->groupMysql->get_row($sql);
		if($row['type'] == 8){
			//站长
			$rst = $this->stationHeadUpdate($data,$id);
			if($rst['Flag']==100 && $data['status']=='0'){
				$this->delLoginSetting($row['uid'],8);
			}
		}elseif($row['type'] == 9){
			//室主
			$rst = $this->roomerUpdate($id,$data,$row['uid'],$row['room_id']);
			if($rst['Flag']==100 && $data['status']=='0'){
				$this->delLoginSetting($row['uid'],9);
			}
		}elseif($row['type'] == 15){
			//艺人
			$rst = $this->artistUpdate($id,$data);
			if($rst['Flag']==100 && $data['status']=='0'){
				$this->delLoginSetting($row['uid'],15);
			}
		}else{
			$rst = $this->otherUpdate($id,$data);
		}
		return $rst;
	}
	
	//合作商的详情
	public function getPartnerInfo($id){
		
		if(empty($id))	return;
		$sql = 'SELECT * FROM '.DB_NAME_PARTNER.'.partner_list WHERE id="'.$id.'"';
		return $this->groupMysql->get_row($sql);
	}
	
	//某个合作商下的渠道
	public function getChannelsByPartnerId($partnerId){
		$sql = 'SELECT COUNT(*) FROM '.DB_NAME_PARTNER.'.channel_user as a,'.DB_NAME_PARTNER.'.partner_list as b WHERE a.partner_id=b.id AND a.partner_id="'.$partnerId.'"';
		$total = $this->groupMysql->get_var($sql);
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = 'SELECT a.*,b.name AS partner_name FROM '.DB_NAME_PARTNER.'.channel_user AS a,'.DB_NAME_PARTNER.'.partner_list AS b WHERE a.partner_id=b.id AND a.partner_id="'.$partnerId.'" LIMIT '.$page_arr['limit'];
			$list = $this->groupMysql->get_results($sql,'ASSOC');
		}
		return array('li'=>$list,'page'=>$page_arr['page'],'channelCategory'=>$this->getChannelCategory(),'Flag'=>100,'FlagString'=>'合作商 ['.$list[0]['partner_name'].'] 下的渠道');
	}
	
	public function partnerAdd($data){
		if(!isset($data['type']) || empty($data['name']) || !isset($data['card_type']) || empty($data['card_id']) || ($data['type']=='0'&&empty($data['business_id'])) || $data['bank_id']=='' || $data['mobile']=='' || $data['address']=='' || $data['zip_code']=='')
			return array('Flag'=>101,'FlagString'=>'参数错误');
		$sql = "INSERT INTO ".DB_NAME_PARTNER.'.partner_list(`type`,`name`,`card_type`,`card_id`,`province_id`,`city_id`,`city_name`,`business_id`,`bank_name`,`bank_id`,`mobile`,`phone`,`fax`,`qq`,`address`,`zip_code`,`uptime`,`flag`) VALUES("'.$data['type'].'","'.$data['name'].'","'.$data['card_type'].'","'.$data['card_id'].'","'.$data['province'].'","'.$data['city'].'","'.$data['city_name'].'","'.$data['business_id'].'","'.$data['bank_name'].'","'.$data['bank_id'].'","'.$data['mobile'].'","'.$data['phone'].'","'.$data['fax'].'","'.$data['qq'].'","'.$data['address'].'","'.$data['zip_code'].'","'.time().'","'.$data['status'].'")';
		if($this->groupMysql->query($sql)){
			return array('Flag'=>100,'FlagString'=>'添加成功');
		}
		return array('Flag'=>102,'FlagString'=>'添加失败');
	}
	
	public function partnerUpdate($data,$id){
		if(!isset($data['type']) || empty($data['name']) || !isset($data['card_type']) || empty($data['card_id']) || ($data['type']=='0'&&empty($data['business_id'])) || $data['bank_id']=='' || $data['mobile']=='' || $data['address']=='' || $data['zip_code']=='' || $id<=0)
			return array('Flag'=>101,'FlagString'=>'参数错误');
		$sql = "UPDATE ".DB_NAME_PARTNER.'.partner_list SET `type`="'.$data['type'].'",`name`="'.$data['name'].'",`card_type`="'.$data['card_type'].'",`card_id`="'.$data['card_id'].'",`province_id`="'.$data['province'].'",`city_id`="'.$data['city'].'",`city_name`="'.$data['city_name'].'",`business_id`="'.$data['business_id'].'",`bank_name`="'.$data['bank_name'].'",`bank_id`="'.$data['bank_id'].'",`mobile`="'.$data['mobile'].'",`phone`="'.$data['phone'].'",`fax`="'.$data['fax'].'",`qq`="'.$data['qq'].'",`address`="'.$data['address'].'",`zip_code`="'.$data['zip_code'].'",`flag`="'.$data['status'].'" WHERE id="'.$id.'"';
		if($this->groupMysql->query($sql)){
			return array('Flag'=>100,'FlagString'=>'修改成功');
		}
		return array('Flag'=>102,'FlagString'=>'修改失败');
	}
	
	public function setSalaryAndReward($id){
		$sql = 'SELECT id,uid,name,have_salary,salary,have_push_money,push_money FROM '.DB_NAME_PARTNER.'.channel_user WHERE id="'.$id.'"';
		return $this->groupMysql->get_row($sql,'ASSOC');
	}
	
	public function saveSalaryAndReward($data){
		if(!is_numeric($data['salary']) || !is_numeric($data['push_money']) || intval($data['id'])<=0)
			return array('Flag'=>101, 'FlagString'=>'参数错误');
		$sql = 'UPDATE '.DB_NAME_PARTNER.'.channel_user SET salary="'.$data['salary'].'",push_money="'.$data['push_money'].'" WHERE id="'.$data['id'].'"';
		$result = $this->groupMysql->query($sql);
		if($result)
			return array('Flag'=>100,'FlagString'=>'保存成功');
		return array('Flag'=>102,'FlagString'=>'保存失败');
	}
	
	public function getChannelUser($uin,$type){
		if($uin > 0){
			$sql = 'SELECT * FROM '.DB_NAME_PARTNER.'.channel_user WHERE uid = '.$uin.' AND `type` = '.$type;
			$row = $this->groupMysql->get_row($sql,'ASSOC');
			return empty($row) ? array('Flag'=>101,'FlagString'=>'不存在') : array('Flag'=>100,'FlagString'=>'存在','Data'=>$row);
		}
	}
	
	public function proxyAdd($info){
		if($info['Uin'] > 0 && $info['Uin'] == $info['ConfirmUin']){
			$uininfo_type = $this->getChannelUser($info['Uin'],16);
			if($uininfo_type['Flag'] ==100)return array('Flag'=>103,'FlagString'=>'用户已经是代理');
			$data = array('type'=>16,'uid'=>$info['Uin'],'name'=>'代理','descr'=>'代理','pact_id'=>'0','bank_id'=>'0','room_id'=>0,'status'=>1,'partner_id'=>100);
			$result=$this->channelAdd($data);
			if($result['Flag']!=100){
				return $result;
			}
			//$sql="SELECT uid FROM ".DB_NAME_IM.".sso_user_relate WHERE uin=".$info['Uin'];
			//$uid=$this->db->get_var($sql);
			//$sql="UPDATE ".DB_NAME_IM.".username SET channel_id=".$info['Uin']." WHERE uid=".$uid;
			//if($this->db->query($sql)){
			//	return array('Flag'=>100,'FlagString'=>'添加成功');
			//}
			return array('Flag'=>102,'FlagString'=>'添加失败');
		}else{
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
	}
	
	public function channelSync($ids){
		if(empty($ids))	return array('Flag'=>101,'FlagString'=>'参数错误');
		$id = implode(',',(array)$ids);
		foreach($ids as $key=>$value){
			$sql = "SELECT uid ,up_uid,room_id FROM ".DB_NAME_PARTNER.".channel_user WHERE id = {$value} AND `type`=9 ";
			$temp = $this->groupMysql->get_row($sql,"ASSOC");
			if(!empty($temp) && $temp['room_id'] >0){
				$array['ChannelId'] = $temp['room_id'];//房间id
				$array['OwnUin'] = $temp['uid'];//室主id
				$array['GroupId'] = $temp['up_uid'];//群id
				$sql = "SELECT uid ,up_uid,room_id FROM ".DB_NAME_PARTNER.".channel_user WHERE room_id = {$temp['up_uid']} AND `type`=8 ";
				$grouprow = $this->groupMysql->get_row($sql,"ASSOC");
				$array['GroupUin'] = $grouprow['uid'];//站长id
				$array['RegionId'] = $grouprow['up_uid'];//地域ID
				$sql = "SELECT uid ,up_uid,room_id FROM ".DB_NAME_PARTNER.".channel_user WHERE room_id = {$grouprow['up_uid']} AND `type`=7 ";
				$regionrow = $this->groupMysql->get_row($sql,"ASSOC");
				$array['RegionUin'] = $regionrow['uid'];//地域负责人ID
				$sql = "SELECT * FROM ".DB_NAME_PARTNER.".channel_relation WHERE ChannelId = ".$temp['room_id'];
				$row = $this->groupMysql->get_row($sql,"ASSOC");
				if(empty($row)){
					$sql = "INSERT INTO ".DB_NAME_PARTNER.".channel_relation (ChannelId ,OwnUin ,GroupId,GroupUin,RegionId,RegionUin) VALUES ({$array['ChannelId']},{$array['OwnUin']},{$array['GroupId']},{$array['GroupUin']},{$array['RegionId']},{$array['RegionUin']})";
				}else{
					$sql = "UPDATE ".DB_NAME_PARTNER.".channel_relation SET OwnUin={$array['OwnUin']},GroupId={$array['GroupId']},GroupUin={$array['GroupUin']},RegionId={$array['RegionId']},RegionUin={$array['RegionUin']} WHERE ChannelId = {$array['ChannelId']}";
				}
				if($this->groupMysql->query($sql)){
					$results[] = array('id'=>$value,'Flag'=>100,'FlagString'=>'同步成功');
				}else{
					$results[] = array('id'=>$value,'Flag'=>102,'FlagString'=>'同步失败');
				}
			}
		}
		return $results;
	}
	
	//分页
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


