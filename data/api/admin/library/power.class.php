<?php

class power {

	private $db = null;

	public function __construct() {
		$this->db = db::connect(config('database','default'));
	}

	public function __destruct() {
		unset ($this->db);
	}

	public function checkUserPower($array,$is_return = false){
		$uin = $array['Uin'];
		if($uin > 0 && $array['BigCaseId'] > 0 && $array['CaseId'] > 0 && $array['ParentId'] > 0 && $array['ChildId'] > 0){
			$tmp = $this->getlevel($uin);
			if(!empty($tmp)){
				$levels = json_decode($tmp['levels'],true);
				$gid = $tmp['gid'];
				$Group_name = $tmp['group_name'];
				$Admin_name = $tmp['passname'];
				if($array['BigCaseId'] == 10002){
					if($levels[$array['CaseId']][$array['ParentId']][$array['ChildId']] > 0){
						$level_array = $is_return == true ? $levels : '';
						return array('Flag'=>100,'FlagString'=>'成功','Level_array'=>$level_array,'Gid'=>$gid,'Group_name'=>$Group_name,'Admin_name'=>$Admin_name);
					}
					elseif($gid == 1){
						$level_array = $is_return == true ? $levels : '';
						return array('Flag'=>100,'FlagString'=>'成功','Level_array'=>$level_array,'Gid'=>$gid,'Group_name'=>$Group_name,'Admin_name'=>$Admin_name);
					}
					return array('Flag'=>101,'FlagString'=>'未授权页面,无法访问!');
				}
				return array('Flag'=>101,'FlagString'=>'非法请求,访问失败!');
			}
			return array('Flag'=>101,'FlagString'=>'未绑定用户,不能访问后台系统!');
		}
		return array('Flag'=>101,'FlagString'=>'参数有误!');
	}
	
	public function getAdminLeftMenu($array){
		$uin = $array['Uin'];
		if($uin > 0){
			$userpower = $this->checkUserPower($array,true);
			if($userpower['Flag'] == 100){
				$param = array(
					'extparam' => array('Tag'=>'GetAdminLeftMenu','Levels'=>json_encode($userpower['Level_array']),'Gid'=>$userpower['Gid'])
				);
				$level_list = httpPOST(CCS_API_PATH,$param);
				$level_list['Uin'] = $uin;
				$level_list['Nick'] = $array['Nick'];
				$level_list['Group_name'] = $userpower['Group_name'];
			} else {
				$level_list = $userpower;
			}
		} else {
			$level_list = array('Flag'=>101,'FlagString'=>'参数有误');
		}
		return $level_list;
	}
	
	public function GetLevelLink($array){
		$uin = $array['Uin'];
		if($uin > 0 ){
			$userpower = $this->checkUserPower($array,true);
			if($userpower['Flag'] == 100){
				$level[$array['CaseId']][$array['ParentId']] = $userpower['Level_array'][$array['CaseId']][$array['ParentId']];
				$param = array(
					'param' => array('BigCaseId'=>10002,'CaseId'=>$array['CaseId'],'ParentId'=>$array['ParentId']),
					'extparam' => array('Tag'=>'GetAdminLeftMenu','Levels'=>json_encode($level),'Gid'=>$userpower['Gid'],'Menu'=>'1')
				);
				$level_list = httpPOST(CCS_API_PATH,$param);
				$level_child = $level_list['Result'][0]['parent'][0]['child'];
				$level_array = array();
				foreach((array)$level_child as $key=>$value){
					$level_array[$value['child_id']]['child_name'] = $value['child_name'];
					$level_array[$value['child_id']]['child_id'] = $value['child_id'];
				}
				$level_array = array('Flag'=>10010,'FlagString'=>'成功','Result'=>$level_array);
			} else {
				$level_array = $userpower;
			}
		} else {
			$level_array = array('Flag'=>101,'FlagString'=>'参数有误');
		}
		return $level_array;
	}
	
	private function getlevel($uin){
		return $this->db->get_row('SELECT u.passid ,u.gid, g.levels,g.group_name,u.passname FROM '.DB_NAME_ADMIN.'.tbl_user u ,'.DB_NAME_ADMIN.'.tbl_cluster c ,'.DB_NAME_ADMIN.'.tbl_group g WHERE u.passid = '.$uin.' AND  c.status =1 AND g.status =1 AND u.status=1 AND u.cid = c.id AND u.gid = g.id','ASSOC');
	}
}
?>