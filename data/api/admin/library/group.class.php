<?php

/**
 *   群组操作接口
 *   文件: group.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class group
{
	const GROUPTYPE = 8;
	//构造函数
	public function __construct() {
		$this->db = db::connect(config('database','default'));
	}

	/**
	 *   插入或更新群组 
	 *   @param	array	$info	群数据	
	 *   @return	array	$array		执行的结果
	 */
	public function addGroup($data) {
		$data['uin'] = intval($data['uin']);
		
		//站是否存在
		$groupId = $data['uin'];
		$groupInfo = $this->GroupInfo(array('Group_id'=>$groupId));
		if($groupInfo['Flag'] == 100){
			return array('Flag'=>103,'FlagString'=>'站已经存在');
		}
		
		//模板是否存在
		$sql = "SELECT id FROM ".DB_NAME_TPL.".template WHERE id={$data['module_id']} AND status=1";
		if(!$this->db->get_var($sql)){
			return array('Flag'=>103,'FlagString'=>'模板不存在');
		}
		
		/*********************************************开站流程开始************************************************************/
		$this->db->start_transaction();
		
		//站基础信息
		$room_ui = json_encode((array)$data['rooms_ui']);
		$sql = "INSERT INTO ".DB_NAME_GROUP.".tbl_groups (`uin`,`groupid`,`name`,`uptime`,`currency_unit`,`open_num`,`module_id`,`room_ui`,`is_use`) VALUES ({$data['uin']},{$groupId},'{$data['GroupName']}',".time().",'{$data['currency_unit']}','{$data['open_num']}',{$data['module_id']},'{$room_ui}',{$data['is_use']})";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>105,'FlagString'=>'开站失败');
		}
		
		//站扩展信息
		$data['group_id'] = $groupId;
		if(!$this->setGroupExt($data)){
			$this->db->rollback();
			return array('Flag'=>106,'FlagString'=>'开站失败--写入站扩展信息失败');
		}
		
		//获取角色ID
		require dirname(__FILE__).'/role.class.php';
		$grouperIdentity1 = 4;
		$grouperIdentity2 = 1;
		$role = new Roles();
		$roleInfo = $role->getRoleByIdentity($grouperIdentity1, $grouperIdentity2, $data['module_id']);
		if($roleInfo['Flag'] != 100){
			return array('Flag'=>104,'FlagString'=>'无站长角色，请先添加');
		}
		$role_id = $roleInfo['Role']['id'];
		
		//渠道信息同步
		require dirname(__FILE__).'/partner_channel.class.php';
		$partner_channel = new partner_channel($data['uin']);
		$channelInfo = array('type'=>self::GROUPTYPE,'uid'=>$data['uin'],'up_uid'=>$data['uin'],'roomid'=>$data['uin'],'role_id'=>$role_id);
		$rst = $partner_channel->addChannel($channelInfo);
		if($rst['Flag'] != 100){
			$this->db->rollback();
			return array('Flag'=>107,'FlagString'=>'开站失败--渠道关系添加失败');
		}
		
		//添加角色
		$roleData=array(
			'extparam'=>array('Tag'=>'AddGroupRole','GroupId'=>$data['uin'],'Uin'=>$data['uin'],'RoleId'=>$role_id,'RoomId'=>0,'NewGroup'=>true,'ModuleId'=>$data['module_id'])
		);
		$res=httpPOST(ROLE_API_PATH,$roleData);
		if($res['Flag']!=100){
			$this->db->rollback();
			return array('Flag'=>110,'FlagString'=>'开站失败--角色授予失败');
		}
		
		//站长账号注册
		$initPass = md5('123456');//默认密码
		$userRegType = 2; //用户名注册
		$rst = httpPOST(SSO_API_PATH,array('param'=>array('GroupId'=>$data['uin']),'extparam'=>array('Tag'=>'RegPassport','User'=>$data['user'],'Pass'=>$initPass,'Nick'=>$data['user'],'Platform'=>$userRegType,'Uid'=>$data['uin'])));
		if($rst['Flag'] != 100){
			$this->db->rollback();
			return array('Flag'=>110,'FlagString'=>'开站失败--失败原因为: 站长账号,'.$rst['FlagString']);
		}
		$this->db->commit();
		
		//站信息同步
		$syncInfo = $this->syncInfo($data['uin']);
		if($syncInfo['Flag'] != 100){
			return array('Flag'=>110,'FlagString'=>'站信息同步失败,请检查开站信息');
		}
		
		//更新用户所属站
		/*
		$rst = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'EditPassport','Uin'=>$data['uin'],'Data'=>array('group_id'=>$data['uin']))));
		if($rst['Flag'] == 100){
			return array('Flag'=>100,'FlagString'=>'开站失败--更换用户所属站失败');
		}
		
		$sql = "UPDATE ".DB_NAME_IM.".new_username SET group_id ={$data['uin']} WHERE uin={$data['uin']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>108,'FlagString'=>'开站失败--更换用户所属站失败');
		}
		$sql = "UPDATE ".DB_NAME_IM.".basic_tbl SET group_id ={$data['uin']} WHERE uin={$data['uin']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>109,'FlagString'=>'开站失败--更换用户所属站失败');
		}*/
		return array('Flag'=>100,'FlagString'=>'开站成功');
	}

	public function editGroup($data){
		$table = DB_NAME_GROUP.'.tbl_groups';
		$groupInfo = $this->GroupInfo(array('Group_id'=>$data['group_id']));
		if($data['group_id'] < 1 || empty($data['domain']) || empty($data['icp']) || empty($data['icp_info'])){
			return array('Flag'=>101,'FlagString'=>'请填写完整的信息');
		}
		if($groupInfo['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'该站点不存在');
		}
		$room_ui = json_encode((array)$data['rooms_ui']);
		$this->db->start_transaction();
		$sql = "UPDATE {$table} SET name='{$data['GroupName']}',currency_unit='{$data['currency_unit']}',module_id={$data['module_id']},room_ui='{$room_ui}',open_num={$data['open_num']},is_use={$data['is_use']} WHERE groupid={$data['group_id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'操作失败');
		}

		if(!$this->setGroupExt($data)){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'操作失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'操作成功');
	}

	private function setGroupExt($data){
		$data['domain'] = addslashes($data['domain']);
		$data['icp'] = addslashes($data['icp']);
		$data['icp_info'] = addslashes($data['icp_info']);
		$data['ext'] = json_encode($data['ext']);
		$data['ktv_templates'] = isset($data['ktv_templates']) ? json_encode($data['ktv_templates']) : '';

		$row = $this->extInfo($data['group_id']);
		if($row['Flag'] == 100){
			$sql = "UPDATE ".DB_NAME_GROUP.".footer SET `domain`='{$data['domain']}',`icp`='{$data['icp']}',`icp_info`='{$data['icp_info']}',`template`='{$data['template']}',`version`='{$data['version']}',`ext`='{$data['ext']}',`ktv_template`='{$data['ktv_templates']}' WHERE id={$row['Info']['id']}";
		}else{
			$time = time();
			$sql = "INSERT INTO ".DB_NAME_GROUP.".footer(`group_id`,`domain`,`icp`,`icp_info`,`template`,`version`,`uptime`,`ext`,`ktv_template`) VALUES({$data['group_id']},'{$data['domain']}','{$data['icp']}','{$data['icp_info']}','{$data['template']}','{$data['version']}',{$time},'{$data['ext']}','{$data['ktv_templates']}')";
		}
		return $this->db->query($sql);
	}

	public function extInfo($group_id){
		$group_id = intval($group_id);
		if($group_id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//$sql = "SELECT f.*,g.name FROM ".DB_NAME_GROUP.".footer f LEFT JOIN ".DB_NAME_GROUP.".tbl_groups g ON f.group_id=g.groupid WHERE f.group_id={$group_id}";
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".footer WHERE group_id={$group_id}";
		$row = $this->db->get_row($sql,ASSOC);
		if($row){
			return array('Flag'=>100,'FlagString'=>'获取站底部信息','Info'=>$row);
		}
		return array('Flag'=>102,'FlagString'=>'获取站底部信息');
	}

	public function syncInfo($group_id){
		$group_id = intval($group_id);
		if($group_id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT group_id,domain FROM ".DB_NAME_GROUP.".footer WHERE group_id={$group_id}";
		$row = $this->db->get_row($sql,ASSOC);
		if(!$row){
			return array('Flag'=>102,'FlagString'=>'站信息同步失败');
		}
		
		//memcache信息同步
		$domain=array($row['group_id']);
		$rst=domain::main()->set_group_info($domain);
		if(empty($rst['groupid'])){
			return array('Flag'=>102,'FlagString'=>'站信息同步失败');
		}

		//同步积分规则
		$result = json_decode(socket_request(BEHAVIOR_SYNC_PATH.'/?cmd={"group_id":'.$group_id.'}'),true);
		if(intval($result['success']) != 1){
			return array('Flag'=>101,'FlagString'=>'业务规则同步失败,失败原因为: '.$result['reason']);
		}

		//同步数据库配置的
		$requestAddr = DATABASE_SYNC_PATH.'/?cmd={"type":"add","group_id":'.$group_id.'}';
		$result = json_decode(socket_request($requestAddr),true);
		if($result['Flag'] != 100){
			return array('Flag'=>101,'FlagString'=>'数据库配置同步失败,失败原因为: '.$result['FlagString']);
		}
		
		return array('Flag'=>100,'FlagString'=>'同步成功');
	}
	
	public function recommend($id){
		$sql = "SELECT recommend FROM ".DB_NAME_GROUP.".tbl_groups WHERE id={$id}";
		$recommend = $this->db->get_var($sql);
		if(empty($recommend)){
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_GROUP.".tbl_groups WHERE recommend=1";
			if($this->db->get_var($sql) >= 5){
				return array('Flag'=>101,'FlagString'=>'最多只能推荐5个站');
			}
		}
		$recommend = intval(! $recommend);
		$sql = "UPDATE ".DB_NAME_GROUP.".tbl_groups SET recommend = {$recommend} WHERE id={$id}";
		$rst = $this->db->query($sql);
		if( ! $rst) return array('Flag'=>102,'FlagString'=>'操作失败');
		return array('Flag'=>100,'FlagString'=>'操作成功');
	}


    public function listGroupUsed(){
        $sql = "SELECT groupid,`name` FROM ".DB_NAME_GROUP.".`tbl_groups` WHERE is_use = 1";
        $list = $this->db->get_results($sql, "ASSOC");
		$list[] = array('groupid'=>10000,'name'=>10000);
        return array("Flag"=>100, "Data"=>$list);
    }

	/**
	 *   查找群组信息
	 *   @param	array	$info	分页信息
	 *   @return	array	$array		返回需要查找的群组信息,以及分页信息
	 */
	public function listGroup($info) {
		if($info['Key_name'] && $info['Val']){
			$where .= ' AND '.$info['Key_name'].' = "'.$info['Val'].'"';
		}
		if($info['Id']>0){
			$where .= ' AND id  = '.$info['Id'];
		}
		if($info['Groupid']>0){
			$where .= ' AND groupid  = '.$info['Groupid'];
		}
		if(is_numeric($info['Recommend'])){
			$where .= ' AND recommend  = '.$info['Recommend'];
		}
        if(is_numeric($info['IsUse'])){
        	$isUse = intval($info['IsUse']);
        	$isUse = $isUse === 0 ? 0 : 1;
		}else{
			$isUse = 1;
		}
		$where .= ' AND is_use  = '.$isUse;
        if($info['TplId']){
            $where .= ' AND module_id  = '.$info['TplId'];
        }
		$table = DB_NAME_GROUP.'.tbl_groups';
		$dlhelper = new dlhelper($this->db);
		$lists = $dlhelper->findAllPage($table, ' 1 '.$where, "is_use desc");
		/*
		foreach($lists as $key=>$value){
			//$province_name = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetProvinceName','ProvinceId'=>$value['province'])));
			//$lists[$key]['province_name'] = $province_name['provinceName'];
			//$city_name = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCityName','CityId'=>$value['city'])));
			//$lists[$key]['city_name'] = $city_name['cityName'];
			//$region_name = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetAreaName','AreaId'=>$value['area'])));
			//$lists[$key]['region_name'] = $region_name['cityName'];
			$group_ext = domain::main()->GroupKeyVal($value['groupid'],'EXT');
			$group_ext = json_decode($group_ext, true);
			if(!empty($group_ext['kkyooDB_HOST']['value']) && !empty($group_ext['kkyooDB_NAME']['value']) && !empty($group_ext['kkyooDB_PASS']['value']) && !empty($group_ext['kkyooDB_PORT']['value'])){
				$groupMysql = domain::main()->GroupDBConn('mysql',$value['groupid']);
				if($groupMysql){
					$sql="SELECT id,name FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE `group`=".$value['groupid'];
					$roomList=$groupMysql->get_results($sql,'ASSOC');
					$lists[$key]['roomList']=$roomList;
					$lists[$key]['room_total']=count($roomList);
				}
			}else{
				$lists[$key]['roomList']=array();
				$lists[$key]['room_total'] = 0;
				continue;
			}
			
			//$sql="SELECT id,uid,room_id FROM ".DB_NAME_PARTNER.".channel_user WHERE type=9 AND up_uid=".$value['groupid'];

			//$roomManagerList=$this->db->get_results($sql,'ASSOC');
			if(count($roomManagerList)>0){
				foreach($roomManagerList as $key2=>$val2){
					$userInfo=httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$val2['uid'])));
					if($userInfo['Flag']==100){
						$userInfo['channel_id']=$val2['id'];
						$userInfo['uid']=$val2['uid'];
						$userInfo['room_id']=$val2['room_id'];
						$roomManagerList[$key2]=$userInfo;
					}
				}
			}
			$lists[$key]['roomManagerList']=$roomManagerList;
			$lists[$key]['room_manager_total']=count($roomManagerList);
			
			//$sql="SELECT id,uid,room_id FROM ".DB_NAME_PARTNER.".channel_user WHERE type=15 AND up_uid=".$value['groupid'];
			//$roomEntertainerList=$this->db->get_results($sql,'ASSOC');
			if(count($roomEntertainerList)>0){
				foreach($roomEntertainerList as $key2=>$val2){
					$userInfo=httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$val2['uid'])));
					if($userInfo['Flag']==100){
						$userInfo['channel_id']=$val2['id'];
						$userInfo['uid']=$val2['uid'];
						$userInfo['room_id']=$val2['room_id'];
						$roomEntertainerList[$key2]=$userInfo;
					}
				}
			}
			$lists[$key]['roomEntertainerList']=$roomEntertainerList;
			$lists[$key]['room_entertainer_total']=count($roomEntertainerList);
		}
		*/
		$page = $dlhelper->getPage();
		$array = array(
			'Flag'=>100,
			'FlagString'=>'成功',
			'lists' => $lists,
			'page'  => $page
			);
		return $array;
	}
	
	public function GroupInfo($info){
		if($info['Group_id'] > 0){
			$sql = "SELECT * FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid = {$info['Group_id']}";
			$row = $this->db->get_row($sql,'ASSOC');
			if(!empty($row)){
				return array('Flag'=>100,'FlagString'=>'成功','GroupInfo'=>$row);
			}
			return array('Flag'=>101,'FlagString'=>'站不存在');
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}
	
	public function opennum($info){
		if($info['Group_id'] > 0 && $info['Uin'] > 0){
			$groupinfo = $this->GroupInfo($info);
			if($groupinfo['Flag'] !==100){
				return $groupinfo;
			}
			$sql = "UPDATE ".DB_NAME_GROUP.".tbl_groups SET open_num = ".$info['Open_num']." WHERE groupid = {$info['Group_id']}";
			if($this->db->query($sql)){
				return array('Flag'=>100,'FlagString'=>'成功');
			}else{
				return array('Flag'=>101,'FlagString'=>'操作失败');
			}
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}

	public function editGame($info){
		$info['group_id'] = intval($info['group_id']);
		if($info['group_id'] < 1){
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
		foreach ((array)$info['row'] as $key => $value) {
			$info['row'][$key]['name'] = urlencode($value['name']);
			$info['row'][$key]['url'] = urlencode($value['url']);
		}
		$info['row'] = json_encode((array)$info['row']);
		$sql = "UPDATE ".DB_NAME_GROUP.".tbl_groups SET games='{$info['row']}' WHERE groupid={$info['group_id']}";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'操作失败');
		}
		return array('Flag'=>100,'FlagString'=>'成功');
	}

	public function gameInterfaceList($group_id){
		$group_id = intval($group_id);
		if($group_id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_CCS.".group_game_interface WHERE group_id={$group_id}";
		$list = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'success','List'=>$list);
	}

	public function gameInterfaceSave($data){
		$group_id = intval($data['group_id']);
		unset($data['group_id']);
		$data = (array)$data['row'];
		if($group_id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$this->db->start_transaction();
		$sql = "DELETE FROM ".DB_NAME_CCS.".group_game_interface WHERE group_id={$group_id}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>102,'FlagString'=>'保存失败');
		}
		foreach ($data as $key => $val) {
			$val['url'] = trim($val['url']);
			if(!empty($val['url'])){
				$val['url'] = urlencode($val['url']);
				$sql = "INSERT INTO ".DB_NAME_CCS.".group_game_interface(group_id,bigcase_id,case_id,parent_id,url) VALUES({$group_id},{$val['bigcase_id']},{$val['case_id']},{$val['parent_id']},'{$val['url']}')";
				if(!$this->db->query($sql)){
					$this->db->rollback();
					return array('Flag'=>103,'FlagString'=>'保存失败');
				}
			}
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'保存成功');
	}
	
	function practiceAccountList($group_id, $is_page = 1){
		$sql		  		= "SELECT COUNT(*) FROM ".DB_NAME_GROUP.".`practice_account` WHERE group_id = ".$group_id;
		if($is_page){
			$total		  		= $this->db->get_var($sql);
			list($page, $limit) = array_values($this->showPage($total));

			$sql 		  = "SELECT * FROM ".DB_NAME_GROUP.".`practice_account` WHERE group_id = ".$group_id." LIMIT ".$limit." ORDER BY `id`";
		}else{
			$sql 		  = "SELECT * FROM ".DB_NAME_GROUP.".`practice_account` WHERE group_id = ".$group_id." ORDER BY `id`";
		}
		$account_list = $this->db->get_results($sql, "ASSOC");
		
		$account_list_with_uin = array();
		foreach($account_list as $row){
			$row['account_details']  = json_decode($row['account_details'], true);
			$uin_str				 = "";
			foreach($row['account_details'] as $account){
				$uin_str .= $account['login'].",";
			}
			$row['uin_str'] 		 = substr($uin_str, 0, -1);
			
			$account_list_with_uin[] = $row; 
		}
		
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$account_list_with_uin, "Page"=>$page);
	}
	
	function savePracticeAccount($id, $group_id, $role_name, $accounts, $room_ids){
		$accounts = (array)$accounts;
		$room_ids = (array)$room_ids;
		
		if($accounts){
			$unique_accounts = array_unique($accounts);
            $groupMysql = domain::main()->GroupDBConn("mysql", $group_id);
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_IM.".`new_username` WHERE login IN ('".join("','", $unique_accounts)."') AND group_id = ".$group_id;
			if(count($unique_accounts) != $groupMysql->get_var($sql)){
				return array("Flag"=>102, "FlagString"=>"对应通行证不存在");
			}
		}
		
		if($room_ids){
			$exist_unique_room_ids = array_unique(array_filter($room_ids));
			$groupMysql = domain::main()->GroupDBConn('mysql',$group_id);
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".`rooms` WHERE id IN (".join(",", $exist_unique_room_ids).") AND `group` = ".$group_id;
			if(count($exist_unique_room_ids) != $groupMysql->get_var($sql)){
				return array("Flag"=>102, "FlagString"=>"对应房间有不存在的房间");
			}
		}
		
		$account_details = array();
		foreach($accounts as $k=>$login){
			$room_id = $room_ids[$k];
			$account_details[] = array("login"=>$login, "room_id"=>$room_id);
		}
		$account_details_json = addslashes(json_encode($account_details));
		if($id){
			$sql  = "UPDATE ".DB_NAME_GROUP.".`practice_account` SET `group_id` = '".$group_id."' ,`role_name` = '".$role_name."' ,`account_details` = '".$account_details_json."' WHERE `id` = '".$id."';" ;
		}else{
			$sql  = "INSERT INTO ".DB_NAME_GROUP.".`practice_account` (`group_id`, `role_name`, `account_details`) VALUES ('".$group_id."', '".$role_name."', '".$account_details_json."'); ";
		}
		$done = $this->db->query($sql);
		
		if($done){
			return array("Flag"=>100, "FlagString"=>"保存成功");
		}else{
			return array("Flag"=>102, "FlagString"=>"保存失败");
		}
	}
		
	function delPracticeAccount($id){
		$sql 	= "DELETE FROM ".DB_NAME_GROUP.".`practice_account` WHERE `id` = '".$id."'";
		$done	= $this->db->query($sql);
		
		if($done){
			return array("Flag"=>100, "FlagString"=>"删除成功");
		}else{
			return array("Flag"=>102, "FlagString"=>"删除失败");
		}
	}
	
	function practiceAccountDetail($id){
		$sql 	= "SELECT * FROM ".DB_NAME_GROUP.".`practice_account` WHERE `id` = '".$id."'";
		$detail = $this->db->get_row($sql, "ASSOC");
		
		$detail['account_details'] = json_decode($detail['account_details'], true);
		
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$detail);
	}
	
	function userIntention($start_time, $end_time, $group_id){
		$sql_condition = "";
		if($start_time){
			$sql_condition .= " AND `uptime` >= ".$start_time;
		}
		if($end_time){
			$sql_condition .= " AND `uptime` <= ".$end_time;
		}
		if($group_id){
			$sql_condition .= " AND `group_id` = ".$group_id;
		}
		
		$sql 				= "SELECT COUNT(*) FROM ".DB_NAME_GROUP.".`user_intention` WHERE 1 ".$sql_condition;
		$total 				= $this->db->get_var($sql);
		list($page, $limit) = array_values($this->showPage($total));
		
		$sql 		= "SELECT * FROM ".DB_NAME_GROUP.".`user_intention` WHERE 1 ".$sql_condition."  ORDER BY id DESC LIMIT ".$limit;
		$user_list 	= $this->db->get_results($sql, "ASSOC");
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$user_list, "Page"=>$page);
	}
	
	private function showPage($total, $perpage = 20) {
		if ($total > 0) {
			require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/library/page.class.php');
			$page = new extpage(array (
					'total' => $total,
					'perpage' => $perpage
			));
			$pageArr['page'] = $page->show();
			$pageArr['limit'] = $page->limit();
			unset ($page);
		}
		return $pageArr;
	}
}


