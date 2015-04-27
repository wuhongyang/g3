<?php

/*奥点网络媒体互动用户计费管理平台软件
 *模块: 奥点网络媒体互动用户计费管理平台软件 大厅基础模块
 *文件: join.class.php
 *copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */

class join {

	function __construct(){
        $this->db = domain::main()->GroupDBConn("mysql");
	}
	
	public function saveOpenInfo($uin,$info){
		if($uin <= 0 || empty($info)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$rst = $this->checkInfo($info);
		if($rst['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>$rst['FlagString']);
		}
		
		$array = array(
			'provinceId' => intval($info['province']),
			'cityId' => intval($info['city'])
		);
		$json = json_encode($array);
		$sql = "UPDATE ".DB_NAME_IM.".basic_tbl set name='{$info['name']}',qq='{$info['qq']}',phone={$info['telphone']},province='{$info['province']}',city='{$info['city']}' WHERE uin={$uin}";
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'保存通行证信息成功');
		}else{
			return array('Flag'=>104,'FlagString'=>'保存通行证信息失败');
		}
	}

	//开通室主和艺人
	public function applyArtistAndRoomer($info){
		$sql = "SELECT id FROM ".DB_NAME_IM.".apply WHERE uin={$info['uin']} AND `type`={$info['type']}";
		$count = $this->db->get_var($sql);
		if($count > 0){
			return array('Flag'=>102,'FlagString'=>'不能重复申请');
		}
		$time = time();
		$sql = "INSERT INTO ".DB_NAME_IM.".apply(group_id,uin,province,city,`area`,`type`,`status`,apply_time) VALUES({$info['group_id']},{$info['uin']},{$info['province']},{$info['city']},-1,{$info['type']},1,{$time})";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'申请失败');
		}
		return array('Flag'=>100,'FlagString'=>'申请成功');
	}
	
	
	
	//申请信息获取
	//public function joinInfo($uin,$role_type){
	//	if($uin <= 0){
	//		return array('Flag'=>101,'FlagString'=>"参数有误");
	//	}
	//	if($role_type > 0){
	//		$where = " AND role_type={$role_type}";
	//	}
	//	$sql = "SELECT * FROM ".DB_NAME_IM.".join_apply WHERE uid={$uin}".$where;
	//	$info = $this->db->get_row($sql,'ASSOC');
	//	if(!empty($info)){
	//		return array('Flag'=>100,'FlagString'=>'申请资料','Info'=>$info);
	//	}else{
	//		$sql = "SELECT * FROM ".DB_NAME_IM.".join_apply WHERE uid={$uin} AND apply_status = 1 LIMIT 1";
	//		$info = $this->db->get_row($sql,'ASSOC');
	//		return array('Flag'=>100,'FlagString'=>'申请资料','Info'=>$info);
	//	}
	//}
	
	//是否存在，审核状态
	public function checkApply($uin){
		if($uin <= 0){
			return array('Flag'=>101,'FlagString'=>"参数有误");
		}
		$sql = "SELECT id FROM ".DB_NAME_IM.".apply WHERE uin={$uin}";
		$id = $this->db->get_var($sql);
		//if(!empty($info)){
		//	if($info['status']==1){
		//		return array('Flag'=>102,'FlagString'=>'审核已通过，不能重复申请','Info'=>$info);
		//	}elseif($info['status']=='0'){
		//		return array('Flag'=>102,'FlagString'=>'您提交的申请正在审核中，不能再次申请','Info'=>$info);
		//	}
		//	return array('Flag'=>100,'FlagString'=>'申请资料','Info'=>$info);
		//}
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'没有申请资料');
		}
		return array('Flag'=>100,'FlagString'=>'申请资料');
	}
	
	//检查数据
	private function checkInfo($info){
		if(isset($info['phone']) && empty($info['phone'])){
			return array('Flag'=>101,'FlagString'=>'手机不能为空');
		}
		if($this->getUserType($info['phone'] != 'phone')){
			return array('Flag'=>101,'FlagString'=>'号码不正确');
		}
		if(strlen($info['idcard']) && strlen($info['idcard']) != 18){
			return array('Flag'=>101,'FlagString'=>'身份证不正确');
		}
		return array('Flag'=>100,'FlagString'=>'成功');
	}
	
	//获取用户名类型
	private function getUserType($user){
		if(preg_match('/^\w+@(\w+([._-][a-zA-Z]+))+$/',$user)) return 'email';
		if(preg_match('/^(13|15|18)\d{9}$/',$user)) return 'phone';
		return false;
	}
}
