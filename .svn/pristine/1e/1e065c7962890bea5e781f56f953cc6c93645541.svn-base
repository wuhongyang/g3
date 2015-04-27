<?php
class ccs
{
	private $db,$dl;
	function __construct(){
		$this->db = db::connect(config('database','default'));
		$this->dl = new dlhelper($this->db);
	//	$this->logbuild = new logbuild();
	}
	
	function CheckUserPower($param){
		$roles_info = httpPOST(ROLE_API_PATH,array('extparam'=>array('Tag'=>'UserRole','Uin'=>$param['Uin'],'GroupId'=>$param['GroupId'],'ChannelId'=>$param['ChannelId'])));
		$uin_role = $roles_info['Roles'];//print_r($uin_role);exit;
		if($param['TargetUin'] != $param['Uin'] && $param['TargetUin'] > 0){
			$roles_info = httpPOST(ROLE_API_PATH,array('extparam'=>array('Tag'=>'UserRole','Uin'=>$param['TargetUin'],'GroupId'=>$param['GroupId'],'ChannelId'=>$param['ChannelId'])));
		}
		$target_role = $roles_info['Roles'];
		$param['TargetUin'] = $param['TargetUin'] == 0 ? $param['Uin'] : $param['TargetUin'];
		//================攻防值的累加方法,当用户所拥有的角色里其中任何一个有操作权限则将所有角色的攻防值累加,进行比较================//
		$attack=0;$defense=0;$uin_attack=false;$target_defense=false;
		foreach((array)$uin_role as $key=>$value){
			if(!$uin_attack){
				$sql = 'SELECT role_id,parent_id,child_id FROM '.DB_NAME_TPL.'.role_permission WHERE parent_id ='.$param['ParentId'].' AND child_id = '.$param['ChildId'].' AND role_id = '.$key;
				$row = $this->db->get_row($sql,"ASSOC");
				if(!empty($row)){
					$uin_attack = true;
				}
			}
			$attack += $value['attack'];
		}
		if($param['Uin'] == $param['TargetUin'] && $attack > 0 && $uin_attack){
			return array('Flag'=>100,'FlagString'=>'可以操作');
		}else if($attack <1 || !$uin_attack){
			return array('Flag'=>500,'FlagString'=>'权限不足，操作失败.');
		}
		
		foreach((array)$target_role as $key=>$value){
			if(!$target_defense && $key > 0){
				$sql = 'SELECT role_id,parent_id,child_id FROM '.DB_NAME_TPL.'.role_permission WHERE parent_id ='.$param['ParentId'].' AND child_id = '.$param['ChildId'].' AND role_id = '.$key;
				$row = $this->db->get_row($sql,"ASSOC");
				if(!empty($row)){
					$target_defense = true;
				}
			}
			$defense += $value['defense'];
		}
		if($attack <= $defense && $target_defense){
			return array('Flag'=>500,'FlagString'=>'权限不足，操作失败!');
		}
		return array('Flag'=>100,'FlagString'=>'可以操作');
	}

	//获取科目配置信息
	function getBusinessConfig($big,$case,$parent,$child,$check_bind=true){
		if($big < 10001 || $case < 10001 || $parent < 10001 || $child < 101){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$tbl_bigcase = DB_NAME_CCS.".tbl_bigcase";
		$tbl_case = DB_NAME_CCS.".tbl_case";
		$tbl_parent = DB_NAME_CCS.".tbl_parent";
		
		$tbl_child = $child > 900 ? DB_NAME_CCS.".tbl_child_common": DB_NAME_CCS.".tbl_child";
		$extra_where = $child > 900 ? '' : 'AND c.parent_id = d.parent_id';
		$sql = "SELECT c.api,d.*,a.bigcase_name,b.case_name,c.parent_name,c.flash_cmd FROM {$tbl_bigcase} as a,{$tbl_case} as b,{$tbl_parent} as c,{$tbl_child} as d
				WHERE a.bigcase_id = b.bigcase_id AND b.case_id = c.case_id  AND a.bigcase_id={$big} AND b.case_id={$case} {$extra_where} AND c.parent_id={$parent} AND d.child_id={$child}/*所有科目检查*/
				AND a.bigcase_status=1 AND b.case_status=1 AND c.parent_status=1 AND d.child_status=1 LIMIT 1";

		$config = $this->db->get_row($sql,'ASSOC');
		if(empty($config)){
			return array('Flag'=>102,'FlagString'=>'业务不存在');
		}
		//检查是否被关联资金操作
		if($check_bind && $config['bind_fund'] > 0 && empty($business_config['bind_child'])){
			$sql = "SELECT COUNT(*) FROM {$tbl_child} WHERE parent_id={$config['parent_id']} AND bind_child={$config['child_id']}";
			if($this->db->get_var($sql) > 0){
				return array('Flag'=>103,'FlagString'=>'关联科目禁止访问');
			}
		}
		return array('Flag'=>100,'Result'=>$config);
	}
	
	public function getBusinessInfo($big,$case,$parent,$child){
		$tbl_bigcase = DB_NAME_CCS.".tbl_bigcase";
		$tbl_case = DB_NAME_CCS.".tbl_case";
		$tbl_parent = DB_NAME_CCS.".tbl_parent";
		$tbl_child = DB_NAME_CCS.".tbl_child";
		$sql = "SELECT c.api,d.*,a.bigcase_name,b.case_name,c.parent_name FROM {$tbl_bigcase} as a,{$tbl_case} as b,{$tbl_parent} as c,{$tbl_child} as d
				WHERE a.bigcase_id = b.bigcase_id AND b.case_id = c.case_id AND c.parent_id=d.parent_id  AND a.bigcase_id={$big} AND b.case_id={$case} {$extra_where} AND c.parent_id={$parent} AND d.child_id={$child}/*所有科目检查*/
				AND a.bigcase_status=1 AND b.case_status=1 AND c.parent_status=1 AND d.child_status=1 LIMIT 1";
		$businessinfo = $this->db->get_row($sql,'ASSOC');
		if(empty($businessinfo)){
			return array('Flag'=>101,'FlagString'=>'fail');
		}
		return array('Flag'=>100,'Result'=>$businessinfo);
	}

	//一级科目操作
	function setBigCase($post){
		if($post['del'] > 0){//删除条目
			$rst = $this->dl->delete(DB_NAME_CCS.'.tbl_bigcase',"bigcase_id={$post['del']}");
			if($rst){
				$result = array('Flag'=>100,'FlagString'=>"删除成功");
			}else{
				$result = array('Flag'=>102,'FlagString'=>"删除失败");
			}
		}elseif($post['bigcase_id'] > 0){
			$rst = $this->dl->update(DB_NAME_CCS.'.tbl_bigcase',$post,"bigcase_id={$post['bigcase_id']}");
			if($rst){
				$result = array('Flag'=>100,'FlagString'=>"修改成功");
			}else{
				$result = array('Flag'=>103,'FlagString'=>"修改失败");
			}
		}else{ //添加条目
			$rst = $this->dl->insert(DB_NAME_CCS.'.tbl_bigcase',$post);
			if($rst){
				$result = array('Flag'=>100,'FlagString'=>"添加成功");
			}else{
				$result = array('Flag'=>104,'FlagString'=>"添加失败");
			}
		}
		return $result;
	}
	
	//获取一级科目
	function getBigCase($id){
		$sql = "SELECT * FROM ".DB_NAME_CCS.".tbl_bigcase WHERE bigcase_id={$id}";
		$result = $this->db->get_row($sql,'ASSOC');
		if($result){
			return array('Flag'=>100,'Result'=>$result);
		}else{
			return array('Flag'=>102,'FlagString'=>'科目不存在');
		}
	}
	
	//获取一级科目列表
	function getBigCaseList($param){
		$where = '1';
		if($param['status'] > 0){
			$status = $param['status'] - 1;
			$where .= " AND bigcase_status={$status}";
		}
		if(!empty($param['wd'])){
			$where .= " AND bigcase_name LIKE '%{$param['wd']}%'";
		}
		
		$_GET['page'] = intval($param['page']);
		$result = $this->dl->findAllPage(DB_NAME_CCS.'.tbl_bigcase',$where);
		$page = $this->dl->getPage();
		return array('Flag'=>100,'Result'=>$result,'Page'=>$page);
	}
	
	//得到使用的一级科目 (接口不符合规范，无Flag)
	public function getUseBigCase(){
		$sql = "SELECT bigcase_id as id,bigcase_name as name FROM ".DB_NAME_CCS.".tbl_bigcase WHERE bigcase_status=1";
		$result = $this->db->get_results($sql,'ASSOC');
		return $result;
	}
	
	//二级科目操作
	function setCase($post){
		if(!empty($post['del'])){//删除条目
			$rst = $this->dl->delete(DB_NAME_CCS.'.tbl_case',"case_id={$post['del']}");
			if($rst){
				$result = array('Flag'=>100,'FlagString'=>"删除成功");
			}else{
				$result = array('Flag'=>102,'FlagString'=>"删除失败");
			}
		}else{
			if(empty($post['bigcase_id'])) return array('Flag'=>101,'FlagString'=>'请选择一级科目类别');
			if($post['case_id'] > 0){
				$rst = $this->dl->update(DB_NAME_CCS.'.tbl_case',$post,"case_id={$post['case_id']}");
				if($rst){
					$result = array('Flag'=>100,'FlagString'=>"修改成功");
				}else{
					$result = array('Flag'=>103,'FlagString'=>"修改失败");
				}
			}else{ //添加条目
				$rst = $this->dl->insert(DB_NAME_CCS.'.tbl_case',$post);
				if($rst){
					$sql = "UPDATE ".DB_NAME_CCS.".tbl_case SET case_order={$rst} WHERE case_id={$rst}";
					$this->db->query($sql);
					$result = array('Flag'=>100,'FlagString'=>"添加成功");
				}else{
					$result = array('Flag'=>104,'FlagString'=>"添加失败");
				}
			}
		}
		if($result['Flag'] == 100){ //操作完成更新科目统计数量并显示操作结果
			//统计科目数量
			$casenum = $this->db->get_var("SELECT COUNT(*) FROM ".DB_NAME_CCS.".tbl_case WHERE bigcase_id={$post['bigcase_id']}");
			//更新一级统计数量
			$rst = $this->db->query("UPDATE ".DB_NAME_CCS.".tbl_bigcase SET case_count={$casenum} WHERE bigcase_id={$post['bigcase_id']}");
			if( ! $rst){
				$result = array('Flag'=>105,'FlagString'=>'更新科目数量失败');
			}
		}
		return $result;
	}
	
	function setCaseOrder($post){
		if(intval($post['bigcase_id']) > 0){
			$where = " AND bigcase_id={$post['bigcase_id']}";
		}
		if(isset($post['up'])){
			$order  = $this->db->get_row("SELECT case_id,case_order FROM ".DB_NAME_CCS.".tbl_case WHERE case_id={$post['up']}",'ASSOC');
			$switch = $this->db->get_row("SELECT case_id,case_order FROM ".DB_NAME_CCS.".tbl_case WHERE case_order>{$order['case_order']} ".$where." ORDER BY case_order ASC LIMIT 1",'ASSOC');
		}elseif(isset($post['down'])){
			$order  = $this->db->get_row("SELECT case_id,case_order FROM ".DB_NAME_CCS.".tbl_case WHERE case_id={$_GET['down']}",'ASSOC');
			$switch = $this->db->get_row("SELECT case_id,case_order FROM ".DB_NAME_CCS.".tbl_case WHERE case_order<{$order['case_order']} ".$where." ORDER BY case_order DESC LIMIT 1",'ASSOC');
		}
		$set1 = "UPDATE ".DB_NAME_CCS.".tbl_case SET case_order={$switch['case_order']} WHERE case_id={$order['case_id']}";
		$set2 = "UPDATE ".DB_NAME_CCS.".tbl_case SET case_order={$order['case_order']} WHERE case_id={$switch['case_id']}";
		if(empty($switch)) return array('Flag'=>102,'FlagString'=>'已经到顶端');
		if( ! $this->db->query($set1) || !$this->db->query($set2)){
			return array('Flag'=>103,'FlagString'=>'排序失败');
		}else{
			return array('Flag'=>100,'FlagString'=>'成功');
		}
	}
	
	//获取二级科目
	function getCase($id){
		$sql = "SELECT * FROM ".DB_NAME_CCS.".tbl_case WHERE case_id={$id} ORDER BY case_order DESC";
		$edit = $this->db->get_row($sql,'ASSOC');
		if($edit){
			$result = array('Flag'=>100,'Result'=>$edit);
		}else{
			$result = array('Flag'=>101,'FlagString'=>'科目不存在');
		}
		return $result;
	}
	
	//获取二级科目列表
	function getCaseList($param){
		$table = DB_NAME_CCS.'.tbl_case as a,'.DB_NAME_CCS.'.tbl_bigcase as b';
		$select = "a.*,b.bigcase_name";
		$where = 'a.bigcase_id=b.bigcase_id';
		if($param['status'] > 0){
			$status = $param['status'] - 1;
			$where .= " AND a.case_status={$status}";
		}
		if(!empty($param['wd'])){
			$where .= " AND a.case_name LIKE '%{$param['wd']}%'";
		}
		if($param['bigcase_id'] > 0){
			$where .= " AND a.bigcase_id={$param['bigcase_id']}";
		}
		$_GET['page'] = $param['page'];
		$lists = $this->dl->findAllPage($table,$where,'case_order DESC',$select);
		$page = $this->dl->getPage();
		return array('Flag'=>100,'Result'=>$lists,'Page'=>$page);
	}
	
	//得到使用的二级科目 (接口不符合规范，无Flag)
	public function getUseCase($bigcase_id){
		$sql = "SELECT case_id as id,case_name as name FROM ".DB_NAME_CCS.".tbl_case WHERE bigcase_id={$bigcase_id} AND case_status=1";
		$result = $this->db->get_results($sql,'ASSOC');
		return $result;
	}
	
	//三级科目操作
	function setParent($post){
		if($post['del'] > 0){ //删除条目
			$rst = $this->dl->delete(DB_NAME_CCS.'.tbl_parent',"parent_id={$post['del']}");
			if($rst){
				array('Flag'=>100,'FlagString'=>'删除成功');
			}else{
				array('Flag'=>101,'FlagString'=>'删除失败');
			}
		}else{
			if(empty($post['bigcase_id'])) return array('Flag'=>102,'FlagString'=>'请选择一级科目类别');
			if(empty($post['case_id'])) return array('Flag'=>103,'FlagString'=>'请选择二级科目类别');
			if($post['parent_id'] > 0){
				$rst = $this->dl->update(DB_NAME_CCS.'.tbl_parent',$post,"parent_id={$post['parent_id']}");
				if($rst){
					$result = array('Flag'=>100,'FlagString'=>'修改成功');
				}else{
					$result = array('Flag'=>104,'FlagString'=>'修改失败');
				}
			}else{ //添加条目
				$rst = $this->dl->insert(DB_NAME_CCS.'.tbl_parent',$post);
				if($rst){
					$result = array('Flag'=>100,'FlagString'=>'添加成功');
				}else{
					$result = array('Flag'=>105,'FlagString'=>'添加失败');
				}
			}
		}
		//操作完成更新科目统计数量并显示操作结果
		if($result['Flag'] == 100){
			//统计三个科目数量
			$casenum = $this->db->get_var("SELECT COUNT(*) FROM ".DB_NAME_CCS.".tbl_case WHERE bigcase_id={$post['bigcase_id']}");
			$parentnumUnderBig = $this->db->get_var("SELECT COUNT(*) FROM ".DB_NAME_CCS.".tbl_parent WHERE bigcase_id={$post['bigcase_id']}");
			$parentnum = $this->db->get_var("SELECT COUNT(*) FROM ".DB_NAME_CCS.".tbl_parent WHERE case_id={$post['case_id']}  AND bigcase_id={$post['bigcase_id']}");
			//更新一级统计数量
			$this->db->query("UPDATE ".DB_NAME_CCS.".tbl_bigcase SET case_count={$casenum} WHERE bigcase_id={$post['bigcase_id']}");
			$this->db->query("UPDATE ".DB_NAME_CCS.".tbl_bigcase SET parent_count={$parentnumUnderBig} WHERE bigcase_id={$post['bigcase_id']}");
			//更新二级统计数量
			$this->db->query("UPDATE ".DB_NAME_CCS.".tbl_case SET parent_count={$parentnum} WHERE case_id={$post['case_id']} AND bigcase_id={$post['bigcase_id']}");
		}
		return $result;
	}
	
	//获取三级科目
	function getParent($id){
		$sql = "SELECT * FROM ".DB_NAME_CCS.".tbl_parent WHERE parent_id={$id}";
		$edit = $this->db->get_row($sql,'ASSOC');
		if($edit){
			$result = array('Flag'=>100,'Result'=>$edit);
		}else{
			$result = array('Flag'=>101,'FlagString'=>'科目不存在');
		}
		return $result;
	}
	
	//获取三级科目列表
	function getParentList($param){
		$table = DB_NAME_CCS.'.tbl_parent as a,'.DB_NAME_CCS.'.tbl_case as b';
		$select = "a.*,b.case_name";
		$where = 'a.case_id=b.case_id';
		if(($param['status']) > 0){
			$status = $param['status'] - 1;
			$where .= " AND a.parent_status={$status}";
		}
		if(!empty($param['wd'])){
			$where .= " AND a.parent_name LIKE '%{$param['wd']}%'";
		}
		if($param['bigcase_id'] > 0){
			$where .= " AND a.bigcase_id={$param['bigcase_id']}";
		}
		if($param['case_id'] > 0){
			$where .= " AND a.case_id={$param['case_id']}";
		}
		$lists = $this->dl->findAllPage($table,$where,'case_id DESC',$select);
		$page = $this->dl->getPage();
		return array('Flag'=>100,'Result'=>$lists,'Page'=>$page);
	}
	
	//得到使用的三级科目 (接口不符合规范，无Flag)
	public function getUseParent($case_id){
		$sql = "SELECT parent_id as id,parent_name as name FROM ".DB_NAME_CCS.".tbl_parent WHERE case_id={$case_id} AND parent_status=1";
		$result = $this->db->get_results($sql,'ASSOC');
		return $result;
	}
	
	//四级科目操作
	function setChild($post){
		if(!empty($post['del'])){ //删除条目
			$rst = $this->dl->delete(DB_NAME_CCS.'.tbl_child',"id={$post['del']}");
			if($rst){
				$result = array('Flag'=>100,'FlagString'=>'删除成功');
			}else{
				$result = array('Flag'=>101,'FlagString'=>'删除失败');
			}
		}else{
			if(empty($post['bigcase_id'])) return array('Flag'=>102,'FlagString'=>'请选择一级科目类别');
			if(empty($post['case_id'])) return array('Flag'=>103,'FlagString'=>'请选择二级科目类别');
			if(empty($post['parent_id'])) return array('Flag'=>104,'FlagString'=>'请选择三级科目类别');
			if($post['id'] > 0){
				$rst = $this->dl->update(DB_NAME_CCS.'.tbl_child',$post,"id={$post['id']}");
				if($rst){
					$result = array('Flag'=>100,'FlagString'=>'修改成功');
				}else{
					$result = array('Flag'=>105,'FlagString'=>'修改失败');
				}
			}else{ //添加条目
				$child_id = $this->db->get_var("SELECT MAX(child_id) FROM ".DB_NAME_CCS.".tbl_child WHERE parent_id={$post['parent_id']} LIMIT 1");
				$post['child_id'] = empty($child_id)? 101 : $child_id+1;
				$rst = $this->dl->insert(DB_NAME_CCS.'.tbl_child',$post);
				if($rst){
					$result = array('Flag'=>100,'FlagString'=>'添加成功');
				}else{
					$result = array('Flag'=>106,'FlagString'=>'添加成功');
				}
			}
		}
		//操作完成更新科目统计数量并显示操作结果
		if($result['Flag'] == 100){
			//统计三个科目数量
			$casenum = $this->db->get_var("SELECT COUNT(*) FROM ".DB_NAME_CCS.".tbl_case WHERE bigcase_id={$_POST['bigcase_id']}");
			$parentnumUnderBig = $this->db->get_var("SELECT COUNT(*) FROM ".DB_NAME_CCS.".tbl_parent WHERE bigcase_id={$post['bigcase_id']}");
			$parentnum = $this->db->get_var("SELECT COUNT(*) FROM ".DB_NAME_CCS.".tbl_parent WHERE case_id={$_POST['case_id']} AND bigcase_id={$post['bigcase_id']}");
			$childnumUnderBig = $this->db->get_var("SELECT COUNT(*) FROM ".DB_NAME_CCS.".tbl_child WHERE bigcase_id={$_POST['bigcase_id']}");
			$childnumUnderCase = $this->db->get_var("SELECT COUNT(*) FROM ".DB_NAME_CCS.".tbl_child WHERE case_id={$_POST['case_id']} AND bigcase_id={$post['bigcase_id']}");
			$childnum = $this->db->get_var("SELECT COUNT(*) FROM ".DB_NAME_CCS.".tbl_child WHERE parent_id={$_POST['parent_id']} AND case_id={$_POST['case_id']} AND bigcase_id={$post['bigcase_id']}");
			//更新一级统计数量
			$this->db->query("UPDATE ".DB_NAME_CCS.".tbl_bigcase SET case_count={$casenum} WHERE bigcase_id={$_POST['bigcase_id']}");
			$this->db->query("UPDATE ".DB_NAME_CCS.".tbl_bigcase SET parent_count={$parentnumUnderBig} WHERE bigcase_id={$_POST['bigcase_id']}");
			$this->db->query("UPDATE ".DB_NAME_CCS.".tbl_bigcase SET child_count={$childnumUnderBig} WHERE bigcase_id={$_POST['bigcase_id']}");
			//更新二级统计数量
			$this->db->query("UPDATE ".DB_NAME_CCS.".tbl_case SET parent_count={$parentnum} WHERE case_id={$_POST['case_id']}  AND bigcase_id={$post['bigcase_id']}");
			$this->db->query("UPDATE ".DB_NAME_CCS.".tbl_case SET child_count={$childnumUnderCase} WHERE case_id={$_POST['case_id']}  AND bigcase_id={$post['bigcase_id']}");
			//更新三级统计数量
			$this->db->query("UPDATE ".DB_NAME_CCS.".tbl_parent SET child_count={$childnum} WHERE parent_id={$_POST['parent_id']} AND case_id={$_POST['case_id']}  AND bigcase_id={$post['bigcase_id']}");
		}
		return $result;
	}
	
	//获取四级科目
	function getChild($id){
		$sql = "SELECT * FROM ".DB_NAME_CCS.".tbl_child WHERE id={$id}";
		$edit = $this->db->get_row($sql,'ASSOC');
		if($edit){
			$result = array('Flag'=>100,'Result'=>$edit);
		}else{
			$result = array('Flag'=>101,'FlagString'=>'科目不存在');
		}
		return $result;
	}
	
	//获取四级科目列表
	function getChildList($param){
		$table = DB_NAME_CCS.'.tbl_child as a,'.DB_NAME_CCS.'.tbl_parent as b';
		$select = "a.*,b.parent_name,b.channel_id";
		$where = 'a.parent_id=b.parent_id';
		if(($param['status']) > 0){
			$status = $param['status'] - 1;
			$where .= " AND a.child_status={$status}";
		}
		if(!empty($param['wd'])){
			$where .= " AND a.child_name LIKE '%{$param['wd']}%'";
		}
		if($param['bigcase_id'] > 0){
			$where .= " AND a.bigcase_id={$param['bigcase_id']}";
		}
		if($param['case_id'] > 0){
			$where .= " AND a.case_id={$param['case_id']}";
		}
		if($param['parent_id'] > 0){
			$where .= " AND a.parent_id={$param['parent_id']}";
		}
		if($param['child_id'] > 0){
			$where .= " AND a.child_id={$param['child_id']}";
		}
		$lists = $this->dl->findAllPage($table,$where,'case_id DESC',$select);
		$page = $this->dl->getPage();
		return array('Flag'=>100,'Result'=>$lists,'Page'=>$page);
	}
	
	//得到使用的四级科目 (接口不符合规范，无Flag)
	public function getUseChild($parent_id){
		$sql = "SELECT child_id as id,child_name as name FROM ".DB_NAME_CCS.".tbl_child WHERE parent_id={$parent_id} AND child_status=1";
		$result = $this->db->get_results($sql,'ASSOC');
		$sql = "SELECT child_id AS id,child_name AS name FROM ".DB_NAME_CCS.".tbl_child_common";
		$common = $this->db->get_results($sql,'ASSOC');
		$result = array_merge($result,$common);
		return $result;
	}
	
	//同步
	public function childSync($ids){
		if(empty($ids))
			return array('Flag'=>101,'FlagString'=>'参数错误');
		
		$id = implode(',',(array)$ids);
		$sql = 'SELECT a.*,p.channel_id,p.allow_overdraw,p.overdraw_money, b.bigcase_name,c.case_name ,p.parent_name FROM '.DB_NAME_CCS.'.tbl_child AS a,'.DB_NAME_CCS.'.tbl_parent AS p ,'.DB_NAME_CCS.'.tbl_case c ,'.DB_NAME_CCS.'.tbl_bigcase b WHERE a.id IN('.$id.') AND a.parent_id = p.parent_id AND a.case_id = c.case_id AND a.bigcase_id =b.bigcase_id';
		$chlids = $this->db->get_results($sql,'ASSOC');
		$results = array();
		foreach((array)$chlids as $child){
			$query = true;
			if((int)$child['fund_type'] == 1 && $child['bind_fund'] == 1){
				$sql = "REPLACE INTO ".DB_NAME_KWEALTH_PLAT.".config(id,bigcase_id,case_id,parent_id,child_id,channel_id,overdraw_money,trade_type,fund_type,trade_property,is_income_pay,is_power,is_log,child_status,bigcase_name,case_name,parent_name,child_name,child_desc)
				VALUES({$child['id']},{$child['bigcase_id']},{$child['case_id']},{$child['parent_id']},{$child['child_id']},{$child['channel_id']},{$child['overdraw_money']},{$child['trade_type']},{$child['fund_type']},{$child['trade_property']},{$child['is_income_pay']},{$child['is_power']},{$child['is_log']},{$child['child_status']},'{$child['bigcase_name']}','{$child['case_name']}','{$child['parent_name']}','{$child['child_name']}','{$child['child_desc']}')";
			}elseif((int)$child['fund_type'] == 2 && $child['bind_fund'] == 1){
				$sql = "REPLACE INTO ".DB_NAME_KMONEY.".config(id,bigcase_id,case_id,parent_id,child_id,channel_id,overdraw_money,trade_type,fund_type,trade_property,is_income_pay,is_power,is_log,child_status,bigcase_name,case_name,parent_name,child_name,child_desc)
				VALUES({$child['id']},{$child['bigcase_id']},{$child['case_id']},{$child['parent_id']},{$child['child_id']},{$child['channel_id']},{$child['overdraw_money']},{$child['trade_type']},{$child['fund_type']},{$child['trade_property']},{$child['is_income_pay']},{$child['is_power']},{$child['is_log']},{$child['child_status']},'{$child['bigcase_name']}','{$child['case_name']}','{$child['parent_name']}','{$child['child_name']}','{$child['child_desc']}')";
			}elseif($child['bind_fund'] == 1){
				$sql = "REPLACE INTO ".DB_NAME_VOUCHER_PLAT.".config(id,bigcase_id,case_id,parent_id,child_id,channel_id,overdraw_money,trade_type,fund_type,trade_property,is_income_pay,is_power,is_log,child_status,bigcase_name,case_name,parent_name,child_name,child_desc)
				VALUES({$child['id']},{$child['bigcase_id']},{$child['case_id']},{$child['parent_id']},{$child['child_id']},{$child['channel_id']},{$child['overdraw_money']},{$child['trade_type']},{$child['fund_type']},{$child['trade_property']},{$child['is_income_pay']},{$child['is_power']},{$child['is_log']},{$child['child_status']},'{$child['bigcase_name']}','{$child['case_name']}','{$child['parent_name']}','{$child['child_name']}','{$child['child_desc']}')";
			}
			if($this->db->query($sql)){
				$results[] = array('id'=>$child['id'],'Flag'=>100,'FlagString'=>'同步成功');
			}else{
				$results[] = array('id'=>$child['id'],'Flag'=>102,'FlagString'=>'同步失败');
			}
		}
		return $results;
	}
	
	public function getFlashCMD($bigcase_id,$case_id,$parent_id){
		if($bigcase_id<=0 || $case_id<=0 || $parent_id <=0) return array('Flag'=>101,'FlagString'=>'参数错误');
		$sql = 'SELECT flash_cmd,cmd_path FROM '.DB_NAME_CCS.'.tbl_parent WHERE bigcase_id="'.$bigcase_id.'" AND case_id="'.$case_id.'" AND parent_id="'.$parent_id.'"';
		$row = $this->db->get_row($sql);
		return array('Flag'=>100,'FlagString'=>'成功','FlashCMD'=>$row['flash_cmd'],'CmdPath'=>$row['cmd_path']);
	}
	
	//取得有权限控制的科目详情
	public function getPowerInfo(){
		$sql = 'SELECT d.bigcase_id,d.case_id,d.parent_id,d.child_id,child_name,parent_name,case_name,bigcase_name FROM '.DB_NAME_CCS.'.tbl_child AS d,'.DB_NAME_CCS.'.tbl_parent AS p,'.DB_NAME_CCS.'.tbl_case AS c,'.DB_NAME_CCS.'.tbl_bigcase AS b WHERE d.is_power=1 AND child_status=1 AND d.parent_id=p.parent_id AND d.case_id=c.case_id AND d.bigcase_id=b.bigcase_id';
		return array('Flag'=>100,'FlagString'=>'成功','Result'=>$this->db->get_results($sql,'ASSOC'));
	}

	public function getInfoUnderBig($bigcase_id){
		if($bigcase_id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		//二级科目
		$sql = "SELECT case_id,case_name FROM ".DB_NAME_CCS.".tbl_case WHERE bigcase_id={$bigcase_id} AND `case_status`=1";
		$rst = $this->db->get_results($sql,ASSOC);
		$info = array();
		foreach ((array)$rst as $key => $val) {
			$result = $this->getUseParent($val['case_id']);
			$parent = array();
			foreach($result as $value){
				$parent[] = array(
					'parent_id' => $value['id'],
					'parent_name' => $value['name']
				);
			}
			$info[$key] = array(
				'case_id' => $val['case_id'],
				'case_name' => $val['case_name'],
				'parent' => $parent
			);
		}
		return array('Flag'=>100,'FlagString'=>'成功','Result'=>$info);
	}
	
	function getAdminLeftMenu($param,$gid,$menu){
		$tbl_case = DB_NAME_CCS.".tbl_case";
		$tbl_parent = DB_NAME_CCS.".tbl_parent";
		$tbl_child = DB_NAME_CCS.".tbl_child";
		if($gid == 1 && $menu !=1){
			$Admin = true;
			$where = '';
		}else{
			$levels = json_decode($param,true);
			foreach((array)$levels as $lev_key =>$lel_value){
				$case_ids .= $lev_key.',';
				if($gid == 1 && $menu !=1){
					$Admin = true;
					break;
				}
				foreach($lel_value as $par_key => $par_value){
					$parent_ids[$lev_key] .= $par_key.',';
					foreach($par_value as $chl_key =>$chl_value){
						$child_ids[$lev_key][$par_key] .= $chl_key.',';
					}
				}
			}
			$case_ids = rtrim($case_ids,',');
			$case_where = " AND case_id IN ($case_ids) ";
		}
		if(!empty($case_ids) || $Admin){
			$sql = "SELECT case_id,case_name FROM {$tbl_case} WHERE bigcase_id=10002 AND case_status=1 $case_where ORDER BY case_order DESC";
			$menu = $this->db->get_results($sql,'ASSOC');
			foreach($menu as $key=>$value){
				$parent_id = rtrim($parent_ids[$value['case_id']],',');
				if(!empty($parent_id) || $Admin ){
					$parent_where = $Admin ? ' ' : " AND parent_id IN ($parent_id) "; 
					$menu[$key]['parent'] = $this->db->get_results("SELECT parent_id,parent_name FROM {$tbl_parent} WHERE case_id={$value['case_id']} AND parent_status=1 $parent_where ",'ASSOC');
					foreach($menu[$key]['parent'] as $k=>$v){
						$child_id = rtrim($child_ids[$value['case_id']][$v['parent_id']],',');
						if(!empty($child_id) || $Admin ){
							$child_where = $Admin ? ' ' : " AND child_id IN ($child_id) ";
							$child_menu = $this->db->get_results("SELECT child_id,child_name FROM {$tbl_child} WHERE parent_id={$v['parent_id']} AND child_status=1 $child_where",'ASSOC');
							if(!empty($child_menu)){
								$menu[$key]['parent'][$k]['child'] = $child_menu;
							}else{
								unset($menu[$key]['parent'][$k]);
							}
						}
					}
				}
			}
			return array('Flag'=>100,'Result'=>$menu);
		}
	}
	
	function sc_save($data){
		$this->db->start_transaction();
		$sql = "TRUNCATE TABLE ".DB_NAME_CCS.".`start_config`";
		$done = $this->db->query($sql);
		if(!$done){
			$this->db->rollback();
			return array('Flag'=>103, 'FlagString'=>"数据库错误");
		}
		foreach($data["ids"] as $key => $value){
			$ids = explode(",", $value);
			$data["min"][$key];
			$data["need"][$key];
			$sql = "INSERT INTO ".DB_NAME_CCS.".`start_config`(`id`,`bigcase_id`,`case_id`,`parent_id`,`min`,`need`,`wait_time`,`allow_balance`,`allow_close`)
					VALUES ( NULL,'".$ids[0]."','".$ids[1]."','".$ids[2]."','".$data["min"][$key]."','".$data["need"][$key]."','".$data["wait_time"][$key]."',".intval($data['allow_balance'][$ids[2]]).",".intval($data['allow_close'][$ids[2]]).")";
			$done = $this->db->query($sql);
			if(!$done){
				$this->db->rollback();
				return array('Flag'=>103, 'FlagString'=>"数据库错误");
			}
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'更新成功');
	}
	
	function sc_save2($data){
		//保存人气票设置
		$sql = "SELECT turn FROM ".DB_NAME_CCS.".`start_config_group`
				WHERE group_id = ".$data['GroupId']." AND bigcase_id = ".$data['BigCaseId']." AND case_id = ".$data['CaseId']." AND parent_id = ".$data['ParentId'];
		$turn = $this->db->get_var($sql);
		if(empty($turn) || $turn == 0){
			return array('Flag'=>102, 'FlagString'=>'人气票业务尚未开启，请先开启人气票业务！');
		}
		$sql = "SELECT min FROM ".DB_NAME_CCS.".`start_config` WHERE bigcase_id = ".$data['BigCaseId']." AND case_id = ".$data['CaseId']." AND parent_id = ".$data['ParentId'];
		$need = $this->db->get_var($sql);
		if(empty($need)){
			return array('Flag'=>102, 'FlagString'=>'此业务没有开启！');
		}
		if($data['Post']['weight'] >= $need){
			return array('Flag'=>102, 'FlagString'=>'不能大于等于最小值');
		}
		
		$sql = "UPDATE ".DB_NAME_CCS.".`start_config_group` SET `config`='".json_encode($data['Post'])."' WHERE bigcase_id = ".$data['BigCaseId']." AND case_id = ".$data['CaseId']." AND parent_id = ".$data['ParentId']." AND group_id = ".$data['GroupId'];
		$done = $this->db->query($sql);
		if($done){
			return array('Flag'=>100, 'FlagString'=>'成功');
		}else{
			return array('Flag'=>102, 'FlagString'=>'数据库错误');
		}
	}
	
	function sc_get($data){
		$sql = "SELECT `config` FROM ".DB_NAME_CCS.".`start_config_group`
				WHERE group_id = ".$data['GroupId']." AND bigcase_id = ".$data['BigCaseId']." AND case_id = ".$data['CaseId']." AND parent_id = ".$data['ParentId'];
		$config = $this->db->get_var($sql);
		return array('Flag'=>100, 'FlagString'=>'查询成功', 'Data'=>$config);
	}
	
	function sc_get_list(){
		$sql = "SELECT * FROM ".DB_NAME_CCS.".`start_config`";
		$result = $this->db->get_results($sql, "ASSOC");
		foreach($result as $key=>$value){
			$result2 = $this->getBusinessConfig($value["bigcase_id"],$value["case_id"],$value["parent_id"],101);
			$result[$key]["parent_names"]= $result2['Result']['parent_name'];
			$result[$key]["ids"] = $value["bigcase_id"].",".$value["case_id"].",".$value["parent_id"];
		}
		$data['Result'] = $result;
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$data);
	}
	
	function sc_get_list2($group_id){
		if(!$group_id){
			return array('Flag'=>101, 'FlagString'=>'参数错误');
		}
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_CCS.".`start_config`";
		$total = $this->db->get_var($sql);
		$page_arr = $this->showPage($total, 20);
		$sql = "SELECT * FROM ".DB_NAME_CCS.".`start_config` limit ".$page_arr['limit'];
		$result = $this->db->get_results($sql, "ASSOC");
		$data['Page'] = $page_arr['page'];
		foreach($result as $key=>$value){
			$result2 = $this->getBusinessConfig($value["bigcase_id"],$value["case_id"],$value["parent_id"],101);
			$result[$key]["parent_name"] = $result2['Result']['parent_name'];
			$result[$key]["balance"] = get_parent_money($value["bigcase_id"], $value["case_id"], $value["parent_id"], $group_id);
			$sql = "SELECT turn FROM ".DB_NAME_CCS.".`start_config_group`
					WHERE group_id = ".$group_id." AND bigcase_id = ".$value['bigcase_id']." AND case_id = ".$value['case_id']." AND parent_id = ".$value['parent_id'];
			$result[$key]["turn"] = $this->db->get_var($sql);
		}
		$data['Result'] = $result;
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$data);
	}

	private function callMuDuInterface($group_id,$bigcase_id,$case_id,$parent_id,$money){
		$sql = "SELECT url FROM ".DB_NAME_CCS.".group_game_interface WHERE group_id={$group_id} AND bigcase_id={$bigcase_id} AND case_id={$case_id} AND parent_id={$parent_id}";
		$url = $this->db->get_var($sql);
		if(!empty($url)){
			$url = urldecode($url);
			socket_request($url.'&Money='.$money);
		}
	}
	
	function Charge($ext_param){
		//判断是否充值或关闭
		if(!$ext_param['GroupId'] || !$ext_param['BigCaseId'] || !$ext_param['CaseId'] || !$ext_param['ParentId']){
			return array('Flag'=>101, 'FlagString'=>'参数错误');
		}
		$data = array();
		$fail_string = "";
		$this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
        $this->db->start_transaction();
		$sql = "SELECT `min`, `need` FROM ".DB_NAME_CCS.".`start_config` WHERE bigcase_id = ".$ext_param['BigCaseId']." AND case_id = ".$ext_param['CaseId']." AND parent_id=".$ext_param['ParentId'].' LIMIT 1 FOR UPDATE';
		$row = $this->db->get_row($sql, "ASSOC");
		$group_id = $ext_param['GroupId'];
		$balance = get_parent_money(10006, 10049, 10269, $group_id);
		$ext_param['Balance'] = $ext_param['Balance']?$ext_param['Balance']:get_parent_money($ext_param['BigCaseId'], $ext_param['CaseId'], $ext_param['ParentId'], $group_id);
		$sql = "SELECT id, turn FROM ".DB_NAME_CCS.".`start_config_group`
					WHERE group_id = ".$group_id." AND bigcase_id = ".$ext_param['BigCaseId']." AND case_id = ".$ext_param['CaseId']." AND parent_id = ".$ext_param['ParentId'];
		$turn = $this->db->get_row($sql, 'ASSOC');
		
		if($ext_param['Turn'] && $balance+$ext_param['Balance'] >= $row['need']){
			//用户开启
			if(!$turn){
				$sql = "INSERT INTO ".DB_NAME_CCS.".`start_config_group`(`id`,`group_id`,`bigcase_id`,`case_id`,`parent_id`,`turn`)
					VALUES ( NULL,'".$group_id."','".$ext_param['BigCaseId']."','".$ext_param['CaseId']."','".$ext_param['ParentId']."','1')";
				$this->db->query($sql);
			}
			if($turn['turn'] == 0){
				$sql = "UPDATE ".DB_NAME_CCS.".`start_config_group` SET `turn`='1' WHERE `id`='".$turn['id']."'";
				$this->db->query($sql);
			}
			$turn['turn'] = 1;
			$this->db->commit();

			return array('Flag'=>100, 'FlagString'=>"开启成功");
		}elseif($ext_param['Turn'] && $balance+$ext_param['Balance'] < $row['need']){
			$this->db->rollback();
			return array('Flag'=>150, 'FlagString'=>"余额不足");
		}
		
		if($turn['turn'] == 0){
			//用户没有开启
			return array('Flag'=>150,'FlagString'=>'业务未开启');
		}
		
		if($ext_param['Balance'] < $row['min'] && $balance >= $row['need']){
			//用户余额可以支付基础值
			$trade = $row['need'];
			//预存账户扣除
			$param = array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>104,
					'MoneyWeight'=>$trade, 'Desc'=>'预存账户-开启业务-金币支出');
			$request = array('param'=>$param,'extparam'=>array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$group_id));
			$rst = httpPOST(KMONEY_API_PATH,$request);
			if($rst['Flag']!=100){
				$this->db->rollback();
				return $rst;
			}
			//站内业务存入
			$param = array('BigCaseId'=>$ext_param['BigCaseId'],'CaseId'=>$ext_param['CaseId'],'ParentId'=>$ext_param['ParentId'],'ChildId'=>906,
					'MoneyWeight'=>$trade, 'Desc'=>'站内业务-开启业务-金币存入');
			$request = array('param'=>$param,'extparam'=>array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$group_id));
			$rst = httpPOST(KMONEY_API_PATH,$request);
			if($rst['Flag']!=100){
				$this->db->rollback();
				return $rst;
			}
			$request['param']['GroupId'] = $group_id;
		//	$this->logbuild->setlog($request);
		}elseif($ext_param['Balance'] < $row['min'] && $balance < $row['need']){
			$result = $this->close($ext_param);
			if($result['Flag']!=100){
				$this->db->rollback();
				return $result;
			}
			$this->db->commit();
			return array('Flag'=>150,'FlagString'=>'业务已关闭');
		}
		//特殊处理
		//人气票
		if($ext_param['BigCaseId'] == 10001 && $ext_param['CaseId'] == 10027 && $ext_param['ParentId'] == 10272){
			$sql = "SELECT config FROM ".DB_NAME_CCS.".`start_config_group`
					WHERE group_id = ".$group_id." AND bigcase_id = ".$ext_param['BigCaseId']." AND case_id = ".$ext_param['CaseId']." AND parent_id = ".$ext_param['ParentId'];
			$config = json_decode($this->db->get_var($sql), true);
			if($config['turn']){
				$start_time = (int)str_replace(":", "", $config['start_time']);
				$end_time = (int)str_replace(":", "", $config['end_time']);
				$time = date("Hi");
				if($start_time < $time && $time < $end_time){
					$data = array("MoneyWeight"=>$config['weight']);
				}
			}
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'成功', 'Data'=>$data);
	}
	
	function close($ext_param){
		$sql = "SELECT allow_close FROM ".DB_NAME_CCS.".start_config WHERE bigcase_id={$ext_param['BigCaseId']} AND case_id={$ext_param['CaseId']} AND parent_id={$ext_param['ParentId']}";
		$allow_close = $this->db->get_var($sql);
		if($allow_close != 1){
			return array('Flag'=>101,'FlagString'=>'不能关闭');
		}
		//用户关闭
		$group_id = $ext_param['GroupId'];
		
		$sql = "SELECT id, turn FROM ".DB_NAME_CCS.".`start_config_group`
					WHERE group_id = ".$group_id." AND bigcase_id = ".$ext_param['BigCaseId']." AND case_id = ".$ext_param['CaseId']." AND parent_id = ".$ext_param['ParentId'];
		$turn = $this->db->get_row($sql, 'ASSOC');
		if($turn['turn'] == 0){
			return array('Flag'=>150,'FlagString'=>'业务已经关闭');
		}
		//记录关闭时间,重置config
		$config = array();
		$config['close_time'] = time();
		$config = json_encode($config);
		//更新
		$sql = "UPDATE ".DB_NAME_CCS.".`start_config_group` SET `turn`='0',`config`='".$config."' WHERE `id`='".$turn['id']."'";
		$this->db->query($sql);
		
		/*
		$trade = get_parent_money($ext_param['BigCaseId'], $ext_param['CaseId'], $ext_param['ParentId'], $group_id);
		//预存账户存入
		$param = array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>105,
				'MoneyWeight'=>$trade, 'Desc'=>'预存账户-关闭业务-v点存入');
		$request = array('param'=>$param,'extparam'=>array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$group_id));
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag']!=100){
			return $rst;
		}
		//站内业务扣除
		$param = array('BigCaseId'=>$ext_param['BigCaseId'],'CaseId'=>$ext_param['CaseId'],'ParentId'=>$ext_param['ParentId'],'ChildId'=>907,
				'MoneyWeight'=>$trade, 'Desc'=>'站内业务-关闭业务-v点支出');
		$request = array('param'=>$param,'extparam'=>array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$group_id));
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag']!=100){
			return $rst;
		}
		*/
		
		return array('Flag'=>100,'FlagString'=>'成功');
	}
	
	function sc_balance_add($data){
		$balance = get_parent_money(10006, 10049, 10269, $data['GroupId']);
		$trade = $data['Value'];
		if($trade > $balance){
			return array('Flag'=>102, 'FlagString'=>'余额不足');
		}
		//预存账户扣除
		$param = array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>104,
				'MoneyWeight'=>$trade, 'Desc'=>'预存账户-开启业务-金币支出');
		$request = array('param'=>$param,'extparam'=>array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$data['GroupId']));
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag']!=100){
			return $rst;
		}
		//站内业务存入
		$param = array('BigCaseId'=>$data['BigCaseId'],'CaseId'=>$data['CaseId'],'ParentId'=>$data['ParentId'],'ChildId'=>906,
				'MoneyWeight'=>$trade, 'Desc'=>'站内业务-开启业务-金币存入');
		$request = array('param'=>$param,'extparam'=>array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$data['GroupId']));
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag']!=100){
			return $rst;
		}
		$request['param']['GroupId'] = $data['GroupId'];
		$this->callMuDuInterface($data['GroupId'],$data['BigCaseId'],$data['CaseId'],$data['ParentId'],$trade);
	//	$this->logbuild->setlog($request);
		return array('Flag'=>100,'FlagString'=>'补充成功');
	}
	
	function sc_balance_get($data){
		$sql = "SELECT turn, `config` FROM ".DB_NAME_CCS.".`start_config_group`
					WHERE group_id = ".$data['GroupId']." AND bigcase_id = ".$data['BigCaseId']." AND case_id = ".$data['CaseId']." AND parent_id = ".$data['ParentId'];
		$row = $this->db->get_row($sql, 'ASSOC');
		if($row['turn'] == 1){
			return array('Flag'=>150,'FlagString'=>'业务尚未关闭');
		}
		$sql = "SELECT wait_time,allow_balance FROM ".DB_NAME_CCS.".`start_config` WHERE bigcase_id = ".$data['BigCaseId']." AND case_id = ".$data['CaseId']." AND parent_id = ".$data['ParentId'];
		$game_config = $this->db->get_row($sql,ASSOC);
		if($game_config['allow_balance'] != 1){
			return array('Flag'=>102,'FlagString'=>'不允许提取余额');
		}
		//检查时间差
		$config = json_decode($row['config'], true);
		$diff = time() - $config['close_time'];
		$need_wait = $game_config['wait_time'] - $diff;
		if($need_wait > 0){
			return array('Flag'=>102,'FlagString'=>'失败,请等待'.$need_wait.'秒后再次重试收回余额');
		}
		
		$trade = get_parent_money($data['BigCaseId'], $data['CaseId'], $data['ParentId'], $data['GroupId']);
		if($trade == 0){
			return array('Flag'=>100,'FlagString'=>'提取成功');
		}
		//预存账户存入
		$param = array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>105,
				'MoneyWeight'=>$trade, 'Desc'=>'预存账户-关闭业务-金币存入');
		$request = array('param'=>$param,'extparam'=>array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$data['GroupId']));
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag']!=100){
			return $rst;
		}
		//站内业务扣除
		$param = array('BigCaseId'=>$data['BigCaseId'],'CaseId'=>$data['CaseId'],'ParentId'=>$data['ParentId'],'ChildId'=>907,
				'MoneyWeight'=>$trade, 'Desc'=>'站内业务-关闭业务-金币支出');
		$request = array('param'=>$param,'extparam'=>array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$data['GroupId']));
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag']!=100){
			return $rst;
		}
		$request['param']['GroupId'] = $data['GroupId'];
	//	$this->logbuild->setlog($request);
		
		return array('Flag'=>100,'FlagString'=>'提取成功'); 
	}
	
	//分页
	private function showPage($total,$perpage=15){
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