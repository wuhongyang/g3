<?php

/**
 *   群组操作接口
 *   文件: Role.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class Roles
{
	//数据库指针
	protected $db = null;

	//构造函数
	public function __construct() {
		$this->db = db::connect(config('database','default'));
	}

	//角色分类
	function cateList($data,$is_not_page=false){
		$where = 'where 1';
		if($data['status']=='0' || $data['status']=='1'){
			$where .= ' AND `status`="'.$data['status'].'"';
		}
		if($data['cate_name']){
			$where .= ' AND `cate_name`="'.$data['cate_name'].'"';
		}
        if($data['tpl_id']){
            $where .= ' AND `tpl_id`="'.$data['tpl_id'].'"';
        }
        
		if($is_not_page){
			$sql = "SELECT * FROM ".DB_NAME_TPL.".`role_cate` ".$where;
			$list = $this->db->get_results($sql, "ASSOC");
			return array('Flag'=>100,'FlagString'=>'查询成功','CateList'=>$list);
		}else{
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_TPL.".`role_cate` ".$where;
			$total = $this->db->get_var($sql);
			$page_arr = $this->showPage($total);
			$sql = "SELECT * FROM ".DB_NAME_TPL.".`role_cate` ".$where." LIMIT ".$page_arr['limit'];
			$list = $this->db->get_results($sql, "ASSOC");
			foreach($list as $k=>$one){
				$sql = "SELECT COUNT(*) FROM ".DB_NAME_TPL.".`role` WHERE cate_id = ".$one['cate_id'];
				$list[$k]['role_total'] = $this->db->get_var($sql);
			}
			return array('Flag'=>100,'FlagString'=>'查询成功','CateList'=>$list,'Page'=>$page_arr['page']);
		} 
	}
	
	function saveCate($data){
		if(!$data['cate_name']){
			return array("Flag"=>101, "FlagString"=>"角色组名称不能为空");
		}
		if(!$data['cate_desc']){
			return array("Flag"=>101, "FlagString"=>"角色组描述不能为空");
		}
		if(!isset($data['status'])){
			return array("Flag"=>101, "FlagString"=>"不能没有状态");
		}
		if($data['cate_id'] != -1){
			return $this->updateCate($data['cate_id'], $data['cate_name'], $data['cate_desc'], $data['status']);
		}else{
			return $this->addCate($data['cate_name'], $data['cate_desc'], $data['status'], $data['tpl_id']);
		}
	}
	
	//角色组详情
	function cateInfo($cate_id){
		$sql = "SELECT * FROM ".DB_NAME_TPL.".`role_cate` WHERE cate_id = '".$cate_id."'";
		$row = $this->db->get_row($sql);
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$row);
	}
	
	//删除角色组
	function cateDel($cate_id){
		$sql = "DELETE FROM ".DB_NAME_TPL.".`role_cate` WHERE `cate_id` = '".$cate_id."';";
		if($this->db->query($sql)){
			return array("Flag"=>100, "FlagString"=>"删除成功");
		}else{
			return array("Flag"=>102, "FlagString"=>"删除失败");
		}
	}
	
	//角色组添加
	function addCate($cate_name, $cate_desc, $status, $tpl_id){
		$sql = "SELECT cate_name FROM ".DB_NAME_TPL.".`role_cate` WHERE cate_name = '".$cate_name."'";
		$name = $this->db->get_var($sql);
		if($name){
			return array("Flag"=>102, "FlagString"=>"已经存在相同分类");
		}
		$sql = "INSERT INTO ".DB_NAME_TPL.".`role_cate` (`cate_name`, `cate_desc`, `status`, `tpl_id`) VALUES ('".$cate_name."', '".$cate_desc."', '".$status."', '".$tpl_id."');";
		$this->db->query($sql);
		return array("Flag"=>100, "FlagString"=>"添加成功");
	}
	
	//角色组更新
	function updateCate($cate_id, $cate_name, $cate_desc, $status){
		$sql = "SELECT cate_name FROM ".DB_NAME_TPL.".`role_cate` WHERE cate_name = '".$cate_name."' AND cate_id != '".$cate_id."'";
		$name = $this->db->get_var($sql);
		if($name){
			return array("Flag"=>102, "FlagString"=>"已经存在相同分类");
		}
		$sql = "UPDATE ".DB_NAME_TPL.".`role_cate` SET `cate_name` = '".$cate_name."' ,`cate_desc` = '".$cate_desc."' ,`status` = '".$status."' WHERE `cate_id` = '".$cate_id."'";
		$this->db->query($sql);
		return array("Flag"=>100, "FlagString"=>"更新成功");
	}
	
	//角色列表
	public function roleList($data,$is_not_page=false){
		$where = '';
		if($data['cate_id']){
			$where .= ' AND cate_id = '.$data['cate_id'];
		}
		if($data['name']){
			$where .= ' AND name LIKE "'.$data['name'].'%"';
		}	
		if($data['status']=='0' || $data['status']=='1'){
			$where .= ' AND `status`="'.$data['status'].'"';
		}
		if($data['id'] > 0){
			$data['id'] = intval($data['id']);
			$where .= ' AND id="'.$data['id'].'"';
		}	
		if($data['rule'] > 0){
			$data['rule'] = intval($data['rule']);
			$where .= ' AND rule='.$data['rule'];
		}
        if($data['tpl_id']){
            $sql = "SELECT cate_id FROM  ".DB_NAME_TPL.".`role_cate` WHERE tpl_id = ".$data['tpl_id'];
            $res = $this->db->get_results($sql, "ASSOC");
            $cate_id = array();
            foreach($res as $one){
                $cate_id[] = $one['cate_id'];
            }
            $where .= ' AND cate_id IN ('.join(",", $cate_id).')';
        }
		if(!empty($where)){
			$where = ' WHERE '.ltrim($where,' AND');
		}
		$sql = 'SELECT COUNT(1) FROM '.DB_NAME_TPL.'.role '.$where;
		$total = $this->db->get_var($sql);
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = 'SELECT * FROM '.DB_NAME_TPL.'.role '.$where.' ORDER BY `status` DESC';
			if($is_not_page != true){
				$sql .= ' LIMIT '.$page_arr['limit'];
			}
			$list = $this->db->get_results($sql,'ASSOC');
		}
		return array('Flag'=>100,'FlagString'=>'角色列表','RoleList'=>$list,'Page'=>$page_arr['page']);
	}

	//角色详情
	public function roleInfo($roleid){
		$roleid = intval($roleid);
		if($roleid < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_TPL.".role WHERE id={$roleid}";
		$row = $this->db->get_row($sql,ASSOC);
		if(empty($row)){
			return array('Flag'=>102,'FlagString'=>'非法角色ID');
		}
		return array('Flag'=>100,'FlagString'=>'角色详情','RoleInfo'=>$row);
	}

	//复制角色
	public function copyRole($roleid){
		$roleid = intval($roleid);
		if($roleid < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$info = $this->roleInfo($roleid);
		if($info['Flag'] != 100){
			return $info;
		}
		$sql = "SELECT bigcase_id,case_id,parent_id,child_id FROM ".DB_NAME_TPL.".role_permission WHERE role_id={$roleid}";
		$permissionList = $this->db->get_results($sql,"ASSOC");
		$this->db->start_transaction();
		$sql = "INSERT INTO ".DB_NAME_TPL.".role(`name`,`desc`,`rule`,`role_show_1`,`role_show_2`,`scope`,`order`,`color`,`order_weight`,`font_color`,`status`,`role_big_icon`,`role_small_icon`,`icon_area`,`admin_type`,`attack`,`defense`,`cate_id`) (SELECT CONCAT('重命名_',`name`),`desc`,`rule`,`role_show_1`,`role_show_2`,`scope`,`order`,`color`,`order_weight`,`font_color`,`status`,`role_big_icon`,`role_small_icon`,`icon_area`,`admin_type`,`attack`,`defense`,`cate_id` FROM ".DB_NAME_TPL.".role WHERE id={$roleid})";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>102,'FlagString'=>'复制失败');
		}
		$new_role_id = $this->db->insert_id();
		//复制权限
		foreach ($permissionList as $val) {
			$sql = "INSERT INTO ".DB_NAME_TPL.".role_permission(`role_id`,`bigcase_id`,`case_id`,`parent_id`,`child_id`) VALUES({$new_role_id},{$val['bigcase_id']},{$val['case_id']},{$val['parent_id']},{$val['child_id']})";
			if(!$this->db->query($sql)){
				$this->db->rollback();
				return array('Flag'=>102,'FlagString'=>'角色权限复制失败');
			}
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'复制成功');
	}

	//删除角色
	public function delRole($roleid){
		$roleid = intval($roleid);
		if($roleid < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$info = $this->roleInfo($roleid);
		if($info['Flag'] != 100){
			return $info;
		}
		//是否绑定了套餐
		require_once 'role_package.class.php';
		$rp = new RolePackage();
		$packageList = $rp->packageList(array('role_id'=>$roleid),true);
		if(!empty($packageList['PackageList'])){
			return array('Flag'=>102,'FlagString'=>'该角下绑定着角色套餐，请先解绑角色与角色套餐之间的绑定关系再做删除');
		}
		$sql = "DELETE FROM ".DB_NAME_TPL.".role WHERE id={$roleid}";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'删除失败');
		}
		return array('Flag'=>100,'FlagString'=>'删除成功');
	}
	
	//定义新角色
	public function addRole($data){
		$check = $this->checkParam($data);
		if($check['Flag'] != 100){
			return $check;
		}
		$data['name'] = addslashes(htmlspecialchars($data['name']));
		$data['desc'] = addslashes(htmlspecialchars($data['desc']));
		$role_show_1 = intval($data['rule_show'][0]);
		$role_show_2 = intval($data['rule_show'][1]);
		$data['has_expire'] = intval($data['has_expire']);
		$data['order'] = intval($data['order']);
		//$data['color'] = json_encode($data['color']);
		$color = '[';
		foreach ($data['color'] as $val) {
			if(!is_numeric($val) || $val < 0 || $val > 255){
				$color = '';
				break;
			}
			$color .= intval($val).',';
		}
		if($color){
			$data['color'] = trim($color, ',').']';
		}else{
			$data['color'] = '';
		}
		$font_color = '[';
		foreach ($data['font_color'] as $val) {
			if(!is_numeric($val) || $val < 0 || $val > 255){
				$font_color = '';
				break;
			}
			$font_color .= intval($val).',';
		}
		if($font_color){
			$data['font_color'] = trim($font_color, ',').']';
		}else{
			$data['font_color'] = '';
		}
		
		$data['order_weight'] = intval($data['order_weight']);
		$icon_area = '[';
		foreach ((array)$data['icon_area'] as $value) {
			$icon_area .= (int)$value.',';
		}
		$icon_area = trim($icon_area, ',');
		$icon_area .= ']';
		$data['admin_type'] = intval($data['admin_type']);
		$data['attack'] = intval($data['attack']);
		$data['defense'] = intval($data['defense']);

// 		$sql = 'SELECT id FROM '.DB_NAME_PERMISSION.'.role WHERE name="'.$data['name'].'"';
// 		if($this->db->get_var($sql) > 0) {
// 			return array('Flag'=>103,'FlagString'=>'该角色名称已经存在');
// 		}
		$sql = "INSERT INTO ".DB_NAME_TPL.".role(`name`,`desc`,`rule`,`role_show_1`,`role_show_2`,`scope`,`has_expire`,`order`,`color`,`order_weight`,`font_color`,`status`,`role_big_icon`,`role_small_icon`,`icon_area`,`admin_type`,`attack`,`defense`,`cate_id`) VALUES('{$data['name']}','{$data['desc']}','{$data['rule']}',{$role_show_1},{$role_show_2},{$data['scope']},{$data['has_expire']},{$data['order']},'{$data['color']}',{$data['order_weight']},'{$data['font_color']}',{$data['status']},'{$data['img_big']}','{$data['img_small']}','{$icon_area}',{$data['admin_type']},{$data['attack']},{$data['defense']},{$data['cate_id']})";
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'添加成功');
		}
		return array('Flag'=>102,'FlagString'=>'添加失败');
	}
	
	//修改角色
	public function updateRole($data,$id){
		if(empty($data['name']) || $id<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$data['name'] = addslashes(htmlspecialchars($data['name']));
		$data['desc'] = addslashes(htmlspecialchars($data['desc']));
		if($data['rule'] != 1){
			$role_show_1 = 0;
			$role_show_2 = 0;
		}else{
			$role_show_1 = $data['rule_show'][0];
			$role_show_2 = $data['rule_show'][1];
		}
		$data['has_expire'] = intval($data['has_expire']);
		$data['order'] = intval($data['order']);
		$color = '[';
		foreach ($data['color'] as $val) {
			if(!is_numeric($val) || $val < 0 || $val > 255){
				$color = '';
				break;
			}
			$color .= intval($val).',';
		}
		if($color){
			$data['color'] = trim($color, ',').']';
		}else{
			$data['color'] = '';
		}

		$font_color = '[';
		foreach ($data['font_color'] as $val) {
			if(!is_numeric($val) || $val < 0 || $val > 255){
				$font_color = '';
				break;
			}
			$font_color .= intval($val).',';
		}
		if($font_color){
			$data['font_color'] = trim($font_color, ',').']';
		}else{
			$data['font_color'] = '';
		}

		$data['order_weight'] = intval($data['order_weight']);
		$icon_area = '[';
		foreach ((array)$data['icon_area'] as $value) {
			$icon_area .= (int)$value.',';
		}
		$icon_area = trim($icon_area, ',');
		$icon_area .= ']';
		$data['admin_type'] = intval($data['admin_type']);
		$data['attack'] = intval($data['attack']);
		$data['defense'] = intval($data['defense']);

// 		$sql = 'SELECT id FROM '.DB_NAME_PERMISSION.'.role WHERE id!="'.$id.'" AND name="'.$data['name'].'"';
// 		if($this->db->get_var($sql) > 0){
// 			return array('Flag'=>103,'FlagString'=>'该角色名称已经存在');
// 		}
		$sql = "UPDATE ".DB_NAME_TPL.".role SET `name`='{$data['name']}',`desc`='{$data['desc']}',`rule`={$data['rule']},`role_show_1`={$role_show_1},`role_show_2`={$role_show_2},`scope`={$data['scope']},`has_expire`={$data['has_expire']},`order`={$data['order']},`color`='{$data['color']}',`order_weight`='{$data['order_weight']}',`font_color`='{$data['font_color']}',`status`={$data['status']},`role_big_icon`='{$data['img_big']}',`role_small_icon`='{$data['img_small']}',`icon_area`='{$icon_area}',`admin_type`={$data['admin_type']},`attack`={$data['attack']},`defense`={$data['defense']},`cate_id`={$data['cate_id']} WHERE id={$id}";
		if($this->db->query($sql))
			return array('Flag'=>100,'FlagString'=>'修改成功');
		return array('Flag'=>102,'FlagString'=>'修改失败');
	}

	private function checkParam($data){
		if(empty($data['name'])){
			return array('Flag'=>101,'FlagString'=>'角色名称不能为空');
		}
		if(empty($data['desc'])){
			return array('Flag'=>101,'FlagString'=>'角色描述不能为空');
		}
		if(!is_numeric($data['attack']) || !is_numeric($data['defense'])){
			return array('Flag'=>101,'FlagString'=>'角色攻防值必须为数字');
		}
		if($data['rule'] < 1){
			return array('Flag'=>101,'FlagString'=>'请选择规则定义');
		}
		if($data['scope'] < 1){
			return array('Flag'=>101,'FlagString'=>'请选择角色作用域');
		}
		if(!is_numeric($data['order'])){
			return array('Flag'=>101,'FlagString'=>'角色排序必须为数字');
		}
		if(empty($data['img_big']) || empty($data['img_small'])){
			return array('Flag'=>101,'FlagString'=>'请先上传图片');
		}
		return array('Flag'=>100,'FlagString'=>'参数正确');
	}
	
	private function getPowerInfo(){
		$res = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetPowerInfo')));
		return $res;
	}
	
	public function getRoles(){
		$sql = 'SELECT id,name FROM '.DB_NAME_TPL.'.role WHERE status="1"';
		return $this->db->get_results($sql);
	}

	public function permissionList($roleid){
		$roleid = intval($roleid);
		if($roleid < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_TPL.".role_permission WHERE role_id={$roleid}";
		$permissionList = $this->db->get_results($sql,ASSOC);
		foreach($permissionList as $key=>$val){
			$extparam = array('Tag'=>'GetBusinessInfo','BigCaseId'=>$val['bigcase_id'],'CaseId'=>$val['case_id'],'ParentId'=>$val['parent_id'],'ChildId'=>$val['child_id']);
			$businessinfo = httpPOST(CCS_API_PATH,array('extparam'=>$extparam));
			$permissionList[$key]['bigcase_name'] = $businessinfo['Result']['bigcase_name'];
			$permissionList[$key]['case_name'] = $businessinfo['Result']['case_name'];
			$permissionList[$key]['parent_name'] = $businessinfo['Result']['parent_name'];
			$permissionList[$key]['child_name'] = $businessinfo['Result']['child_name'];
		}
		$roleInfo = $this->roleInfo($roleid);
		return array('Flag'=>100,'FlagString'=>'角色权限列表','PermissionList'=>$permissionList,'RoleName'=>$roleInfo['RoleInfo']['name']);
	}

	public function saveRolePermission($roleid,$data){
		$roleid = intval($roleid);
		if($roleid<1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		$roleInfo = $this->roleInfo($roleid);
		if($roleInfo['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'角色不存在');
		}

		$this->db->start_transaction();
		$sql = "DELETE FROM ".DB_NAME_TPL.".role_permission WHERE role_id={$roleid}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'修改角色权限失败');
		}
		foreach ((array)$data as $val) {
			$rule = json_decode($val,true);
			$sql = "INSERT INTO ".DB_NAME_TPL.".role_permission(`role_id`,`bigcase_id`,`case_id`,`parent_id`,`child_id`) VALUES({$roleid},{$rule['bigcase_id']},{$rule['case_id']},{$rule['parent_id']},{$rule['child_id']})";
			if(!$this->db->query($sql)){
				$this->db->rollback();
				return array('Flag'=>103,'FlagString'=>'修改角色权限失败');
			}
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'修改角色权限成功');
	}

	public function getRoleByIdentity($identity1, $identity2, $module_id){
		$identity1 = intval($identity1);
		$identity2 = intval($identity2);
		$module_id = intval($module_id);
		$sql = "SELECT cate_id FROM ".DB_NAME_TPL.".`role_cate` WHERE tpl_id = ".$module_id." AND status=1";
        $res = $this->db->get_results($sql,"ASSOC");
		$cate_id_arr = array();
		foreach($res as $one){
			$cate_id_arr[] = $one['cate_id'];
		}
        //$sql = "SELECT id FROM ".DB_NAME_PERMISSION.".`role` WHERE cate_id IN (".join(",", $cate_id_arr).");";
		//$sql = "SELECT cate_id FROM ".DB_NAME_PERMISSION.".role_cate WHERE tpl_id={$module_id} AND status=1";
		//$cate_id = $this->db->get_var($sql);
		$cate_ids = implode(',', $cate_id_arr);
		$sql = "SELECT * FROM ".DB_NAME_TPL.".role WHERE role_show_1={$identity1} AND role_show_2={$identity2} AND cate_id IN ({$cate_ids}) ORDER BY id DESC LIMIT 1";
		$row = $this->db->get_row($sql, ASSOC);
		if(!$row){
			return array('Flag'=>101,'FlagString'=>'无角色');
		}
		return array('Flag'=>100,'FlagString'=>'角色信息','Role'=>$row);
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


