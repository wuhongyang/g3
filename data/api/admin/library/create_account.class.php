<?php
class createAccount{
	
	function create_single($uin){
		$exist = $this->uin_exist($uin);
		if($exist['Flag'] != 100){
			return $exist;
		}
		
		$range = $this->uin_range($uin);
		if($range['Flag'] != 100){
			return $range;
		}
		
		$p = array('extparam'=>array(
				'Tag'=>'RegisterUin',
				'Uin'=>$uin,
				'Nick'=>'user_'.$uin,
				'Age'=>1,
				'Sex'=>1,
			)
		);
		$info = httpPOST(SSO_API_PATH, $p);
		if($info['Flag'] == 100){
			$info['FlagString'] = "申请成功,您申请的ID为: ".$info['Uin'];
		}else{
			$info['FlagString'] = "申请失败";
		}
		return $info;
	}
	
	function create_group($range){
		$interrupt = false;
		$range_end = $this->uin_range($range[1]);
		if($range_end['Flag'] != 100){
			return $range_end;
		}
		
		for($uin=$range[0];$uin<=$range[1];$uin++){
			$exist = $this->uin_exist($uin);
			if($exist){
				$info = $this->create_single($uin);
				if($info['Flag'] != 100){
					$interrupt = true;
					break;
				}
			}else{
				$interrupt = true;
				break;
			}
		}
		if(!$interrupt){
			$result = array('Flag'=>100, 'FlagString'=>"创建号码段成功");
		}else{
			if($range[0] == $uin){
				$result = array('Flag'=>101, 'FlagString'=>"创建号码段中断,没有被创建");
			}else{
				$result = array('Flag'=>101, 'FlagString'=>"创建号码段中断,从{$range[0]}到{$uin}已被创建");
			}
		}
		return $result;
	}
	
	private function uin_exist($uin){
		$p = array('extparam'=>array(
				'Tag'=>'UinExist',
				'Uin'=>$uin,
			)
		);
		$info = httpPOST(SSO_API_PATH, $p);
		if($info['Flag']==102){//该号未被注册
			$info['Flag']=100;
			$info['FlagString']="该号未被注册";
		}elseif($info['Flag']==101){//该号已被注册且已绑定
			$info['Flag']=108;
			$info['FlagString']="该号已被注册且已绑定";
		}else{//该号已注册但未绑定
			$info['Flag']=107;
			$info['FlagString']="该号已注册但未绑定";
		}
		return $info;
	}
	
	//UIN是否小于现有注册用户最大UIN
	private function uin_range($uin){
		$p = array('extparam'=>array(
				'Tag'=>'GetLastUin'
			)
		);
		$last_uin = httpPOST(SSO_API_PATH, $p);
		if($last_uin['Flag'] != 100) return array('Flag'=>102,'FlagString'=>'无可用UIN');
		if($uin <= $last_uin['LastUin']){
			$result = array('Flag'=>100,'FlagString'=>'验证通过','Uin'=>$uin);
		}
		else{
			$result = array('Flag'=>101,'FlagString'=>'账号ID必须小于等于 '.$last_uin['LastUin'],'Uin'=>$uin);
		}
		
		return $result;
	}
}