<?php

class Permission{
	private $db;

	public function __construct(){
		$this->db = domain::main()->GroupDBConn();
		$this->storage = cache::connect(config('cache','memcache'));
	}

	//站登录
	public function dpUserLogin($data){
		//登录
		if($data['isQQLogin']){
	 		$result = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'OpenidLogin',"Userinfo"=>array('openid'=>$data['openid']))));
		}else{
			$result = httpPOST(SSO_API_PATH,array('param'=>array('Uin'=>$data['username'],'SessionKey'=>md5($data['password']),'GroupId'=>$data['GroupId']),'extparam'=>array('Tag'=>'UserLogin',"Ip"=>get_ip(),'Remember'=>$data['remember'])));
		}
		if($result['Flag'] != 100){
			return $result;
		}
		
		$sql = "SELECT m.role_id,m.group_id FROM ".DB_NAME_GROUP.".`tbl_groups_member` m LEFT JOIN ".DB_NAME_GROUP.".tbl_role r ON m.role_id=r.id WHERE m.uin={$result['Uin']} AND r.status=1";
		$row = $this->db->get_row($sql,ASSOC);

		if(empty($row)){
			//是否是站长
			$info = getChannelInfo($result['Uin'],0,8);
			if(empty($info)){
				return array('Flag'=>102,'FlagString'=>'未绑定用户,不能访问站管理后台!');
			}
		}

		//保存信息到MC
		if(!$this->storage->set(sha1($result['Token']),$result,604800)){
			return array('Flag'=>102,'FlagString'=>'storage error');
		}
		//保存到cookie
		$expire = $data['remember']>0? time()+86400 * 30 : 0;
		setcookie('DP_LOGIN_TOKEN',sha1($result['Token']),$expire, '/',$_SERVER['HTTP_HOST']);

		return array('Flag'=>100,'FlagString'=>'成功','Token'=>$result['Token']);
	}

	//角色组列表
	public function roleList($groupId){
		$groupId = intval($groupId);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".tbl_role WHERE group_id={$groupId}";
		$rst = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'角色组列表','List'=>$rst);
	}

	//角色组详情
	public function roleInfo($id){
		$id = intval($id);
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".tbl_role WHERE id={$id}";
		$info = $this->db->get_row($sql,ASSOC);
		if(empty($info)){
			return array('Flag'=>102,'FlagString'=>'不存在该角色组');
		}
		$sql = "SELECT uin FROM ".DB_NAME_GROUP.".tbl_groups_member WHERE group_id={$info['group_id']} AND role_id={$id}";
		$uinInfo = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'成功','RoleInfo'=>$info,'UinInfo'=>$uinInfo);
	}

	//添加角色组
	public function insertRole($data){
		if(empty($data['name'])){
			return array('Flag'=>101,'FlagString'=>'角色组名称不能为空');
		}
		//同一个站下相同名字只能一个
		$sql  = "SELECT group_id FROM ".DB_NAME_GROUP.".tbl_role WHERE group_id={$data['group_id']} AND name='{$data['name']}'";
		if($this->db->get_var($sql) > 0){
			return array('Flag'=>102,'FlagString'=>'已经存在该角色组名称');
		}

		//站点基本信息为默认权限
		$permission = json_encode(array("10258"));

		$this->db->start_transaction();
		//添加到角色组表，取得last_id，用于插入成员表
		$sql = "INSERT INTO ".DB_NAME_GROUP.".tbl_role(`group_id`,`name`,`permission`,`status`,`uptime`) VALUES({$data['group_id']},'{$data['name']}','{$permission}',{$data['status']},".time().")";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>106,'FlagString'=>'添加角色组失败');
		}
		$roleId = $this->db->insert_id();

		//多个成员组成SQL，一同插入成员表
		if(count($data['uins']) > 0){
			$sql = "INSERT INTO ".DB_NAME_GROUP.".tbl_groups_member(`group_id`,`role_id`,`uin`) VALUES";
			foreach((array)$data['uins'] as $val){
				$rst = $this->verifyUin($val);
				if($rst['Flag'] != 100){
					$this->db->rollback();
					return $rst;
				}
				$sql .= " ({$data['group_id']},{$roleId},{$val}),";
			}
			$sql = rtrim($sql, ',');
			if(!$this->db->query($sql)){
				$this->db->rollback();
				return array('Flag'=>105,'FlagString'=>'添加角色组成员失败');
			}
		}

		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'添加角色组成功');
	}

	//更新角色组
	public function updateRole($data){
		//验证参数
		if($data['id'] < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		if(empty($data['name'])){
			return array('Flag'=>101,'FlagString'=>'角色组名称不能为空');
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".tbl_role where id={$data['id']}";
		$row = $this->db->get_row($sql);
		if(empty($row)){
			return array('Flag'=>101,'FlagString'=>'非法请求');
		}

		$sql  = "SELECT group_id FROM ".DB_NAME_GROUP.".tbl_role WHERE id!={$data['id']} AND group_id={$row['group_id']} AND name='{$data['name']}'";
		if($this->db->get_var($sql) > 0){
			return array('Flag'=>102,'FlagString'=>'已经存在该角色组名称');
		}

		$this->db->start_transaction();
		//角色成员表先删除再插入实现更新
		$sql = "DELETE FROM ".DB_NAME_GROUP.".tbl_groups_member WHERE group_id={$row['group_id']} AND role_id={$data['id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'修改角色组成员失败');
		}
		if(count($data['uins']) > 0){
			$sql = "INSERT INTO ".DB_NAME_GROUP.".tbl_groups_member(`group_id`,`role_id`,`uin`) VALUES";
			$data['uins'] = array_unique((array)$data['uins']);
			foreach((array)$data['uins'] as $val){
				$rst = $this->verifyUin($val,$data['id']);
				if($rst['Flag'] != 100){
					$this->db->rollback();
					return $rst;
				}
				$sql .= "({$row['group_id']},{$data['id']},{$val}),";
			}
			$sql = rtrim($sql, ',');
			if(!$this->db->query($sql)){
				$this->db->rollback();
				return array('Flag'=>105,'FlagString'=>'修改角色组成员失败');
			}
		}
		
		$sql = "UPDATE ".DB_NAME_GROUP.".tbl_role SET `name`='{$data['name']}',`status`={$data['status']} WHERE id={$data['id']} AND group_id={$row['group_id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>106,'FlagString'=>'修改角色组失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'修改角色组成功');
	}

	//赋予角色权限
	public function setPermission($data){
		if($data['id']<1 || !isset($data['isDz']) || !isset($data['uin'])){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//不是站长不能更新自己所在的角色组
		if($data['isDz'] != 1){
			$sql = "SELECT role_id FROM ".DB_NAME_GROUP.".tbl_groups_member WHERE group_id={$data['group_id']} AND uin={$data['uin']}";
			$role_id = $this->db->get_var($sql);
			if($role_id == $data['id']){
				return array('Flag'=>102,'FlagString'=>'不能修改自己所在的角色组');
			}
		}

		//把默认的加进来
		$permission = array_merge((array)$data['parent'],array("10258"));
		$permission = json_encode($permission);

		$sql = "UPDATE ".DB_NAME_GROUP.".tbl_role SET permission='{$permission}' WHERE id='{$data['id']}' AND group_id={$data['group_id']}";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'赋予角色权限失败');
		}
		return array('Flag'=>100,'FlagString'=>'赋予角色权限成功');
	}

	//读取角色组拥有的权限
	public function permissionInfo($id,$groupId){
		$id = intval($id);
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".tbl_role WHERE id={$id} AND group_id={$groupId}";
		$info = $this->db->get_row($sql,ASSOC);
		if(!empty($info)){
			$info['permission'] = json_decode($info['permission']);
		}
		return array('Flag'=>100,'FlagString'=>'成功','Info'=>$info);
	}

	//用户ID拥有的权限
	public function ownPermissions($uin){
		$uin = intval($uin);
		if($uin < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		$sql = "SELECT m.role_id,m.group_id,r.permission FROM ".DB_NAME_GROUP.".`tbl_groups_member` m LEFT JOIN ".DB_NAME_GROUP.".tbl_role r ON m.role_id=r.id WHERE m.uin={$uin} AND r.status=1";
		$row = $this->db->get_row($sql,ASSOC);
		if(!empty($row)){
			$info['permission'] = json_decode($row['permission'], true);
			$info['groupId'] = $row['group_id'];
			$info['isDz'] = 0;
		}else{
			$channelInfo = getChannelInfo($uin,0,8);
			if(!empty($channelInfo)){
				$info['isDz'] = 1;
				$info['groupId'] = $channelInfo['room_id'];
			}else{
				$info['isDz'] = 0;
			}
		}
		//站管理导航栏的权限
		$menuPermissions=array(
			'groupManage'=>0,
			'roomManage'=>0,
			'signedManage'=>0,
			'vipManage'=>0,
			'voucherManage'=>0,
			'imformation'=>0,
			'permissionsManage'=>0,
			'decoration'=>0,
			'goodsManage'=>0,
		);
		//如果是站长，拥有全部权限
		if($info['isDz']==1){
			foreach($menuPermissions as $key=>$val){
				$menuPermissions[$key]=1;
			}
			$info['permission']['isDz']=1;
		}
		else{
			//站管理导航需要的三级科目
			$parent=array(10258,10259,10315,10619);
			$intersect=array_intersect($info['permission'],$parent);
			if(!empty($intersect)){
				$menuPermissions['groupManage']=1;
			}
			//房间管理导航需要的三级科目
			$parent=array(10099,10100,10101,10102,10103,10153,10155,10223,10251,10257,10260);
			$intersect=array_intersect($info['permission'],$parent);
			if(!empty($intersect)){
				$menuPermissions['roomManage']=1;
			}
			//签约管理导航需要的三级科目
			$parent=array(10261,10262,10273);
			$intersect=array_intersect($info['permission'],$parent);
			if(!empty($intersect)){
				$menuPermissions['signedManage']=1;
			}
			//会员管理导航需要的三级科目
			$parent=array(10267,10268);
			$intersect=array_intersect($info['permission'],$parent);
			if(!empty($intersect)){
				$menuPermissions['vipManage']=1;
			}
			//V点管理导航需要的三级科目
			$parent=array(10269,10270);
			$intersect=array_intersect($info['permission'],$parent);
			if(!empty($intersect)){
				$menuPermissions['voucherManage']=1;
			}
			//站税收导航需要的三级科目
			$parent=array(10263,10264,10265,10577,10632);
			$intersect=array_intersect($info['permission'],$parent);
			if(!empty($intersect)){
				$menuPermissions['imformation']=1;
			}
			//权限管理导航需要的三级科目
			$parent=array(10279,10280,10281);
			$intersect=array_intersect($info['permission'],$parent);
			if(!empty($intersect)){
				$menuPermissions['permissionsManage']=1;
			}

			//主页装修
			$parent = array(10318,10319,10327,10328,10329,10330,10331);
			$intersect=array_intersect($info['permission'],$parent);
			if(!empty($intersect)){
				$menuPermissions['decoration']=1;
			}
			//商品管理
			$parent = array(10429);
			$intersect=array_intersect($info['permission'],$parent);
			if(!empty($intersect)){
				$menuPermissions['goodsManage']=1;
			}
		}
		$info['menuPermissions']=$menuPermissions;
		return array('Flag'=>100,'FlagString'=>'成功','Result'=>$info);
	}

	//检查用户ID的合法性
	public function verifyUin($uin,$roleId=0){
		//是否存在该UIN
		$userInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'UinExist','Uin'=>$uin)));
		if($userInfo['Flag'] == 102){
			return array('Flag'=>103,'FlagString'=>'不存在的用户ID：'.$uin);
		}
		//是否在其他角色组中
		$isExistSql = "SELECT uin FROM ".DB_NAME_GROUP.".tbl_groups_member WHERE uin={$uin}";
		if($roleId > 0){
			$isExistSql .= " AND role_id!={$roleId}";
		}
		$isExist = $this->db->get_results($isExistSql);
		if(!empty($isExist)){
			return array('Flag'=>104,'FlagString'=>"{$uin}已经存在或者在其他角色组");
		}
		//是否为站长
		$isDz = getChannelType($uin,0,8);
		if($isDz > 0){
			return array('Flag'=>105,'FlagString'=>"{$uin}是站长，不能添加");
		}

		return array('Flag'=>100,'FlagString'=>'成功');
	}
}