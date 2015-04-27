<?php

/**
 *   群组操作接口
 *   文件: Role.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class Role
{
	//数据库指针
	protected $db = null;

	//构造函数
	public function __construct() {
		$this->db = db::connect(config('database','default'));
		$this->mongodb = domain::main()->GroupDBConn('mongo');
	}
	
// 	//角色绑定
// 	public function setRole2User($uin,$roles){
// 		if($uin<=0 || empty($roles)) return array('Flag'=>101,'FlagString'=>'参数错误');
// 		//检查用户是否存在
// 		$sql = 'SELECT COUNT(*) FROM '.DB_NAME_IM.'.basic_tbl WHERE uin="'.$uin.'"';
// 		$total = $this->db->get_var($sql);
// 		if($total <= 0)	return array('Flag'=>103,'FlagString'=>'不存在的账号');
// 		$sql = 'SELECT roles FROM '.DB_NAME_IM.'.basic_tbl WHERE uin="'.$uin.'"';
// 		$role = $this->db->get_var($sql);
// 		if($role)
// 			$roleList = implode(',',array_unique(array_merge(explode(',',$roles),explode(',',$role))));
// 		else
// 			$roleList = implode(',',array_unique(explode(',',$roles)));
// 		$sql = 'UPDATE '.DB_NAME_IM.'.basic_tbl SET roles="'.$roleList.'" WHERE uin="'.$uin.'"';
// 		if($this->db->query($sql))
// 			return array('Flag'=>100,'FlagString'=>'角色赋予成功');
// 		return array('Flag'=>102,'FlagString'=>'角色赋予失败');
// 	}
	
// 	//角色解绑
// 	public function unbindRole($uin,$roleId){
// 		if($uin<=0 || $roleId<=0) return array('Flag'=>101,'FlagString'=>'参数错误');
// 		//检查用户是否存在
// 		$sql = 'SELECT roles FROM '.DB_NAME_IM.'.basic_tbl WHERE uin="'.$uin.'"';
// 		$role = $this->db->get_var($sql);
// 		if($role){
// 			$roles = explode(',',$role);
// 			foreach($roles as $k=>$r){
// 				if($r == $roleId){
// 					unset($roles[$k]);
// 					break;
// 				}	
// 			}
// 			$sql = 'UPDATE '.DB_NAME_IM.'.basic_tbl SET roles="'.implode(',',$roles).'" WHERE uin="'.$uin.'"';
// 			if($this->db->query($sql))
// 				return array('Flag'=>100,'FlagString'=>'角色解绑成功');
// 			return array('Flag'=>102,'FlagString'=>'角色解绑失败');
// 		}
// 		return array('Flag'=>103,'FlagString'=>'该用户没有相应的角色');
// 	}
	
// 	public function updateRole($uin,$roles){
// 		if($uin<0 || empty($roles) || !is_array($roles)) return array('Flag'=>101,'FlagString'=>'core/role参数错误');
// 		$roles = implode(',', $roles);
// 		$sql = "UPDATE ".DB_NAME_IM.".basic_tbl SET roles='{$roles}' WHERE uin={$uin}";
// 		if($this->db->query($sql)){
// 			return array('Flag'=>100,'FlagString'=>'角色更新成功');
// 		}
// 		return array('Flag'=>102,'FlagString'=>'角色更新失败');
// 	}
	
	public function uinRole($groupid){
		if($groupid > 0){
			$sql = 'SELECT module_id,role_order_type FROM '.DB_NAME_GROUP.'.`tbl_groups` WHERE groupid = '.$groupid;
			$row = $this->db->get_row($sql,'ASSOC');
			if($row['module_id'] > 0){
				$sql = 'SELECT cate_id FROM '.DB_NAME_TPL.'.role_cate WHERE tpl_id =  '.$row['module_id'].' AND `status` = 1';
				$result = $this->db->get_results($sql,"ASSOC");
                $cate_id_arr = array();
                foreach($result as $one){
                    $cate_id_arr[] = $one['cate_id'];
                }
                $sql = "SELECT id FROM `".DB_NAME_TPL."`.`role` WHERE cate_id IN (".join(",", $cate_id_arr).")";
                $result = $this->db->get_results($sql,"ASSOC");
                $roles = array();
                foreach($result as $one){
                    $roles[] = $one['id'];
                }
			}
			return array('Flag'=>100,'FlagString'=>'成功','Roles'=>$roles,'RoleOrderType'=>$row['role_order_type']);
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}
	
	public function userRole($uin,$groupid,$roomid){
		if($uin>0 && $groupid >0 ){
			if($uin>=GUEST_UIN_START && $uin<=GUEST_UIN_END){//游客
				$role_result[] = array('RoleId'=>10006,'ChannelId'=>$roomid,'GroupId'=>$groupid);
			}else{
				$query_condition['Uin'] = intval($uin);
				$array[] = array('GroupId'=>(int)$groupid);
				$array[] = array('GroupId'=>1);
				$query_condition['$or'] = $array;
				//用户站角色及用户平台角色
				$levelrole_result = $this->mongodb->get_results(
					'kkyoo_role.uin_role',
					$query_condition
				);
				//用户排行类角色
				$rankrole_result = $this->mongodb->get_results(
					'kkyoo_role.business_role',
					$query_condition
				);
				$role_result = array_merge((array)$levelrole_result,(array)$rankrole_result);
				$r_id = ($groupid == 20228318) ? 10389 : 10007;
				$role_result[] = array('RoleId'=>$r_id,'ChannelId'=>$roomid,'GroupId'=>$groupid);//普通用户
			}
			foreach((array)$role_result as $key=>$value){
				$role_row = $this->getRoleInfo($value['RoleId'],$roomid,$groupid,$value['ChannelId'],$value['GroupId'],$value['Uin']);
				if($role_row['Flag'] == 100){
					$roles[$value['RoleId']] = $role_row['RoleInfo'];
				}
			}
			return array('Flag'=>100,'FlagString'=>'成功','Roles'=>$roles);
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}
	
	//添加站内角色
	public function addGroupRole($groupId,$uin,$roleId,$roomId,$newGroup,$ruleid,$module_id){
		if((empty($groupId)&&empty($roomId))||empty($uin)||empty($roleId)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
        if(!$module_id){
            $module_id = domain::main()->GroupKeyVal($groupId, "module_id");
        }
        $sql = "SELECT cate_id FROM ".DB_NAME_TPL.".`role_cate` WHERE tpl_id = ".$module_id." AND status=1";
        $res = $this->db->get_results($sql,"ASSOC");
		$cate_id_arr = array();
		foreach($res as $one){
			$cate_id_arr[] = $one['cate_id'];
		}
        $sql = "SELECT id FROM ".DB_NAME_TPL.".`role` WHERE cate_id IN (".join(",", $cate_id_arr).");";
        $res = $this->db->get_results($sql, "ASSOC");
        $roles_id = array();
        foreach($res as $one){
            $roles_id[] = $one['id'];
        }
        if(!in_array($roleId,$roles_id)){
			return array('Flag'=>102,'FlagString'=>'该站没有这个角色');
		}
		$data=array(
			'GroupId'=>(int)$groupId,
			'Uin'=>(int)$uin,
			'ChannelId'=>(int)$roomId,
		);
		if($ruleid > 0){
			$data['Ruleid'] = $ruleid;
		}
		if(empty($data['ChannelId'])){
			unset($data['ChannelId']);
		}
		$table='kkyoo_role.uin_role';
		if($ruleid > 0){
			$query = array('$set'=>array('RoleId'=>$roleId));
			$this->mongodb->query($table,$query,$data);
		}else{
			$data['RoleId'] = $roleId;
			$this->mongodb->query($table,$data);
		}		
		return array('Flag'=>100,'FlagString'=>'操作成功');
	}
	
	//删除站内角色
	public function deleteGroupRole($groupId,$uin,$roleIds,$roomId){
		if((empty($groupId)&&empty($roomId))||empty($uin)||!is_array($roleIds)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		foreach($roleIds as $key=>$val){
			$roleIds[$key]=intval($val);
		}
		$query=array(
			'GroupId'=>(int)$groupId,
			'Uin'=>(int)$uin,
			'RoleId'=>array('$in'=>$roleIds),
			'ChannelId'=>(int)$roomId
		);
		if(empty($query['ChannelId'])){
			unset($query['ChannelId']);
		}
		$table='kkyoo_role.uin_role';
		$this->mongodb->delete($table,$query);
		return array('Flag'=>100,'FlagString'=>'操作成功');
	}

	public function getRole($groupid,$roomid,$uin,$roleid,$ruleid,$category,$artist){
		$table = 'kkyoo_role.uin_role';
		if($groupid > 0 ){
			$query_condition['GroupId'] = $groupid;
		}
		if($roomid > 0 && $roleid <1){
			$query_condition['ChannelId'] = $roomid;
		}
		if(is_array($uin)){
			$query_condition['Uin']['$in'] = $uin;
		}else if((int)$uin > 0 ){
			$query_condition['Uin'] = (int)$uin;
		}
		if(is_array($roleid) ){
			$query_condition['RoleId']['$in'] = $roleid;
		}else if((int)$roleid > 0){
			$query_condition['RoleId'] = (int)$roleid;
		}
		if(is_array($ruleid) ){
			$query_condition['Ruleid']['$in'] = $ruleid;
		}else if((int)$ruleid > 0){
			$query_condition['Ruleid'] = (int)$ruleid;
		}
		if(empty($query_condition)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		/*if(is_array($ruleid) || $ruleid > 0){
			$table = 'kkyoo_role.business_role';
		}*/
		$role_result = $this->mongodb->get_results(
			$table,
			$query_condition
		);
		
		foreach((array)$role_result as $key=>$value){
			$role_row = $this->getRoleInfo($value['RoleId'],$roomid,$groupid,$value['ChannelId'],$value['GroupId'],$value['Uin'],$category,$artist);
			if($role_row['Flag'] == 100){
				$roles[] = array_merge($value, $role_row['RoleInfo']);
			}
		}
		if(!empty($roles)){
			return array('Flag'=>100,'FlagString'=>'成功','Roles'=>$roles);
		}
		return array('Flag'=>101,'FlagString'=>'失败');
	}
	
	/*	角色id 登陆房间id 登陆站id 角色房间id 角色站id 		*/
	private function getRoleInfo($id,$login_roomid,$login_groupid,$role_roomid,$role_groupid,$uin,$category=0,$artist=0){
		if($id > 0){
			$flag = false;
			$sql = "SELECT id,name,`order`,color,order_weight,font_color,role_big_icon,role_small_icon,icon_area,admin_type,scope,`attack`,`defense`,has_expire FROM ".DB_NAME_TPL.".role WHERE id = ".$id;
			$row = $this->db->get_row($sql,"ASSOC");
			$row['uin'] = $uin;
			$row['role_big_icon'] = cdn_url(PIC_API_PATH."/p/{$row['role_big_icon']}/0/0.jpg");
            $row['role_small_icon'] = cdn_url(PIC_API_PATH."/p/{$row['role_small_icon']}/0/0.jpg");
			if($row['scope']== 2 && $role_roomid == $login_roomid && $login_roomid >0){//角色作用域 房间
				$flag = true;
			}else if($row['scope']== 1 && $role_groupid == $login_groupid){//角色作用域 站
				$flag = true;
			}else if($row['scope']== 3){//角色作用域 平台
				$flag = true;
			}else if($login_roomid < 1){
				$flag = true;
			}
			if($flag && $row['has_expire']){
				if($category <= 0){
					$category = in_array($id,array(10390,10391)) ? 5 : 0;
				}
				$rst = httpPOST(GROUP_SHOP_API_PATH,array('extparam'=>array('Tag'=>'GetStock','Category'=>$category,'Data'=>array('uin'=>$uin,'role_id'=>$id,'room_id'=>$login_roomid,'group_id'=>$login_groupid,'artist'=>$artist))));
				if($rst['Flag'] !== 100){
					$flag = false;
				}
			}
			return $flag ? array('Flag'=>100,'FlagString'=>'成功','RoleInfo'=>$row) : array('Flag'=>101,'FlagString'=>'失败');
		}
	}
}


