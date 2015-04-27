<?php

class PassManager{
	private $db;
	
	public function __construct(){
        $this->db = domain::main()->GroupDBConn('mysql');
	}
	/*
	public function uins($uid){
		$uid = intval($uid);
		if($uid < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT s.uid,s.is_use,b.* FROM ".DB_NAME_IM.".sso_user_relate s LEFT JOIN basic_tbl b USING(uin) WHERE uid={$uid}";
		$uins = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'成功','Result'=>$uins);
	}

	//UIN向通行证ID的转化
	public function uin2uid($uin){
		if($uin < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT uid FROM ".DB_NAME_IM.".sso_user_relate WHERE uin={$uin}";
		$uid = $this->db->get_var($sql);
		if($uid > 0){
			return array('Flag'=>100,'FlagString'=>'成功','Uid'=>$uid);
		}
		return array('Flag'=>102,'FlagString'=>'失败');
	}

	//通行证信息
	public function info($uid){
		$uid = intval($uid);
		if($uid < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		$sql = "SELECT * FROM ".DB_NAME_IM.".username WHERE uid={$uid}";
		$row = $this->db->get_row($sql,ASSOC);
		if(!empty($row)){
			return array('Flag'=>100,'FlagString'=>'成功','Info'=>$row);
		}
		return array('Flag'=>102,'FlagString'=>'失败');
	}
	
	//通行证sso信息
	public function ssoInfo($uid){
		if($uid < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_IM.".sso_user_relate WHERE uin={$uid}";
		$row = $this->db->get_row($sql,ASSOC);
		if(!empty($row)){
			return array('Flag'=>100,'FlagString'=>'成功','Info'=>$row);
		}
		return array('Flag'=>102,'FlagString'=>'失败');
	}
	
	//通行证信息for uin
	public function infoForUin($uin){
		$uin=intval($uin);
		if($uin<1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$uinInfo=$this->uin2uid($uin);
		if($uinInfo['Flag']!=100){
			return $uinInfo;
		}
		$uid=$uinInfo['Uid'];
		return $this->info($uid);
	}*/

	//保存通行证信息
	public function savePassInfo($uin,$data){
		if(!empty($data['idcard'])){
			if(strlen($data['idcard'])==15 || strlen($data['idcard'])==18){
				$sql = "SELECT uin FROM ".DB_NAME_IM.".new_username WHERE idcard='{$data['idcard']}' AND uin!={$uin}";
				if($this->db->get_var($sql) > 0){
					return array('Flag'=>101,'FlagString'=>'身份证已被他人使用');
				}
			}
		}
		$updateString = '';
		foreach ((array)$data as $key => $value) {
			if($value == ''){
				continue;
			}
			$updateString .= "`{$key}`='{$value}',";
		}
		if(empty($updateString)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$updateString = rtrim($updateString,',');
		$sql = "UPDATE ".DB_NAME_IM.".new_username SET {$updateString} WHERE uin={$uin}";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'保存信息失败');
		}
		return array('Flag'=>100,'FlagString'=>'保存信息成功');
	}
	/*
	//得到特长
	public function getSpecialtyInfo($uid){
		$sql = "SELECT * FROM ".DB_NAME_IM.".specialty WHERE uid={$uid}";
		$info = $this->db->get_row($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'成功','Info'=>$info);
	}

	//保存特长
	public function saveSpecialty($uid,$data){
		if($uid < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		if(isset($data['specialty']) && count($data['specialty'])>0){
			if(count($data['specialty']) > 3){
				return array('Flag'=>102,'FlagString'=>'特长最多选三项');
			}
			if(!in_array(-1, $data['specialty'])){
				$other_specialty = '';
			}else{
				$other_specialty = addslashes(trim($data['other_specialty']));
			}
		}else{
			$data['specialty'] = '';
		}
		$specialty = json_encode($data['specialty']);

		if(isset($data['experience']) && count($data['experience'])>0){
			if(!in_array(-1, $data['experience'])){
				$other_experience = '';
			}else{
				$other_experience = addslashes(trim($data['other_experience']));
			}
		}else{
			$data['experience'] = '';
		}
		$experience = json_encode($data['experience']);

		$honor = addslashes($data['honor']);
		$self_evaluation = addslashes($data['self_evaluation']);
		if(isset($data['imgs']) && count($data['imgs'])>0){
			$imgs = json_encode($data['imgs']);
		}else{
			$imgs = '';
		}
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_IM.".specialty WHERE uid={$uid}";
		$count = $this->db->get_var($sql);
		if($count > 0){
			$sql = "UPDATE ".DB_NAME_IM.".specialty SET specialty='{$specialty}',other_specialty='{$other_specialty}',experience='{$experience}',other_experience='{$other_experience}',honor='{$honor}',self_evaluation='{$self_evaluation}',imgs='{$imgs}'";
		}else{
			$sql = "INSERT INTO ".DB_NAME_IM.".specialty(uid,specialty,other_specialty,experience,other_experience,honor,self_evaluation,imgs) VALUES({$uid},'{$specialty}','{$other_specialty}','{$experience}','{$other_experience}','{$honor}','{$self_evaluation}','{$imgs}')";
		}
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'保存失败');
		}
		return array('Flag'=>100,'FlagString'=>'保存成功');
	}

	//设为默认UIN
	public function setDefaultUin($uid,$uin){
		$uid = intval($uid);
		$uin = intval($uin);
		if($uid < 1 || $uin < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		//是否在uid下存在uin
		$sql = "SELECT uid FROM ".DB_NAME_IM.".sso_user_relate WHERE uid={$uid} AND uin={$uin}";
		$getUid = $this->db->get_var($sql);
		if($getUid != $uid){
			return array('Flag'=>102,'FlagString'=>'账号ID不存在');
		}

		$this->db->start_transaction();
		$sql = "UPDATE ".DB_NAME_IM.".sso_user_relate SET is_use=0 WHERE uid={$uid}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'设为默认账号失败');
		}
		$sql = "UPDATE ".DB_NAME_IM.".sso_user_relate SET is_use=1 WHERE uid={$uid} AND uin={$uin}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'设为默认账号失败');
		}
		$this->db->commit();
		//得到UIN下的信息
		$sql = "SELECT nick,gender FROM ".DB_NAME_IM.".basic_tbl WHERE uin={$uin}";
		$row = $this->db->get_row($sql,ASSOC);
		//得到mc中的登录信息
		$userInfo= httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
		//修改UIN的信息
		$userInfo['Uin'] = $uin;
		$userInfo['Nick'] = $row['nick'];
		$userInfo['Gender'] = $row['gender'];
		//保存到mc中
		httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'SetStorage','Userinfo'=>$userInfo)));

		return array('Flag'=>100,'FlagString'=>'设为默认账号成功');
	}*/
}