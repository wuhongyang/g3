<?php

/**
 *   通行证操作接口
 *   文件: passport.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class passport
{
	//构造函数
	public function __construct($group_id) {
        $this->db = ($group_id==10000) ? db::connect(config('database','default')) : domain::main()->GroupDBConn('mysql', $group_id);
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
	
	/**
	 *   显示通行证列表
	 *   @message	array	$message	查找数据条件 
	 *   @return	array	$return		通行证所有信息
	 */
	public function listPass($message) {
		$where = " where 1";
		if(!empty($message['login'])){
			$where .= " AND login = '".$message['login']."'";
		}
		if(!empty($message['uin'])){
			$where .= " AND uin = '".$message['uin']."'";
		}
		if(!empty($message['data_group_id'])){
			$where .= " AND group_id = '".$message['data_group_id']."'";
		}
		if($message['platform'] != -1 && isset($message['platform'])){
			$where .= " AND platform = '".$message['platform']."'";
		}
		if($message['state'] != -1 && isset($message['state'])){
			$where .= " AND state = '".$message['state']."'";
		}
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_IM.".new_username".$where;
		$total = $this->db->get_var($sql);
		$page = $this->showPage($total);
		$sql = "SELECT * FROM ".DB_NAME_IM.".new_username ".$where." LIMIT ".$page['limit'];
		$res = $this->db->get_results($sql, "ASSOC");
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>(array)$res, "Page"=>$page['page']);
	}
	
	function getPassDetail($id){
		$sql = "SELECT * FROM ".DB_NAME_IM.".new_username WHERE id = '".$id."'";
		$username = $this->db->get_row($sql,"ASSOC");
		$sql = "SELECT * FROM ".DB_NAME_IM.".specialty WHERE uid = '".$username['uin']."'";
		$username['specialty'] = $this->db->get_row($sql,"ASSOC");
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$username);
	}

	/**
	 *   获得一个通行证用户账号
	 *   @param	array	$message	通行证id
	 *   @return	array	@return		通行证信息
	 */
	public function getOnePass($message) {
		$id = $message['id'];
		$sql = "SELECT * FROM ".DB_NAME_IM.".new_username WHERE id = '$id'";
		$row = $this->db->get_row($sql, "ASSOC");
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$row);
	}

	/**
	 *   设置账号状态
	 *   @param	array	$message	要将账号设置成什么状态的值
	 *   @return	array	$return		返回操作结果
	 */
	public function setPass($message) {
		$id = $message['id'];
		$sql = "SELECT `uin`,`state` FROM ".DB_NAME_IM.".new_username WHERE id = ".$id;
		$row = $this->db->get_row($sql);
		if($row['state'] == 1){
			$new_state = 0;
		}else{
			$new_state = 1;
		}
		$sql = "UPDATE ".DB_NAME_IM.".new_username SET state = '".$new_state."' WHERE uin = ".$row['uin'];
		$done = $this->db->query($sql);
		if($done){
			return array("Flag"=>100, "FlagString"=>"操作成功");
		}else{
			return array("Flag"=>102, "FlagString"=>"操作失败");
		}
	}

	/**
	 *   通行证密码重置
	 *   @param	array	@message	通行证uid和新密码
	 *   @return	array	@return		返回操作结果
	 */
	public function editPass($message) {
		$result = httpPOST("core/sso/sso_api.php",
                    array(
                    'extparam'=>
                        array('Tag'=>'EditPassword','Uid'=>$message['uin'],'Pass'=>$message['pass']),
                    'param'=>
                        array('GroupId'=>$message['data_group_id'])
                    ));
		if( $result['Flag'] == 100 ) {
			$return = array(
				'FlagString' => '密码修改成功 ',
				'Flag'  => '100'
			);
		} else {
			$return = array(
				'FlagString' => '密码修改失败',
				'Flag'  => '101'
			);
		}
		return $return;
	}
	
	//保存通行证信息
	public function savePassInfo($info){
		if($info['uin'] < 1){
			return array('Flag'=>100,'FlagString'=>'参数错误');
		}

		//保存通行证信息
		$citys = json_encode($citys);
		$sql = "UPDATE ".DB_NAME_IM.".new_username SET name='{$info['name']}',idcard='{$info['idcard']}',qq='{$info['qq']}' WHERE uin={$info['uin']}";
		if(!$this->db->query($sql)){
			return array('Flag'=>104,'FlagString'=>'保存通行证信息失败');
		}
		return array('Flag'=>100,'FlagString'=>'保存通行证信息成功');
	}
}