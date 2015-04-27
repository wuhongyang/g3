<?php

/**
 *   站管理接口
 *   文件: group.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
 
class GroupManage{
	protected $expire = 180;
	
	public function __construct(){
		$this->db = db::connect(config('database','default'));
		$this->mongodb = domain::main()->GroupDBConn('mongo');
		$this->cache = cache::connect(config('cache','memcache'));
		$this->groupMysql = domain::main()->GroupDBConn();
	}
	
	/**
	 *   站信息
	 *   @param	int $uin 用户uin
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回需要查找的站信息
	 */
	public function getGroupInfo($uin=0,$groupId=0,$isDetails=false){
		$uin=intval($uin);
		$groupId=intval($groupId);
		if($uin<=0&&$groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$where='1';
		if($uin>0){
			$where.=" AND g.uin=$uin";
		}
		if($groupId>0){
			$where.=" AND g.groupid=$groupId";
		}
		$sql="SELECT * FROM ".DB_NAME_GROUP.".tbl_groups AS g LEFT JOIN ".DB_NAME_GROUP.".footer AS f ON g.groupid=f.group_id WHERE $where";
		$groupInfo=$this->db->get_row($sql,"ASSOC");
		if(empty($groupInfo)){
			return array('Flag'=>102,'FlagString'=>'没有该站');
		}
		
		if($isDetails===true){
			//$provinceName=httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetProvinceName','ProvinceId'=>$groupInfo['province'])));
			//$groupInfo['province_name']=$provinceName['provinceName'];
			
			//$cityName=httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCityName','CityId'=>$groupInfo['city'])));
			//$groupInfo['city_name']=$cityName['cityName'];
			
			//$sql="SELECT nickname,qq FROM ".DB_NAME_PARTNER.".service WHERE find_in_set(".$groupInfo['groupid'].",group_id)";
			//$serviceInfo=$this->db->get_row($sql,'ASSOC');
			//$groupInfo['serviceInfo']=$serviceInfo;
			
			$sql="SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE `group`=".$groupInfo['groupid'];
			$groupInfo['room_total']=$this->groupMysql->get_var($sql);
			//室主
			$sql="SELECT COUNT(*) FROM ".DB_NAME_PARTNER.".channel_user WHERE type=9 AND up_uid=".$groupInfo['groupid'].' AND `flag`=1';
			$groupInfo['room_manager_total']=$this->groupMysql->get_var($sql);
			//艺人
			$sql="SELECT COUNT(*) FROM ".DB_NAME_PARTNER.".channel_user WHERE type=15 AND up_uid=".$groupInfo['groupid'].' AND `flag`=1';
			$groupInfo['room_entertainer_total']=$this->groupMysql->get_var($sql);
			//代理
			//$sql="SELECT COUNT(*) FROM ".DB_NAME_GROUP.".groups_proxy WHERE group_id=".$groupInfo['groupid'];
			//$groupInfo['agent_total']=$this->groupMysql->get_var($sql);
			
		}
		
		return array('Flag'=>100,'FlagString'=>'站信息','Result'=>$groupInfo);
	}
	
	/**
	 *   获取站下所有房间信息
	 *   @param	int $groupId 站groupid
	 *   @param	boole $surplusEntertainer 是否需要显示剩余可签约艺人数
	 *   @return array $array 返回站下房间信息
	 */
	public function getGroupRooms($groupId){
		if(!is_numeric($groupId)||$groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		$sql="SELECT * FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE `group`=$groupId ORDER BY id";
		$list=$this->groupMysql->get_results($sql,'ASSOC');
		
		return array('Flag'=>100,'FlagString'=>'站房间列表','roomList'=>$list);
	}
	
	/**
	 *   获取站下房间列表信息，带分页
	 *   @param	int $groupId 站groupid
	 *   @param	boole $surplusEntertainer 是否需要显示剩余可签约艺人数
	 *   @return array $array 返回站下房间信息
	 */
	public function getGroupRoomsList($info, $no_page = false){
		$groupId = $info['GroupId'];
		$channel_id = $info['ChannelId'];
		if(!is_numeric($groupId)||$groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		if($channel_id > 0){
			$where = ' AND id = '.$channel_id;
		}
		$sql="SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE `group`=$groupId".$where;
		$count=$this->groupMysql->get_var($sql);
		if($count<=0){
			return array('Flag'=>100,'FlagString'=>'没有数据','total'=>0,'freezeTotal'=>0);
		}
		
		if($no_page) $pageArr = $this->showPageCommon($count,$count);
		else $pageArr=$this->showPageCommon($count,20);
		$sql="SELECT * FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE `group`=$groupId".$where." ORDER BY id LIMIT ".$pageArr['limit'];
		
		$list=$this->groupMysql->get_results($sql,'ASSOC');
		$freezeTotal=0;
		foreach($list as $key=>$val){
			//冻结房间数
			if($val['status']==0){
				$freezeTotal++;
			}
			//艺人数
			$sql="SELECT COUNT(*) FROM ".DB_NAME_PARTNER.".channel_user WHERE type=15 AND room_id=".$val['id'];
			$total=$this->groupMysql->get_var($sql);
			//剩余艺人数
			$list[$key]['surplus_entertainer']=$val['entertainer_quota']-$total;
			$list[$key]['entertainer_total']=$total;
		}
		
		return array('Flag'=>100,'FlagString'=>'站房间列表','roomList'=>$list,'page'=>$pageArr['page'],'total'=>$count,'freezeTotal'=>$freezeTotal);
	}
	
	/**
	 *   获取站内代理列表
	 *   @param	int $groupId 站groupid
	 *   @return array $array 返回代理信息
	 */
	public function getProxyList($groupId){
		if(!is_numeric($groupId)||$groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT COUNT(*) FROM ".DB_NAME_GROUP.".groups_proxy WHERE group_id=$groupId";
		$count=$this->groupMysql->get_var($sql);
		$pageArr=$this->showPageCommon($count,20);
		if($count<=0){
			return array('Flag'=>100,'FlagString'=>'没有数据','page'=>$pageArr['page'],'total'=>$count);
		}
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".groups_proxy WHERE group_id=$groupId ORDER BY uptime DESC LIMIT ".$pageArr['limit'];
		$list=$this->groupMysql->get_results($sql,'ASSOC');
		
		return array('Flag'=>100,'FlagString'=>'站内代理列表','list'=>$list,'page'=>$pageArr['page'],'total'=>$count);
	}
	
	/**
	 *   获取站内代理信息
	 *   @param	int $groupId 站groupid
	 *   @return array $array 返回代理信息
	 */
	public function getProxyInfo($id,$groupId){
		if(!is_numeric($id)||$id<=0||!is_numeric($groupId)||$groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql="SELECT * FROM ".DB_NAME_GROUP.".groups_proxy WHERE id=$id AND group_id=$groupId";
		$info=$this->groupMysql->get_row($sql,'ASSOC');
		return array('Flag'=>100,'FlagString'=>'站内代理信息','proxyInfo'=>$info);
	}
	
	/**
	 *   添加、更新站内代理信息
	 *   @param	int $groupId 站groupid
	 *   @param	array $data 内容 
	 *   @return array $array 返回代理信息
	 */
	public function saveProxyInfo($data){
		$roleId=intval($data['RoleId']);
		$groupId=intval($data['GroupId']);
		$uin=intval($data['Uin']);
		$roomId=intval($data['RoomId']);
		$id=intval($data['Id']);
		if($roleId<=0){
			return array('Flag'=>101,'FlagString'=>'请选择签约类型');
		}
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		if($uin<=0){
			return array('Flag'=>101,'FlagString'=>'用户ID不能为空');
		}
		if($roomId<0){
			return array('Flag'=>101,'FlagString'=>'房间ID不能为空');
		}
		
		//$passManager=new PassManager();
		//$ssoInfo=$passManager->ssoInfo($uin);
		$ssoInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$uin)));
		if($ssoInfo['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'ID为：'.$uin.'的用户不存在');
		}
		
		if($id>0){
			$sql="SELECT uin FROM ".DB_NAME_GROUP.".groups_proxy WHERE id=$id";
			$oldUin=$this->groupMysql->get_var($sql);
			if(empty($oldUin)){
				return array('Flag'=>101,'FlagString'=>'参数错误');
			}
		}
		
		//验证角色
		$roleData=array(
			'extparam'=>array(
				'Tag'=>'UinRole',
				'GroupId'=>$groupId
			)
		);
		$roles=httpPOST(ROLE_API_PATH,$roleData);
		if($roles['Flag']!=100){
			return $roles;
		}
		if(!in_array($roleId,$roles['Roles'])){
			return array('Flag'=>102,'FlagString'=>'没有这个签约类型');
		}
		
		$sql="SELECT group_id FROM ".DB_NAME_GROUP.".groups_proxy WHERE uin=$uin";
		$gId=$this->groupMysql->get_var($sql);
		if(!empty($gId)&&$gId!=$groupId){
			return array('Flag'=>103,'FlagString'=>'该用户已是站 '.$gId.' 的代理');
		}
		
		$this->groupMysql->start_transaction();
		if($id>0&&$uin!=$oldUin){
			$sql="DELETE FROM ".DB_NAME_GROUP.".groups_proxy WHERE id=$id AND group_id=$groupId";
			if(!$this->groupMysql->query($sql)){
				$this->groupMysql->rollback();
				return array('Flag'=>110,'FlagString'=>'操作失败');
			}
			$roleData=array(
				'extparam'=>array(
					'Tag'=>'DeleteGroupRole',
					'GroupId'=>$groupId,
					'Uin'=>$oldUin,
					'RoleId'=>$roleId,
					'RoomId'=>$roomId
				)
			);
			$res=httpPOST(ROLE_API_PATH,$roleData);
			if($res['Flag']!=100){
				return array('Flag'=>111,'FlagString'=>'操作失败');
			}
		}
		
		//添加角色
		$roleData=array(
			'extparam'=>array(
				'Tag'=>'AddGroupRole',
				'GroupId'=>$groupId,
				'Uin'=>$uin,
				'RoleId'=>$roleId,
				'RoomId'=>$roomId
			)
		);
		$res=httpPOST(ROLE_API_PATH,$roleData);
		if($res['Flag']!=100){
			$this->groupMysql->rollback();
			return array('Flag'=>111,'FlagString'=>'操作失败');
		}
		
		$sql="REPLACE INTO ".DB_NAME_GROUP.".groups_proxy (id,group_id,uin,room_id,uptime) VALUES ('".$data['id']."','$groupId','$uin','$roomId','".time()."')";
		if(!$this->groupMysql->query($sql)){
			$this->groupMysql->rollback();
			return array('Flag'=>110,'FlagString'=>'操作失败');
		}
		else{
			$this->groupMysql->commit();
			return array('Flag'=>100,'FlagString'=>'操作成功');
		}
	}
	
	/**
	 *   删除站内代理信息
	 *   @param	int $groupId 站groupid
	 *   @return array $array 返回代理信息
	 */
	public function removeProxyInfo($id,$groupId){
		if(!is_numeric($id)||$id<=0||!is_numeric($groupId)||$groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$proxyInfo=$this->getProxyInfo($id,$groupId);
		$proxyInfo=$proxyInfo['proxyInfo'];
		if(empty($proxyInfo)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		//删除站代理角色
		$groupInfo=$this->getGroupInfo(0,$groupId);
		$packageId=$groupInfo['Result']['package_id'];
		if(empty($packageId)){
			return array('Flag'=>101,'FlagString'=>'这个站没有配置代理签约角色');
		}
		$roleList=$this->getGroupRole($packageId,array(1),array(2));
		$roleList=$roleList['list'];
		if(empty($roleList)){
			return array('Flag'=>101,'FlagString'=>'这个站没有配置代理签约角色');
		}
		$roleIds=array();
		foreach($roleList as $val){
			$roleIds[]=$val['id'];
		}
		$roleData=array(
			'extparam'=>array(
				'Tag'=>'DeleteGroupRole',
				'GroupId'=>$groupId,
				'Uin'=>$proxyInfo['uin'],
				'RoleId'=>$roleIds,
				'RoomId'=>$proxyInfo['room_id']
			)
		);
		$res=httpPOST(ROLE_API_PATH,$roleData);
		if($res['Flag']!=100){
			return array('Flag'=>111,'FlagString'=>'操作失败');
		}
		//删除代理数据
		$sql="DELETE FROM ".DB_NAME_GROUP.".groups_proxy WHERE id=$id AND group_id=$groupId";
		if(!$this->groupMysql->query($sql)){
			return array('Flag'=>110,'FlagString'=>'操作失败');
		}
		else{
			return array('Flag'=>100,'FlagString'=>'操作成功');
		}
	}
	
	/**
	 *   站长开设房间
	 *   @param	array $data 开房需要的数据
	 *   @return array $array 返回结果
	 */
	public function openRoom($data, $group_id){
		if(!is_numeric($data['GroupId'])||$data['GroupId']<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误,所属站不能为空');
		}
		if(!is_numeric($data['GroupUin'])||$data['GroupUin']<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误,站长ID不能为空');
		}

		$groupInfo = $this->getGroupInfo(0, $group_id);
		if($groupInfo['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'站不存在');
		}
		$rooms_ui = json_decode($groupInfo['Result']['room_ui'], true);
		$ktv_template = json_decode($groupInfo['Result']['ktv_template'], true);
		$data['RoomUi'] = intval($data['RoomUi']);
		if(! in_array($data['RoomUi'], $rooms_ui) && $data['TemplateUi'] ==-1 && $data['RoomUi']>0){
			return array('Flag'=>103,'FlagString'=>'不存在该房间界面');
		}else if(! in_array($data['TemplateUi'], $ktv_template) && $data['TemplateUi'] !=-1 && !empty($data['TemplateUi'])){
			return array('Flag'=>103,'FlagString'=>'不存在该模板界面');
		}
		if($data['TemplateUi'] ==-1 && $data['RoomUi']>0){
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_SYSTEM_CONFIG.".`tbl_rooms_ui` WHERE id = ".$data['RoomUi'];
			$count = $this->db->get_var($sql);
			if(!$count){
				return array('Flag'=>102,'FlagString'=>'设置错误');
			}
		}
		$this->groupMysql->start_transaction();
		$sql="SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE `group`=".$data['GroupId'];
		$total=$this->groupMysql->get_var($sql);
		if($total>=$data['OpenNum']){
			$$this->groupMysql->rollback();
			return array('Flag'=>104,'FlagString'=>'对不起,您站的免费房间额度已用完,无法开设新房间');
		}
		
		$sql="SELECT id FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id BETWEEN 5880001 AND 9999999 ORDER BY id DESC LIMIT 1";
		$row=$this->groupMysql->get_var($sql,"ASSOC");
		$roomId=empty($row)?5880001:$row+1;
		
		$sql="SELECT host,port FROM ".DB_NAME_NEW_ROOMS.".tbl_roomhost";
		$alias=$this->groupMysql->get_row($sql);
		$sql="INSERT INTO ".DB_NAME_NEW_ROOMS.".rooms (id,name,host,port,maxuser,`date`,`group`,entertainer_quota,ui_version,template_ui) VALUES ('".$roomId."','".$roomId."','".$alias['host']."','".$alias['port']."','100','".date('Y-m-d H:i:s',time())."','".$data['GroupId']."','100','".$data['RoomUi']."','".$data['TemplateUi']."')";
		if(!$this->groupMysql->query($sql)){
			$this->groupMysql->rollback();
			return array('Flag'=>111,'FlagString'=>'操作失败');
		}
		$this->groupMysql->commit();
		return array('Flag'=>100,'FlagString'=>'操作成功','roomId'=>$roomId);
	}
	
	/**
	 *   站内角色
	 *   @param	int $packageId 站package_id
	 *   @param	array $roleShowOne 规则1的ID数组
	 *   @param	array $roleShowTwo 规则2的ID数组
	 *   @return array $array 返回结果
	 */
	public function getGroupRole($group_id,$roleShowOne,$roleShowTwo){
		if(!is_array($roleShowOne)||empty($roleShowOne)||!is_array($roleShowTwo)||empty($roleShowTwo)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
        $module_id = domain::main()->GroupKeyVal($group_id,'module_id');
		$sql = "SELECT cate_id FROM ".DB_NAME_TPL.".`role_cate` WHERE tpl_id = ".$module_id;
        $res = $this->db->get_results($sql,"ASSOC");
		$cate_id_arr = array();
		foreach($res as $one){
			$cate_id_arr[] = $one['cate_id'];
		}
        
		$sql="SELECT * FROM ".DB_NAME_TPL.".role WHERE cate_id IN (".join(",", $cate_id_arr).") AND role_show_1 IN (".implode(',',$roleShowOne).") AND role_show_2 IN (".implode(',',$roleShowTwo).") AND status=1";
		$list=$this->db->get_results($sql,'ASSOC');
		return array('Flag'=>100,'FlagString'=>'角色列表','list'=>$list);
	}
	
	/**
	 *   站内业务规则
	 *   @param	int $packageId 站package_id
	 *   @param	array $roleShowOne 规则1的ID数组
	 *   @param	array $roleShowTwo 规则2的ID数组
	 *   @return array $array 返回结果
	 */
	public function getBusinessRule($group_id){
	    $module_id = domain::main()->GroupKeyVal($group_id,'module_id');
		$sql = "SELECT * FROM ".DB_NAME_TPL.".business_rule where status='1' AND tpl_id = ".$module_id;
		$list=$this->db->get_results($sql,'ASSOC');
		return array('Flag'=>100,'FlagString'=>'积分规则列表','list'=>$list);
	}
	
	function groupFlow($data){
		//接受参数
		$res = array();
		$roomid = array();
		$time = strtotime($data['Time']?$data['Time']:date("Y-m-d"));
		$GroupId = (int)$data['GroupId'];
		// foreach($data['RoomId'] as $key=>$value){
			// $roomid[$key] = intval($value);
		// }
		// if(!$roomid)
			// return array('Flag'=>102,'FlagString'=>'参数错误');
		
		//查询
		$db = DB_NAME_NEW_ROOMS;
		$table = "tbl_rooms_usertotal";
		$query = array("region_id"=>$GroupId, "createtime"=>$time);
		$result = $this->mongodb->get_results(
				$db.".".$table,
				$query,
				array()
		);
		// if($data['Format']){
			// foreach($result as $key => $value){
				// $res[$value['roomid']] = $result[$key];
			// }
			// return array('Flag'=>100,'FlagString'=>'查询成功','Result'=>(array)$res);
		// }
		return array('Flag'=>100,'FlagString'=>'查询成功','Result'=>(array)$result);
		
	}
	
	function groupImcome($data){
		$db = "kkyoo_integral";
		$type = $data['Type'];
		$groupid = $data['GroupId'];
		$time = strtotime($data['Time']);
		// $roomid = array();
		// foreach($data['RoomId'] as $key=>$value){
			// $roomid[$key] = intval($value);
		// }
		// if(!$roomid){
			// return array('Flag'=>102,'FlagString'=>'参数错误');
		// }
		switch($type){
			case 1:
				$table = "day_weight";
				$query['Uptime'] = intval(date("Ymd", $time));
				break;
			case 2:
				$table = "week_weight";
				$query['Uptime'] = intval(date("oW", $time));
				break;
			case 3:
				$table = "month_weight";
				$query['Uptime'] = intval(date("Ym", $time));
				break;
			default:
				return array('Flag'=>102,'FlagString'=>'参数错误');
				break;
		}
		
		$page_arr = $this->showPage($total,20);
		list($offset,$rows) = explode(',',$page_arr['limit']);
		$limit = array('offset'=>$offset,'rows'=>$rows);
		
		$query['Ruleid'] = 38;
		$query['ExtendUin'] = intval($groupid);
		$result = $this->mongodb->get_results(
				$db.".".$table,
				$query,
				array(
						'sort'=>array('Uptime'=>-1),
						'limit'=>$limit
				)
		);
		$page_arr = $this->showPage(count($result),20);
		
		return array('Flag'=>100,'FlagString'=>'查询成功','Result'=>(array)$result, 'Page'=>$page_arr['page']);
	}
	
	function taxDetails($info){
		if($info['GroupId'] <1){
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
		$where = " group_id = {$info['GroupId']}";
		
		$startdate = $info['StartDate']? strtotime($info['StartDate']) :strtotime(date('Ymd'));
		$enddate = $info['EndDate'] ? strtotime($info['EndDate'])+86400 : time();
		if(!empty($startdate) && !empty($enddate)){
			$where .= ' AND `uptime` BETWEEN '.$startdate.' AND '.$enddate;
		}
		$table = DB_NAME_TAX.'.tax_running';
		
		$sql="SELECT COUNT(*) FROM ".$table." WHERE ".$where;
		$count=$this->groupMysql->get_var($sql);
		if($count<=0){
			return array('Flag'=>100,'FlagString'=>'没有数据');
		}

		$pageArr=$this->showPage($count);
		
		$sql="SELECT * FROM ".$table." WHERE ".$where." ORDER BY id DESC LIMIT ".$pageArr['limit'];
		$list=$this->groupMysql->get_results($sql,'ASSOC');
		/*
		foreach($list as $key=>$value){
			$param = array(
				'param'=>array(
					"BigCaseId"   => $value['bigcase_id'],
					"CaseId"   => $value['case_id'],
					"ParentId"   => $value['parent_id'],
					"ChildId"   => $value['child_id'],
				),
				'extparam'=>array(
					"Tag" 		 => "GetBusinessConfig",
				)
			);
			$result = httpPOST(CCS_API_PATH,$param);
			$list[$key]['bigcase_name'] = $result['Result']['bigcase_name'];
			$list[$key]['case_name'] = $result['Result']['case_name'];
			$list[$key]['parent_name'] = $result['Result']['parent_name'];
			$list[$key]['child_name'] = $result['Result']['child_name'];
		}
		*/
		$total_array = $this->cache->get("{$table}_{$where}");
		if(empty($total_array)){
			$long_info = $this->cache->long_get("{$table}_{$where}");
			$this->cache->set("{$table}_{$where}",$long_info,$this->expire);
			$total_array['pay_total'] = $this->groupMysql->get_var('SELECT SUM(trade_money) FROM '.$table .' WHERE  '.$where .' AND trade_type = 1');
			$total_array['deposit_total'] = $this->groupMysql->get_var('SELECT SUM(trade_money) FROM '.$table .' WHERE  '.$where .' AND trade_type = 2');
			$this->cache->set("{$table}_{$where}",$total_array,$this->expire);
		}
		$list['pay_total'] = $total_array['pay_total'];
		$list['deposit_total'] = $total_array['deposit_total'];
		return array('Flag'=>100,'FlagString'=>'V点流水列表','List'=>$list,'Page'=>$pageArr['page'],'total'=>$count);
	}
	
	function getBalance($uin, $channeltype=8, $result=array('sort'=>array('_id'=>-1)), $field=array()){
		$db = "parter_income";
		$table = "new_parter_balance";
		
		$query = array("Uin"=>intval($uin), "ChannelType"=>$channeltype);
		
		$s_result = $this->mongodb->get_row(
				$db.".".$table,
				$query,
				(array)$result,
				(array)$field
		);
		return array('Flag'=>100,'FlagString'=>'查询成功','Result'=>(array)$s_result);
	}
	
	function exchangeDetails($data){
		$table = "parter_income.new_exchange_details";
		$page_arr = $this->showPage($total,10);
		list($offset,$rows) = explode(',',$page_arr['limit']);
		$limit = array('offset'=>$offset,'rows'=>$rows);
		
		$uin = intval($data['Uin']);
		$type = intval($data['Type']);
		$start_time = intval(strtotime($data['StartDate']));
		$end_time = intval(strtotime($data['EndDate']));
		
		if($uin)
			$query['Uin'] = $uin;
		if($type)
			$query['Type'] = $type;
		if($start_time)
			$query['Uptime']['$gte'] = $start_time;
		if($end_time)
			$query['Uptime']['$lte'] = $end_time + 24*60*60 - 1;
		
		$result = $this->mongodb->get_results(
				$table,
				$query,
				array(
						'sort'=>array('Uptime'=>-1),
						'limit'=>$limit
				)
		);
		return array('Flag'=>100,'FlagString'=>'查询成功','Result'=>(array)$result, 'Page'=>$page_arr['page']);
	}
	
	function RMBExchange($data){
		$weight = intval($data['Weight'])*9/10;
		$tax_weight = $weight*100000/9;
		$uin = intval($data['Uin']);
		$channelId = intval($data['ChannelId']);
		$room_id = $data['RoomId'];
		
//		$sql = "SELECT uid FROM ".DB_NAME_PARTNER.".channel_user WHERE room_id=".$room_id." AND `type`=7 AND `flag`=1 ORDER BY `type` ASC";
//		$upchannel = intval($this->groupMysql->get_var($sql,'ASSOC'));
		
		//查询税收余额
		$last_tax_balance = array_pop($this->getBalance($uin, 8));
		$tax_balance = $last_tax_balance['Weight'] - $tax_weight;
		if($tax_balance < 0){
			return array('Flag'=>104,'FlagString'=>'税收余额不足');
		}
		//插入记录
		$done = $this->setBalance($uin, $tax_balance, $channelId,$room_id, $tax_weight, "兑换".$weight."人民币,税收扣除".$tax_weight, 2, $weight);
		if(!$done){
			return array('Flag'=>103,'FlagString'=>'数据库错误');
		}
		$table = "parter_income.parter_rmb_balance";
		$query = array();
		$fields = array();
		$result_condition = array();
		$query['Uin'] = $uin;
		//获取余额
		$fields = array("Balance"=>1);
		$lastbalance = $this->mongodb->get_row(
				$table,
				$query,
				$result_condition,
				$fields
		);
		//插入记录
		$balance = $lastbalance['Balance'] + $weight;
		$query = array('$set'=>array('Balance'=>$balance, 'Uptime'=>time()));
		$where = array("Uin"=>$uin);
		$done = $this->mongodb->query(
				$table,
				$query,
				$where
		);
		if(!$done){
			return array('Flag'=>103,'FlagString'=>'数据库错误');
		}
		//插入流水
		$table = "parter_income.parter_rmb_details";
		$this->mongodb->query(
				$table,
				array("Uin"=>$uin,"TargetUin"=>$uin,"Weight"=>$weight,
					  "Uptime"=>time(),"Balance"=>$balance,"Type"=>1,
					  "ChannelType"=>(int)$data['ChannelType'],"ChildType"=>10)
		);
		
		/*
		$table = "parter_income.new_parter_details";
		$record = array("Uin"=>$uin, "ChannelType"=>8, "Uptime"=>time(), "ChannelId"=>$channelId, "UpChannel"=>$upchannel,
				"Desc"=>"兑换".$weight."人民币,税收扣除".$tax_weight, 
				"Balance"=>$tax_balance, "Weight"=>$tax_weight);
		$this->mongodb->query(
				$table,
				$record
		);
		*/
		//人民币账户汇总查询
		$thismonth = intval(date("Ym"));
		$table = "parter_income.parter_cash_total";
		
		//检测记录是否存在
		$query = array('Uptime'=>$thismonth);
		$fields = array('Balance'=>1);
		$result_condition = array();
		$fields = array("Balance"=>1);
		$lastbalance = $this->mongodb->get_row(
				$table,
				$query,
				$result_condition,
				$fields
		);
		if(!$lastbalance){
			//需加上上个月金额
			$lastmonth = intval(date("Ym", mktime(0, 0, 0, date("m")-1, 1, date("Y"))));
			$query = array('Uptime'=>$lastmonth);
			$fields = array('Balance'=>1);
			$result_condition = array();
			$lastbalance = $this->mongodb->get_row(
					$table,
					$query,
					$result_condition,
					$fields
			);
			$balance = $weight+$lastbalance['Balance'];
		}else{
			$balance = $weight;
		}
		$record = array('$inc'=>array("Balance"=>$balance, "Deposit"=>$weight));
		$where = array("Uptime"=>$thismonth);
		$this->mongodb->query(
				$table,
				$record,
				$where
		);
		
		return array('Flag'=>100,'FlagString'=>'操作成功');
	}
	
	function signatoryDetails($data){
		//获得条件
		$or_condition = array();
		$uin = $data['Uin'];
		$type = $data['Type'];
		$role = $data['Role'];
		if(!$uin)
			return array('Flag'=>102,'FlagString'=>'参数错误');
		switch($type){
			case 1:
				$table = "parter_income.new_parter_user_day";
				break;
			case 2:
				$table = "parter_income.new_parter_user_week";
				break;
			case 3:
				$table = "parter_income.new_parter_user_month";
				break;
			case 4:
				$table = "parter_income.new_parter_user_total";
				break;
			default:
				return array('Flag'=>102,'FlagString'=>'参数错误');
		}
		if($role&1){
			//群主
			$or_condition[] = array("UpChannel"=>intval($uin), "ChannelType"=>9);
		}
		if($role&2){
			//艺人
			$sql = "SELECT room_id FROM ".DB_NAME_PARTNER.".`channel_user` WHERE uid = ".$uin." AND `type` = 8";
			$channel_id = $this->groupMysql->get_var($sql);
			$sql = "SELECT uid FROM ".DB_NAME_PARTNER.".`channel_user` WHERE up_uid = ".$channel_id." AND `type` = 9";
			$result = $this->groupMysql->get_results($sql, "ASSOC");
			
			foreach($result as $one){
				$or_condition[] = array("UpChannel"=>intval($one['uid']), "ChannelType"=>15);
			}
		}
		
		//查询
		$page_arr = $this->showPage($total,10);
		list($offset,$rows) = explode(',',$page_arr['limit']);
		$limit = array('offset'=>$offset,'rows'=>$rows);
		$query = array('$or'=>$or_condition);
		$result = $this->mongodb->get_results(
				$table,
				$query,
				array(
						'sort'=>array('Uptime'=>-1,'Balance'=>-1),
						'limit'=>$limit
				)
		);
		
		return array('Flag'=>100,'FlagString'=>'查询成功','Result'=>(array)$result, 'Page'=>$page_arr['page']);
	}
	
	function exchange($param, $exparam){
		$uin = intval($exparam['Uin']);
		$ChannelType = intval($exparam['ChannelType']);
		$channelId = intval($exparam['ChannelId']);
		$kmoney = $param['MoneyWeight'];
		$room_id = $exparam['RoomId'];
		
//		$sql = "SELECT uid FROM ".DB_NAME_PARTNER.".channel_user WHERE room_id=".$room_id." AND `type`=7 AND `flag`=1 ORDER BY `type` ASC";
//		$upchannel = intval($this->groupMysql->get_var($sql,'ASSOC'));
		
		if($kmoney <= 0){
			return array('Flag'=>101,'FlagString'=>"兑换失败:兑换金额必须大于0");
		}
		$log = array();
	
		//mongo余额库减少
		$fields = array("Weight"=>1);
		//余额查询
		$list = array_pop($this->getBalance($uin, $ChannelType, array(), $fields));
		if($list['Weight'] < $kmoney*10000){
			return array('Flag'=>101,'FlagString'=>"兑换失败：兑换金额大于账户金额！");
		}
		//余额更新
		$balance = intval($list['Weight']-$kmoney*10000);
		$done = $this->setBalance($uin, $balance, $channelId,$room_id, $kmoney*10000, "兑换".$kmoney."V宝,税收扣除".($kmoney*10000), 1, $kmoney, $ChannelType);
		if(!$done){
			return array('Flag'=>102,'FlagString'=>"数据库存储错误");
		}
		
		//调用109
		$info = array(
				'param'=>array("Uin"=>$uin, "TargetUin"=>$uin, "ChannelId"=>$channelId, "MoneyWeight"=>$kmoney,
						"BigCaseId"=>$param['BigCaseId'], "CaseId"=>$param['CaseId'], "ParentId"=>$param['ParentId'],
						"ChildId"=>101, "Client"=>"WEB ADMIN", "Desc"=>"税收兑换V宝-V宝净存入"),
				'extparam'=>array("Tag"=>"Kwealth","Operator"=>'CF9BEF26F303FF9DDA3F5DED2AA7C3C5')
		);
		$log[] = $info;
		$rst = httpPOST(KWEALTH_API_PATH,$info);
		if($rst['Flag'] != 100){
			return array('Flag'=>102, 'FlagString'=>$rst['FlagString']);
		}
		
		//调用110
		$info = array(
				'param'=>array("Uin"=>$uin, "TargetUin"=>$uin, "ChannelId"=>$channelId, "MoneyWeight"=>$kmoney,
						"BigCaseId"=>$param['BigCaseId'], "CaseId"=>$param['CaseId'], "ParentId"=>$param['ParentId'],
						"ChildId"=>102, "Client"=>"WEB ADMIN", "Desc"=>"税收兑换V宝-存入用户账户"),
				'extparam'=>array("Tag"=>"Kwealth","Operator"=>'CF9BEF26F303FF9DDA3F5DED2AA7C3C5')
		);
		$rst = httpPOST(KWEALTH_API_PATH,$info);
		if($rst['Flag'] != 100){
			return array('Flag'=>102, 'FlagString'=>$rst['FlagString']);
		}
		$log[] = $info;
		/*
		//税收记录
		$table = "parter_income.new_parter_details";
		$record = array("Uin"=>$uin, "ChannelType"=>$ChannelType, "ChannelId"=>$channelId, "UpChannel"=>$upchannel, 
				"Uptime"=>time(), "Desc"=>"税收".($kmoney*10000)."兑换".$kmoney."V宝", "Weight"=>$kmoney*10000,
				"Balance"=>$balance, "TaxType"=>1);
		$this->mongodb->query(
				$table,
				$record
		);
		*/
		return array('Flag'=>100, 'FlagString'=>"兑换成功", 'LogData'=>$log);
	}
	
	function setBalance($uin, $tax_balance, $channelId,$room_id, $tax_weight, $desc, $type, $to_weight, $channelType=8, $uptime=""){
		$sql = "SELECT uid FROM ".DB_NAME_PARTNER.".channel_user WHERE room_id=".$room_id." AND `type`=7 AND `flag`=1 ORDER BY `type` ASC";
		$upchannel = intval($this->groupMysql->get_var($sql,'ASSOC'));
		
		if(!$uptime){
			$uptime = time();
		}
		$table = "parter_income.new_parter_balance";
		$query = array('$set'=>array('Weight'=>(int)$tax_balance));
		$where = array('Uin'=>(int)$uin, 'ChannelType'=>8, 'UpChannel'=>$upchannel);
		$done = $this->mongodb->query(
				$table,
				$query,
				$where
		);
		
		$table = "parter_income.new_parter_details";
		$record = array("Uin"=>(int)$uin, "ChannelType"=>(int)$channelType, "Uptime"=>(int)$uptime, "ChannelId"=>(int)$channelId, "UpChannel"=>$upchannel,
				"Desc"=>$desc, "Balance"=>(int)$tax_balance, "Weight"=>(int)$tax_weight);
		$done2 = $this->mongodb->query(
				$table,
				$record
		);
		
		$table = "parter_income.new_exchange_details";
		$record = array("Uin"=>(int)$uin, "ChannelType"=>(int)$channelType, "Uptime"=>(int)$uptime,
				"Desc"=>$desc, "Weight"=>(int)$tax_weight, "ToWeight"=>(int)$to_weight,
				"Type"=>(int)$type);
		$this->mongodb->query(
				$table,
				$record
		);

		return $done&&$done2;
	}
	
	/**
	 *   添加、更新赋予角色
	 *   @param	array $data 内容 
	 *   @return array $array 返回代理信息
	 */
	public function saveRoleInfo($data){
		$roleId = intval($data['RoleId']);
		$groupId = intval($data['GroupId']);
		$uin = intval($data['Uin']);
		$roomId = intval($data['RoomId']);
		$id = $data['Id'];//更新时
		
		if($roleId <= 0){
			return array('Flag'=>101,'FlagString'=>'请选择签约类型');
		}
		if($groupId <= 0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		if($uin <= 0){
			return array('Flag'=>101,'FlagString'=>'用户ID不能为空');
		}
		if($roomId < 0){
			return array('Flag'=>101,'FlagString'=>'房间ID不能为空');
		}
		
		//$passManager = new PassManager();
		//$ssoInfo = $passManager->ssoInfo($uin);
		$ssoInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$uin)));
		if($ssoInfo['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'ID为：'.$uin.'的用户不存在');
		}
		
		$db = "kkyoo_role";
		$table = "uin_role";
		
		if($id>0){//如果是修改
			$query = array("_id"=>new MongoId($id));
			$result = $this->mongodb->get_row(
				$db.".".$table,
				$query
			);
			$oldUin = $result['Uin'];
			if(empty($oldUin)){
				return array('Flag'=>101, 'FlagString'=>'参数错误');
			}
		}
		
		//验证角色
        $module_id = domain::main()->GroupKeyVal($groupId, "module_id");
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
        if(!in_array($roleId,$roles_id)){
			return array('Flag'=>102,'FlagString'=>'该站没有这个角色');
		}
		
		//查看该uin是否已存在其他站的该角色
		/*$sql="SELECT group_id FROM ".DB_NAME_GROUP.".groups_proxy WHERE uin=$uin";
		$gId=$this->groupMysql->get_var($sql);
		if(!empty($gId)&&$gId!=$groupId){
			return array('Flag'=>103,'FlagString'=>'该用户已是站 '.$gId.' 的代理');
		}*/
		
		if($id > 0&& $uin != $oldUin){
			//删掉该id在该站该房间的所有该角色
			$roleData=array(
				'extparam'=>array(
					'Tag'=>'DeleteGroupRole',
					'GroupId'=>$groupId,
					'Uin'=>$uin,
					'RoleId'=>array($roleId),
					'RoomId'=>$roomId
				)
			);
			$res=httpPOST(ROLE_API_PATH,$roleData);
			if($res['Flag']!=100){
				return array('Flag'=>111,'FlagString'=>'操作失败');
			}
		}
		
		//添加角色
		$roleData=array(
			'extparam'=>array(
				'Tag'=>'AddGroupRole',
				'GroupId'=>$groupId,
				'Uin'=>$uin,
				'RoleId'=>$roleId,
				'RoomId'=>$roomId,
				'UpTime'=>time()
			)
		);
		$res=httpPOST(ROLE_API_PATH,$roleData);
		if($res['Flag'] == 100){
			return array('Flag' => 100, 'FlagString' => '操作成功');	
		}else{
			return array('Flag'=>111,'FlagString'=>'操作失败');
		}
	}
	
	/**
	 *   @param	int $groupId 站groupid
	 *   @param array $roleIds 
	 *   @return array $array 返回
	 */
	public function getRoleList($groupId, $roleIds, $data){
		if(!is_numeric($groupId)||$groupId<=0||!is_array($roleIds)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$page_arr = $this->showPage($total, 20);
		list($offset, $rows) = explode(',', $page_arr['limit']);
		$limit = array('offset' => $offset, 'rows' => $rows);
		$db = "kkyoo_role";
		$table = "uin_role";
		
		$query = array("GroupId"=>intval($groupId), "RoleId"=>array('$in'=>$roleIds));
		if($data['Uin'] > 0) $query['Uin'] = intval($data['Uin']);
		if($data['RoomId'] > 0) $query['ChannelId'] = intval($data['RoomId']);
		$result0 = $this->mongodb->get_results(
				$db.".".$table,
				$query
		);
		
		$result = $this->mongodb->get_results(
				$db.".".$table,
				$query,
				array(
					'limit' => $limit
				)
		);
		$page_arr = $this->showPageCommon(count($result0), 20);
		return array('Flag'=>100,'FlagString'=>'查询成功','Result'=>(array)$result, 'Page'=>$page_arr['page']);
	}
	
	/**
	 *   获取站内代理信息
	 *   @param	int $groupId 站groupid
	 *   @return array $array 返回代理信息
	 */
	public function getRoleInfo($id,$groupId){
		if(!$id || !is_numeric($groupId)||$groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$db = "kkyoo_role";
		$table = "uin_role";
		
		$query = array("_id"=>new MongoId($id));
		$result = $this->mongodb->get_row(
				$db.".".$table,
				$query
		);
		return array('Flag'=>100,'FlagString'=>'站内代理信息','proxyInfo'=>$result);
	}
	
	/**
	 *   删除站内代理信息
	 *   @param	int $groupId 站groupid
	 *   @return array $array 返回代理信息
	 */
	public function removeRoleInfo($id,$groupId,$packageId,$roleId){
		if(!$id || !is_numeric($groupId)||$groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$proxyInfo=$this->getRoleInfo($id,$groupId);
		$proxyInfo=$proxyInfo['proxyInfo'];
		if(empty($proxyInfo)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$roleList=$this->getGroupRole($groupId,array(1),array(2));
		$roleList=$roleList['list'];
		if(empty($roleList)){
			return array('Flag'=>101,'FlagString'=>'这个站没有配置代理签约角色');
		}
		/*查出该id对应的信息*/
		$db = "kkyoo_role";
		$table = "uin_role";
		$query = array("_id"=>new MongoId($id));
		$result = $this->mongodb->get_row(
				$db.".".$table,
				$query
		);
		
		$roleData=array(
			'extparam'=>array(
				'Tag'=>'DeleteGroupRole',
				'GroupId'=>$result['GroupId'],
				'Uin'=>$result['Uin'],
				'RoleId'=>array($result['RoleId']),
				'RoomId'=>intval($result['ChannelId'])
			)
		);
		$res=httpPOST(ROLE_API_PATH,$roleData);
		if($res['Flag'] == 100){
			return array('Flag'=>100,'FlagString'=>'操作成功');
		}else{
			return array('Flag'=>111,'FlagString'=>'操作失败');
		}
	}
	
	function integralSearch($type, $rule_id, $search, $group_id){
		$module_id = domain::main()->GroupKeyVal($group_id,'module_id');
        $sql = 'SELECT `id`,`name`,`user_id_type`,`business_id_type`,`extended_parameters` FROM '.DB_NAME_TPL.'.business_rule WHERE `status`="1" AND tpl_id = '.$module_id;
		$rule = $this->db->get_results($sql,'ASSOC');
		if(!$rule){
			return array("Flag"=>102, "FlagString"=>"未选择角色包");
		}
		if(!$type){
			$type = 1;
		}
		if(!$rule_id){
			$rule_id = $rule[0]['id'];
		}
		if($search){
			foreach($search as $k=>$v){
				if($v){
					$query_condition[$k] = intval($v);
				}
			}
		}
		
		$rule_arr = array();
		$label_name = $this->get_label_name($rule);
		foreach($rule as $key => $val){
			$rule_arr[$val['id']] = $val['name'];
		}
		$site = false;
		foreach($label_name[$rule_id] as $k=>$v){
			if($v == "站id"){
				$query_condition[$k] = intval($group_id);
				$site = true;
				break;
			}
		}
		if(!$site){
			return array("Flag"=>102, "FlagString"=>"系统错误");
		}
		switch($type){
			case 1:
				$table = 'day_weight';
				break;
			case 2:
				$table = 'week_weight';
				break;
			case 3:
				$table = 'month_weight';
				break;
			case 4:
				$table = 'total_weight';
				break;
		}
		$query_condition['Ruleid'] = intval($rule_id);
		$table_name = DB_NAME_INTEGRAL.'.'.$table;
		$page_arr = $this->showPage($total,20);
		list($offset,$rows) = explode(',',$page_arr['limit']);
		$result = $this->mongodb->get_results(
				$table_name,
				$query_condition,
				array(
						'sort'=>array('Uptime'=>-1),
						'limit'=>array('offset'=>$offset,'rows'=>$rows)
				)
		);
		
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$result, "Rule"=>$rule_arr, "Page"=>$page_arr['page'],'LabelName'=>$label_name,'RuleId'=>$rule_id);
	}
	
	function sendMsg($title, $content, $group_id){
		$sql = "INSERT INTO ".DB_NAME_GROUP.".`send_message` (`group_id`, `title`, `content`) VALUES ('".$group_id."', '".$title."', '".$content."'); ";
		$done = $this->groupMysql->query($sql);
		if($done){
			return array("Flag"=>100, "FlagString"=>"操作成功");
		}else{
			return array("Flag"=>102, "FlagString"=>"操作失败");
		}
	}
	
	function getMsg($last_id, $group_id){
		$sql = "SELECT `id`,`title`,`content` FROM ".DB_NAME_GROUP.".`send_message` WHERE group_id = ".$group_id." AND id > ".$last_id." ORDER BY id DESC";
		$res = $this->groupMysql->get_results($sql, "ASSOC");
		$data = array("readid"=>$res[0]['id'], "msg"=>$res);
		return array("Flag"=>100, "FlagString"=>"操作成功", "Data"=>$data);
	}
	
	private function get_label_name($rule){
		$sql = "SELECT `id`,`name` FROM ".DB_NAME_BEHAVIOR.". business_key";
		$res = $this->db->get_results($sql, "ASSOC");
		$arr = array();
		foreach($res as $one){
			$arr[$one['id']] = $one['name'];
		}
		foreach($rule as $key => $val){
			$rule_arr[$val['id']] = $val['name'];
			$arr2 = json_decode($val['business_id_type'], true);
			if(is_array($arr2)){
				foreach($arr2 as $key_name=>$v){
					if($v){
						$label_name[$val['id']][$key_name] = $arr[$v];
					}
				}
			}
		}
		return $label_name;
	}
	
	//分页
	private function showPage($total, $perpage = 20) {
		require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/library/page.class.php');
		$page = new extpage(array (
				'total' => $total,
				'perpage' => $perpage
		));
		$pageArr['page'] = $page->simple_page($total);
		$pageArr['limit'] = $page->simple_limit();
		unset ($page);
		return $pageArr;
	}
	
	//分页2
	private function showPageCommon($total,$perpage=20){
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