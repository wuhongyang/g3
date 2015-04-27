<?php
require_once 'room_common.class.php';

class RoomManage extends RoomCommon
{
	private $db;
	
	function __construct(){
		parent::__construct();
		$this->db = db::connect(config('database','default'));
		$this->groupMysql = domain::main()->GroupDBConn();
		$this->mongodb = domain::main()->GroupDBConn('mongo');
		$this->set_attr("NUM");
	}

	public function getRoomUi($roomsUi){
		if (empty($roomsUi)) {
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$where = ' id';
		if(is_array($roomsUi)){
			$where .= ' IN (';
			foreach ($roomsUi as $val) {
				$where .= "{$val},";
			}
			$where = rtrim($where, ',');
			$where .= ')';
		}else{
			$roomsUi = intval($roomsUi);
			$where .= "={$roomsUi}";
		}
		$sql = "SELECT id,name,pics FROM ".DB_NAME_SYSTEM_CONFIG.".tbl_rooms_ui WHERE {$where}";
		$results = $this->db->get_results($sql, ASSOC);
		if(!$results){
			return array('Flag'=>102,'FlagString'=>'无房间界面信息');
		}
		foreach ($results as $key => $val) {
			$pic = json_decode($val['pics'],true);
			if($pic['pic_id'] > 0){
				$sql = "SELECT img_path FROM ".DB_NAME_SYSTEM_CONFIG.".pic_manager WHERE id={$pic['pic_id']}";
				$results[$key]['img_path'] = $this->db->get_var($sql);
			}else{
				$results[$key]['img_path'] = '';
			}
		}
		return array('Flag'=>100,'FlagString'=>'房间信息','Results'=>$results);
	}

	public function setRoomUi($roomUi, $templateUi, $roomId, $groupId){
		$roomUi = intval($roomUi);
		$roomId = intval($roomId);
		$groupId = intval($groupId);

		if(!empty($templateUi) && $templateUi!=-1 ){
			$sql = "SELECT ktv_template FROM ".DB_NAME_GROUP.".footer WHERE group_id={$groupId} LIMIT 1";
			$templates_ui = $this->db->get_var($sql);
			if(empty($templates_ui)){
				return array('Flag'=>101,'FlagString'=>'非法设置');
			}
			$templates_ui = json_decode($templates_ui, true);
			if(! in_array($templateUi, $templates_ui)){
				return array('Flag'=>101,'FlagString'=>'无模板界面，保存失败');
			}
			$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET ui_version='{$roomUi}',template_ui='{$templateUi}' WHERE id={$roomId} LIMIT 1";
		}else{
			$sql = "SELECT room_ui FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid={$groupId} LIMIT 1";
			$rooms_ui = $this->db->get_var($sql);
			$rooms_ui = json_decode($rooms_ui, true);
			if(! in_array($roomUi, $rooms_ui)){
				return array('Flag'=>101,'FlagString'=>'无房间界面，保存失败');
			}
			$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET ui_version={$roomUi},template_ui='' WHERE id={$roomId} LIMIT 1";
		}
		
		if(!$this->groupMysql->query($sql)){
			return array('Flag'=>102,'FlagString'=>'保存失败');
		}
		return array('Flag'=>100,'FlagString'=>'保存成功');
	}

	public function roomInfo($roomId, $groupId){
		$roomId = intval($roomId);
		$sql = "SELECT * FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$roomId} AND `group`={$groupId} LIMIT 1";
		$row = $this->groupMysql->get_row($sql, ASSOC);
		if(!$row){
			return array('Flag'=>101,'FlagString'=>'无房间信息');
		}
		return array('Flag'=>100,'FlagString'=>'房间信息','RoomInfo'=>$row);
	}
	
	/*
    public function getUiPackage($data,$roomid,$group_id){
    	if($roomid){
    		$ui_version = $this->groupMysql->get_var("SELECT ui_version FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$roomid}");
    	}
		$where = '1';
        foreach((array)$data as $key=>$val){
            $where .= " AND p.`{$key}`='{$val}'";
        }
        if($group_id){
        	$where .= " AND FIND_IN_SET(".$group_id.", p.group_ids)";
        }
        $sql = "SELECT p.id,p.ui_id,p.name,r.pics FROM ".DB_NAME_SYSTEM_CONFIG.".tbl_ui_package AS p,".DB_NAME_SYSTEM_CONFIG.".tbl_rooms_ui AS r WHERE ({$where} AND p.ui_id=r.id)";
		$rst = $this->db->get_results($sql,'ASSOC');
		foreach($rst as $key=>$val){
			$pic = json_decode($val['pics'],true);
			$sql = "SELECT img_path FROM ".DB_NAME_SYSTEM_CONFIG.".pic_manager WHERE id={$pic['pic_id']}";
			$rst[$key]['img_path'] = $this->db->get_var($sql);
			if($val['id'] == $ui_version && $roomid){
				$curui = $rst[$key];
			}
		}
        return array('Flag'=>100,'FlagString'=>'ok','Result'=>(array)$rst,'Curui'=>$curui);
    }
	
	public function setUiPackage($uiid,$roomid,$group_id){
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_SYSTEM_CONFIG.".`tbl_ui_package` WHERE FIND_IN_SET(".$group_id.", group_ids) AND id = ".$uiid;
		$count = $this->db->get_var($sql);
		if(!$count){
			return array('Flag'=>102,'FlagString'=>'设置错误');
		}
		$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET ui_version={$uiid} WHERE id={$roomid}";
		if( ! $this->groupMysql->query($sql)){
			return array('Flag'=>101,'FlagString'=>'设置失败');
		}
		return array('Flag'=>100,'FlagString'=>'设置成功'); 
	}*/
	
	public function GetRoomRanks($roomid){
		$sql = "SELECT id,name FROM ".DB_NAME_BEHAVIOR.".business_param_group WHERE status=1";
		$param_group = $this->db->get_results($sql,'ASSOC');
		$sql = "SELECT ranks FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$roomid}";
		$ranks = (array)json_decode($this->groupMysql->get_var($sql),true);
		$param_rooms = array();
		foreach($ranks as $key=>$val){
			$ranks[$key]['name'] = urldecode($ranks[$key]['name']);
			$ranks[$key]['type'] = urldecode($ranks[$key]['type']);
			$param_rooms[] = $val['id'];
		}
		$rules = array();
		foreach($param_group as $key=>$val){
			if(in_array($val['id'],$param_rooms)){
				unset($param_group[$key]);
			}
		}
		
		return array('Flag'=>100,'FlagString'=>'ok','Ranks'=>$ranks,'Rules'=>$param_group);
	}
	
	public function SetRoomRanks($roomid,$post){
		if($roomid <= 0) return array('Flag'=>101,'FlagString'=>'参数错误');
		$post = (array)$post;
		/*$idin = implode(',',$post);
		$sql = "SELECT id,name,rules FROM ".DB_NAME_BEHAVIOR.".business_param_group WHERE id IN({$idin}) AND status=1";
		$result = (array)$this->db->get_results($sql,'ASSOC');
		$roomranks = $setranks = array();
		foreach($result as $key=>$val){
			$val['name'] = rawurlencode($val['name']);
			$val['id'] = intval($val['id']);
			$roomranks[$val['id']] = $val;
		}
		foreach($post as $key=>$val){
			$setranks[] = $roomranks[$val];
		}
		
		foreach($setranks as $key=>$val){
			$ids = implode(',',json_decode($val['rules']));
			$sql = "SELECT id,name FROM ".DB_NAME_BEHAVIOR.".business_rule WHERE id IN({$ids})";
			$rules = $this->db->get_results($sql,'ASSOC');
			foreach($rules as $k=>$val){
				$rules[$k]['name'] = rawurlencode($rules[$k]['name']);
			}
			$setranks[$key]['rules'] = $rules;
		}
		
		$setranks = json_encode($setranks);*/
		$setranks = json_encode($post);
		$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET ranks='{$setranks}' WHERE id={$roomid}";
		$rst = $this->groupMysql->query($sql);
		if( ! $rst){
			return array('Flag'=>101,'FlagString'=>'操作失败');
		}
		return array('Flag'=>101,'FlagString'=>'操作成功');
	}
	
	public function getUserRooms($uin){
		if(!is_numeric($uin)||$uin<=0){
			return array('Flag'=>102,'FlagString'=>'参数错误');
		}
		$isGroupManage=current(getChannelUserInfo($uin,8));
		$groupId=0;
		if($isGroupManage['flag']>0){
			$sql="SELECT groupid FROM ".DB_NAME_GROUP.".tbl_groups WHERE uin=".$uin;
			$groupId=$this->db->get_var($sql);
			$sql="SELECT * FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE ownuin=$uin OR `group`=$groupId LIMIT 1";
		}
		else{
			$sql="SELECT * FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE ownuin=$uin LIMIT 1";
		}
		$roomInfo=$this->groupMysql->get_row($sql,'ASSOC');
		
		if(empty($roomInfo)){
			return array('Flag'=>101,'FlagString'=>'您没有任何房间的管理权限');
		}else{
			return array('Flag'=>100,'FlagString'=>'ok','Result'=>$roomInfo);
		}
	}
	
	//获取房间欢迎词等信息
	function getRoomInfo($uin,$roomId){
		//判断
		if(!is_numeric($uin)||$uin<=0||!is_numeric($roomId)||$roomId<=0){
			return array("Flag"=>102,"FlagString"=>"缺少必要参数");
		}
		//查找
		//$info=$this->get_roominfo($roomId);
		$sql = "SELECT * FROM ".DB_NAME_NEW_ROOMS.".rooms AS a WHERE id={$roomId}";
		$info = $this->groupMysql->get_row($sql,ASSOC);

		$rst=$this->getRoomRole(array("RoomId"=>$roomId,"Uid"=>$uin));
		$info['Role']=$rst['Data'];
		
		$sql="SELECT type FROM ".DB_NAME_PARTNER.".room_login_role WHERE uid=".$uin." AND room_id=".$roomId;
		$info['SelectRoles']=$this->groupMysql->get_results($sql);
		
		if($info){
			return array("Flag"=>100,"info"=>$info, "FlagString"=>"查询成功");
		}else{
			return array("Flag"=>101,"FlagString"=>"您不是室主，没有这个权限");
		}
	}
	
	//保存房间欢迎词等信息
	function saveRoomInfo($param){
		$name 		 = $param['name'];
		$description = $param['description'];
		$salutatory  = $param['salutatory'];
		$room_id	 = $param['room_id'];
		$uin		 = $param['Uin'];
		$bgalign	 = $param['bgalign'];
		$type 		 = $param['type'];
		$robot_base_num   = $param['robot_base_num'];
		$robot_num   = $param['robot_num'];
		//判断
		if(empty($name)){
			return array("Flag"=>101, "FlagString"=>"房间名称不能为空");
		}
		if(empty($type)){
			return array("Flag"=>101, "FlagString"=>"请选择房间角色");
		}
		if(!is_numeric($robot_base_num)||$robot_base_num<0){
			return array("Flag"=>101, "FlagString"=>"请填写正确的机器人基数");
		}
		if(!is_numeric($robot_num)||$robot_num<0){
			return array("Flag"=>101, "FlagString"=>"请填写正确的每个真人对应机器人数");
		}
		if($uin < 0 || $room_id < 0){
			return array("Flag"=>102, "FlagString"=>"缺少必要参数");
		}
		//更新
		$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".`rooms` SET `name`='{$name}',description='{$description}',salutatory='{$salutatory}',bgalign='{$bgalign}',robot_base_num='{$robot_base_num}',robot_num='{$robot_num}' WHERE `id`={$room_id}";
		$done = $this->groupMysql->query($sql);
		
		$sql = "SELECT `type` FROM ".DB_NAME_PARTNER.".`channel_user` WHERE uid = '".$uin."'";
		$rst = $this->groupMysql->get_results($sql,'ASSOC');
		$types = array();
		foreach($rst as $one){
			$types[] = $one['type'];
		}

		$done2 = false;
		if(in_array($type, $types)){
			$sql = "SELECT id FROM ".DB_NAME_PARTNER.".`room_login_role` WHERE uid = '".$uin."' AND room_id = '".$room_id."'";
			$existid = $this->groupMysql->get_var($sql);
			if($existid){
				$sql = "UPDATE `".DB_NAME_PARTNER."`.`room_login_role` SET `uid`='".$uin."',`room_id`='".$room_id."',`type`='".$type."' WHERE `id`='".$existid."'";
			}else{
				$sql = "INSERT INTO `".DB_NAME_PARTNER."`.`room_login_role`(`id`,`uid`,`room_id`,`type`) VALUES ( NULL,'".$uin."','".$room_id."','".$type."')";
			}
			$done2 = $this->groupMysql->query($sql);
		}
		
		if($done && $done2){
			return array("Flag"=>100, "FlagString"=>"更新成功");
		}else{
			return array("Flag"=>101, "FlagString"=>"更新失败");
		}
	}
	
	private function getRoomRole($json){
		$room_id = $json['RoomId'];
		$uid = $json['Uid'];
		$rst = array();
		
		$info = getChannelRelation($room_id);
		if($info['GroupUin'] == $uid){
			$rst['8'] = "站长";
		}
		if($info['OwnUin'] == $uid){
			$rst['9'] = "室主";
		}
		
		$sql = "SELECT id FROM ".DB_NAME_PARTNER.".`channel_user` WHERE `room_id` = '".$room_id."' AND `type` = 15 AND uid = ".$uid.' AND flag=1';
		$exist = $this->groupMysql->get_var($sql);
		if(!empty($exist)){
			$rst['15'] = "艺人";
		}
		
		return array("Flag"=>100, "Data"=>$rst, "FlagString"=>"查询成功");
	}
	
	//获取房间进入信息
	function getEnterInfo($roomId){
		//判断
		if(!is_numeric($roomId)||$roomId<=0){
			return array("Flag"=>102,"FlagString"=>"缺少必要参数");
		}
		//获取房间信息
		$sql="SELECT id,status,passwd FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id=".$roomId;
		$info=$this->groupMysql->get_row($sql,"ASSOC");
		if(!$info){
			return array("Flag"=>101,"FlagString"=>"不存在该房间");
		}
		//获得指定成员或者黑名单成员
		$table='roommember_tbl';
		$info['member']=$this->_getMember($table,$roomId);
		$table='roomdeny_tbl';
		$info['deny']=$this->_getMember($table,$roomId);
		return array("Flag"=>100,"FlagString"=>"查询成功","info"=>$info);
	}
	
	//保存房间进入信息
	function saveEnterInfo($param){
		//判断
		$Uin 			= $param['Uin'];
		$status 		= $param['status'];
		$passwd 		= $param['passwd']?"'".$param['passwd']."'":"NULL";
		$member_value 	= $param['member_value']?$param['member_value']:array();
		$deny_value 	= $param['deny_value']?$param['deny_value']:array();
		$room_id		= $param['room_id'];
		if(!($Uin && $status && $room_id)){
			return array("Flag"=>102, "FlagString"=>"缺少必要参数");
		}
		//验证帐号
		if(!empty($member_value)){
			//$passManager=new PassManager();
			foreach($member_value as $val){
				if(!is_numeric($val)||$val<=0){
					return array('Flag'=>103,'FlagString'=>'指定成员中ID为：'.$val.'的用户不存在');
				}
				//$ssoInfo=$passManager->uin2uid($val);
				$ssoInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$val)));
				if($ssoInfo['Flag'] != 100){
					return array('Flag'=>103,'FlagString'=>'指定成员中ID为：'.$val.'的用户不存在');
				}
			}
		}
		if(!empty($deny_value)){
			//$passManager=new PassManager();
			foreach($deny_value as $val){
				if(!is_numeric($val)||$val<=0){
					return array('Flag'=>103,'FlagString'=>'黑名单中ID为：'.$val.'的用户不存在');
				}
				//$ssoInfo=$passManager->uin2uid($val);
				$ssoInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$val)));
				if($ssoInfo['Flag'] != 100){
					return array('Flag'=>103,'FlagString'=>'黑名单中ID为：'.$val.'的用户不存在');
				}
			}
		}
		//更新成员和黑名单列表
		$this->groupMysql->start_transaction();
		$m_done = $this->_insertMember("roommember_tbl", $member_value, $room_id);
		$d_done = $this->_insertMember("roomdeny_tbl", $deny_value, $room_id);
		if(!($m_done && $d_done)){
			$this->groupMysql->rollback();
			return array("Flag"=>101, "FlagString"=>"更新失败");
		}
		//更新房间信息
		$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".`rooms` SET `passwd`=".$passwd.",`status`='".$status."' WHERE `id`='".$room_id."'";
		$done = $this->groupMysql->query($sql);
		if($done){
			$this->groupMysql->commit();
			return array("Flag"=>100, "FlagString"=>"更新成功");
		}else{
			$this->groupMysql->rollback();
			return array("Flag"=>101, "FlagString"=>"更新失败");
		}
	}
	
	//获取房间排麦信息
	function getOrderInfo($roomId){
		//判断
		if(!is_numeric($roomId)||$roomId<=0){
			return array("Flag"=>102,"FlagString"=>"缺少必要参数");
		}
		//获取房间信息
		$sql="SELECT id,mike_power,main_video_time FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id=".$roomId;
		$info=$this->groupMysql->get_row($sql,"ASSOC");
		if(!$info){
			return array("Flag"=>101,"FlagString"=>"不存在该房间");
		}
		$info['member']=$this->_toArray($this->get_member("mike_members",$roomId));
		return array("Flag"=>100,"FlagString"=>"查询成功","info"=>$info);
	}
	
	//保存房间排麦信息
	function saveOrderInfo($param){
		//判断
		$uin 			 = $param['Uin'];
		$room_id 		 = $param['room_id'];
		$mike_power 	 = $param['mike_power'];
		$member 		 = $param['member']?$param['member']:array();
		$main_video_time = $param['main_video_time'];

		if(!($uin && $room_id && $mike_power && $main_video_time )){
			return array("Flag"=>102, "FlagString"=>"缺少必要参数");
		}
		//更新成员信息
		$this->groupMysql->start_transaction();
		$done = $this->_insertMember("mike_members", $member, $room_id);
		if(!$done){
			$this->groupMysql->rollback();
			return array("Flag"=>101, "FlagString"=>"更新失败");
		}
		//更新房间信息
		$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".`rooms` SET `mike_power`=".$mike_power.",`main_video_time`='".$main_video_time."' WHERE `id`='".$room_id."'";
		$done = $this->groupMysql->query($sql);
		if($done){
			$this->groupMysql->commit();
			return array("Flag"=>100, "FlagString"=>"更新成功");
		}else{
			$this->groupMysql->rollback();
			return array("Flag"=>101, "FlagString"=>"更新失败");
		}
	}
	
	//房间管理员查看
	function getManagerInfo($roomId){
		//判断
		if(!is_numeric($roomId)||$roomId<=0){
			return array("Flag"=>102,"FlagString"=>"缺少必要参数");
		}
		$roleData = array(
			'extparam' => array(
				'Tag' => 'GetRole',
				'ChannelId' => $roomId,
			)
		);
		$res = httpPOST(ROLE_API_PATH, $roleData);
		if($res['Flag'] == 100){
			return array("Flag"=>100, "FlagString"=>"查询成功", "roles"=>$res['Roles']);
		}else{
			return array("Flag"=>101,"FlagString"=>"查询失败");
		}
		//获取房间信息
		/*$sql="SELECT id FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id=".$roomId;
		$info=$this->groupMysql->get_row($sql,"ASSOC");
		if(empty($info)){
			return array("Flag"=>101,"FlagString"=>"没有这个房间");
		}
		$info['member']=$this->_toArray($this->get_member("roommanager_tbl",$roomId));
		return array("Flag"=>100,"FlagString"=>"查询成功","info"=>$info);*/
	}
	
	//房间管理员更新
	function saveManagerInfo($param){
		//判断
		$uin 	 = intval($param['Uin']);
		$room_id = intval($param['room_id']);
		$role_id = intval($param['role_id']);
		$member  = $param['member'];
		
		if($role_id<=0){
			return array('Flag'=>101,'FlagString'=>'请选择类型');
		}
		if(!($uin && $room_id)){
			return array("Flag"=>102, "FlagString"=>"缺少必要参数");
		}
		
		$sql="SELECT `group` FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id=".$room_id;
		$group_id=$this->groupMysql->get_var($sql);
		
		//验证角色
		$roleData=array(
			'extparam'=>array(
				'Tag'=>'UinRole',
				'GroupId'=>$group_id
			)
		);
		$roles=httpPOST(ROLE_API_PATH,$roleData);
		if($roles['Flag']!=100){
			return $roles;
		}
		if(!in_array($role_id,$roles['Roles'])){
			return array('Flag'=>102,'FlagString'=>'没有这个管理类型');
		}
		//验证帐号
		//$passManager=new PassManager();
		foreach($member as $val){
			if(!is_numeric($val)||$val<=0){
				return array('Flag'=>103,'FlagString'=>'ID为：'.$val.'的用户不存在');
			}
			//$ssoInfo=$passManager->ssoInfo($val);
			$ssoInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$val)));
			if($ssoInfo['Flag'] != 100){
				return array('Flag'=>103,'FlagString'=>'ID为：'.$val.'的用户不存在');
			}
			//if($ssoInfo['Info']['group_id']!=$group_id){
			//	return array('Flag'=>104,'FlagString'=>'ID为：'.$val.'的用户不是该站的会员');
			//}
		}
		
		//删除站房间管理原来的角色
		$sql="SELECT id FROM ".DB_NAME_TPL.".role WHERE id IN (".implode(',',$roles['Roles']).") AND role_show_1=2 AND role_show_2=1 AND status=1";
		$roleList=$this->db->get_results($sql,'ASSOC');
		if(empty($roleList)){
			return array('Flag'=>101,'FlagString'=>'这个站没有配置管理类型');
		}
		$roleIds=array();
		foreach($roleList as $val){
			$roleIds[]=$val['id'];
		}
		
		/*$sql="SELECT * FROM ".DB_NAME_NEW_ROOMS.".roommanager_tbl WHERE room_id=$room_id";
		$list=$this->groupMysql->get_results($sql,'ASSOC');
		foreach($list as $val){
			if(empty($val['uin'])){
				continue;
			}
			$roleData=array(
				'extparam'=>array(
					'Tag'=>'DeleteGroupRole',
					'GroupId'=>$group_id,
					'Uin'=>$val['uin'],
					'RoleId'=>$roleIds,
					'RoomId'=>$room_id
				)
			);
			$res=httpPOST(ROLE_API_PATH,$roleData);
			if($res['Flag']!=100){
				return array('Flag'=>111,'FlagString'=>'操作失败');
			}	
		}*/
		$roleData = array(
			'extparam' => array(
				'Tag' => 'GetRole',
				'ChannelId' => $room_id,
				'RoleId' => $role_id
			)
		);
		$res = httpPOST(ROLE_API_PATH, $roleData);
		if($res['Flag'] == 100){
			foreach ($res['Roles'] as $key=>$val){
				$roleData1 = array(
					'extparam' => array(
						'Tag'     => 'DeleteGroupRole',
						'GroupId' => $group_id,
						'RoleId'  => array($role_id),
						'RoomId'  => $room_id,
						'Uin'     => $val['uin'],
					)
				);
				$res = httpPOST(ROLE_API_PATH, $roleData1);
				if($res['Flag'] != 100){
					return array('Flag' => $res['Falg'], 'FlagString' => $res['FlagString']);
				}
			}
			if(empty($member)){
				return array('Flag' => 100, 'FlagString' => '更新成功');
			}
		}
		//添加角色
		foreach($member as $val){
			$roleData2=array(
				'extparam'=>array(
					'Tag'=>'AddGroupRole',
					'GroupId'=>$group_id,
					'Uin'=>$val,
					'RoleId'=>$role_id,
					'RoomId'=>$room_id
				)
			);
			$res=httpPOST(ROLE_API_PATH,$roleData2);
			if($res['Flag'] != 100){
				return array('Flag' => $res['Flag'], 'FlagString' => $res['FlagString']);
			}
		}
		return array('Flag'=>100, 'FlagString'=>'操作成功');
			
		
		//更新管理员表信息
		/*$done = $this->_insertMember("roommanager_tbl", $member, $room_id);
		if($done){
			return array("Flag"=>100, "FlagString"=>"更新成功");
		}else{
			return array("Flag"=>101, "FlagString"=>"更新失败");
		}*/
	}
	
	//被踢出ID或IP查看
	function getReleaseInfo($roomId,$type){
		//判断
		if(!is_numeric($roomId)||$roomId<=0){
			return array("Flag"=>102,"FlagString"=>"缺少必要参数");
		}
		$sql="SELECT id FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id=".$roomId;
		$info=$this->groupMysql->get_row($sql,"ASSOC");
		if(!$info){
			return array("Flag"=>101,"FlagString"=>"不存在该房间");
		}
		$type=="id"?$table="roomkick_tbl":$table="roomkick_ip_tbl";
		$where="room_id='".$roomId."'";
		$dl=new dlhelper($this->groupMysql);
		$dl->isShow=1;
		$dl->showNum=10;
		$lists=$dl->findAllPage(DB_NAME_NEW_ROOMS.".".$table,$where,'kick_time DESC');
		$page=$dl->getPage();
		return array("Flag"=>100,"FlagString"=>"查询成功","info"=>$lists,"page"=>$page);
	}
	
	//被踢出ID或IP释放
	function release($param, $type){
		$Uin 	 = $param['Uin'];
		$id      = $param['id'];
		if(!($Uin && $id)){
			return array("Flag"=>102, "FlagString"=>"缺少必要参数");
		}
		$table = $type == "ip"?"roomkick_ip_tbl":"roomkick_tbl";
		foreach($id as $one){
			$str .= ' OR `id` = '.$one;
		}
		$str   = substr($str,3);
		$sql   = "DELETE FROM ".DB_NAME_NEW_ROOMS.".`".$table."` WHERE ".$str;
		$done  = $this->groupMysql->query($sql);
		if($done){
			return array("Flag"=>100, "FlagString"=>"删除成功");
		}else{
			return array("Flag"=>101, "FlagString"=>"删除失败");
		}
	}
	
	//添加封杀IP或ID
	function addDeny($param, $type){
		$Uin			= $param['Uin'];
		$room_id		= $param['room_id'];
		$kick_ip		= $param['kick_ip'];
		$kick_id		= $param['kick_id'];
		$kick_nick		= $param['kick_nick'];
		$operator_id	= $param['operator_id'];
		$operator_nick	= $param['operator_nick'];
		$condition = $Uin && $room_id && $kick_id && $kick_nick && $operator_id && $operator_nick;
		$type == "ip"?$condition = $condition && $kick_ip:"";
		if(!$condition){
			return array("Flag"=>102, "FlagString"=>"缺少必要参数");
		}
		if($type == "id"){
			$sql = "INSERT INTO ".DB_NAME_NEW_ROOMS.".`roomkick_tbl`(`id`,`room_id`,`kick_id`,`kick_nick`,`operator_id`,`operator_nick`,`kick_time`)". 
			"VALUES ( NULL,'".$room_id."','".$kick_id."','".$kick_nick."','".$operator_id."','".$operator_nick."','".time()."')";
		}elseif($type == "ip"){
			$sql = "INSERT INTO ".DB_NAME_NEW_ROOMS.".`roomkick_ip_tbl`(`id`,`room_id`,`kick_ip`,`kick_id`,`kick_nick`,`operator_id`,`operator_nick`,`kick_time`)".
					"VALUES ( NULL,'".$room_id."','".$kick_ip."','".$kick_id."','".$kick_nick."','".$operator_id."','".$operator_nick."','".time()."')";
		}
		$done = $this->groupMysql->query($sql);
		if($done){
			return array("Flag"=>100, "FlagString"=>"添加成功");
		}else{
			return array("Flag"=>101, "FlagString"=>"添加失败");
		}
	}
	
	function getRoomStatus($param){
		$sql = "SELECT id,status FROM ".DB_NAME_NEW_ROOMS.".`rooms` where ownuin = '".$param['ownuin']."' limit 1";
		$roomInfo = $this->groupMysql->get_row($sql, "ASSOC");
		return array("Flag"=>100, "FlagString"=>"查询成功", "status"=>$roomInfo['status'],"Roomid"=>$roomInfo['id']);	
	}
	
	function getRoomNotice($param){
		$sql = "SELECT roomnotice FROM ".DB_NAME_NEW_ROOMS.".`rooms` where id = '".$param['Roomid']."' limit 1";
		$roomNotice = $this->groupMysql->get_var($sql, "ASSOC");
		return array("Flag"=>100, "FlagString"=>"查询成功", "roomNotice"=>$roomNotice);	
	}
	
	function setRoomNotice($param){
		$sql = 'UPDATE '.DB_NAME_NEW_ROOMS.'.`rooms` SET roomnotice = \''.$param['RoomNotice'].'\' where id = '.$param['Roomid'];
		$query = $this->groupMysql->query($sql, "ASSOC");
		return array("Flag"=>100, "FlagString"=>"设置成功");	
	}
	
	/**
	 *   房间在线人数统计
	 *   @param	int $roomId 房间id 
	 *   @param	array $data 查询条件 
	 *   @return array $array 返回查询结果
	 */
	public function getRoomStatistics($roomId,$data){
		if(!is_numeric($roomId)||$roomId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$query=array('roomid'=>intval($roomId));
		if(!empty($data['Start'])){
			$query['createtime']['$gte']=intval(strtotime($data['Start']));
		}
		if(!empty($data['End'])){
			$query['createtime']['$lte']=intval(strtotime($data['End']));
		}
        $pageArr=$this->showPage($total,20);
        list($offset,$rows)=explode(',',$pageArr['limit']);
        $result=$this->mongodb->get_results(
        	DB_NAME_NEW_ROOMS.'.tbl_roomsuser_history',
            $query,
            array(
                'sort'=>array('createtime'=>-1),
                'limit'=>array('offset'=>$offset,'rows'=>$rows)
            )
        );
		$pageArr=$this->showPage(count($result),20);
		return array('Flag'=>100,'FlagString'=>'房间在线人数明细','Result'=>$result,'Page'=>$pageArr['page']);
	}
	
	private function showPage($total,$perpage=10){
		require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/library/page.class.php');
        $page=new extpage(array (
            'total'=>$total,
            'perpage'=>$perpage
        ));
        $pageArr['page']=$page->simple_page($total);
        $pageArr['limit']=$page->simple_limit();
        unset($page);
        return $pageArr;
	}
	
	//根据表获得指定成员
	private function _getMember($table, $room_id){
		$result = $this->get_member($table, $room_id);
		$member = $this->_toArray($result);
		return $member;
	}
	
	//根据表对比后，插入指定成员或者黑名单成员
	private function _insertMember($table, $member_value, $room_id){
		$room_col = $table == 'mike_members'?'roomid':'room_id';
		$members 	 = $this->_getMember($table, $room_id);
		$add_members = array();
		$del_members = array();
		foreach($member_value as $one){
			if(!in_array($one, $members)){
				array_push($add_members, $one);
			}
		}
		foreach($members as $one){
			if(!in_array($one, $member_value)){
				array_push($del_members, $one);
			}
		}
		$add = true;
		$del = true;
		if($add_members){
			foreach($add_members as $one){
				$m_str .= "('".$room_id."','".$one."'),";
			}
			$m_str = substr($m_str, 0, -1);
			$sql   = "INSERT INTO ".DB_NAME_NEW_ROOMS.".`".$table."`(`".$room_col."`,`uin`) VALUES ".$m_str;
			$add   =  $this->groupMysql->query($sql);
		}
		if($del_members){
			foreach($del_members as $one){
				$d_str .= "OR `uin` = '".$one."' ";
			}
			$d_str = substr($d_str, 2);
			$sql   = "DELETE FROM ".DB_NAME_NEW_ROOMS.".`".$table."` WHERE `".$room_col."`='".$room_id."' AND ".$d_str;
			$del   = $this->groupMysql->query($sql);
		}
		return $add&&$del;
	}
	
	//将单个字段查询二维数据换成一维数组
	private function _toArray($arr){
		$result = array();
		foreach($arr as $one){
			$result[] =  $one[1];
		}
		return $result;
	}
}