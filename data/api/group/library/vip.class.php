<?php

/**
 *   代理账户操作接口
 *   文件: vip.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class vip
{
	//数据库指针
	protected $db = null;
	protected $mongo = null;
	private static $instance;
	
	//获取单例对象
    function instance(){
        if(!is_object(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	//构造函数
	public function __construct() {
        $this->db = domain::main()->GroupDBConn('mysql');
        $this->mongo = domain::main()->GroupDBConn('mongo');
	}
	
	public function vipList($info){
		$where = " b.group_id = {$info['GroupId']}";
		if($info['Uin'] > 0){
			$where .= ' AND b.uin = '.$info['Uin'];
		}
		if($info['province'] > 0){
			$where .= ' AND b.province = '.$info['province'];
		}
		if($info['city'] > 0){
			$where .= ' AND b.city = '.$info['city'];
		}
		$sql="SELECT COUNT(*) FROM ".DB_NAME_IM.".basic_tbl b WHERE ".$where;
		$count=$this->db->get_var($sql);
		if($count<=0){
			return array('Flag'=>100,'FlagString'=>'没有数据');
		}

		$pageArr=$this->showPage($count);
		
		//$sql="SELECT * FROM ".DB_NAME_GROUP.".tbl_vip WHERE ".$where." ORDER BY id DESC LIMIT ".$pageArr['limit'];
		$sql = "SELECT DISTINCT(b.uin),b.nick,b.gender,b.province,b.city,b.name,b.phone,n.uptime FROM ".DB_NAME_IM.".basic_tbl AS b LEFT JOIN ".DB_NAME_IM.".new_username AS n USING(uin)  WHERE ".$where." ORDER BY n.uptime DESC LIMIT ".$pageArr['limit'];
		$list=$this->db->get_results($sql,'ASSOC');
		$uin_arr = array();
		foreach($list as $key=>$value){
			if($value['province'] > 0){
				$pName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetProvinceName','ProvinceId'=>$value['province'])));
				$list[$key]['province'] = $pName['provinceName'];
			}else{
				$list[$key]['province'] = '-';
			}
			if($value['city'] > 0){
				$cName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCityName','CityId'=>$value['city'])));
				$list[$key]['city'] = $cName['cityName'];
			}else{
				$list[$key]['city'] = '-';
			}
			if($value['phone'] <1){
				$list[$key]['phone'] = '--';
			}
			$uin_arr[] = $value['uin'];
		}
		
		$sql = "SELECT DISTINCT uin,`state` FROM ".DB_NAME_IM.".new_username WHERE uin IN (".join(",", $uin_arr).")";
		$state_temp_arr = $this->db->get_results($sql, "ASSOC");
		$state_arr = array();
		foreach($state_temp_arr as $one){
			$state_arr[$one['uin']] = $one['state'];
		}
		foreach($list as $key=>$value){
			$list[$key]['state'] = $state_arr[$value['uin']];
		}
		
		return array('Flag'=>100,'FlagString'=>'会员列表','List'=>$list,'Page'=>$pageArr['page'],'total'=>$count);
	}
	
	function setState($param, $json){
		$uin = $param['TargetUin'];
		$group_id = $param['GroupId'];
		$sql = "SELECT `state` FROM ".DB_NAME_IM.".new_username WHERE uin = ".$uin;
		$state = $this->db->get_var($sql);
		if($state == 1){
			$new_state = 0;
			$param['Desc'] = "冻结通行证";
		}else{
			$new_state = 1;
			$param['Desc'] = "解冻通行证";
		}
		$sql = "UPDATE ".DB_NAME_IM.".new_username SET state = '".$new_state."' WHERE uin = ".$uin;
		$done = $this->db->query($sql);
		
		$log = getLogData($param,$json);
		$logData[] = $log;
		if($done){
			return array("Flag"=>100, "FlagString"=>"操作成功", "LogData"=>$logData);
		}else{
			return array("Flag"=>102, "FlagString"=>"操作失败", "LogData"=>$logData);
		}
	}
	
	function editPass($message, $group_id){
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_IM.".`basic_tbl` WHERE uin = ".$message['uin']." AND group_id = ".$group_id;
		$exist = $this->db->get_var($sql);
		if(!$exist){
			return array("Flag"=>102, "FlagString"=>"本站下无改会员");
		}
		$result = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'EditPassword','Uid'=>$message['uin'],'Pass'=>$message['pass'])));
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

	public function vipInfo($info){
		if($info['Uin'] <1 ||  $info['GroupId'] < 1 ) return array('Flag'=>101,'FlagString'=>'参数有误');
		$sql = "SELECT uin FROM ".DB_NAME_IM.".basic_tbl WHERE group_id={$info['GroupId']} AND uin={$info['Uin']}";
		$uin = $this->db->get_var($sql);
		if($uin != $info['Uin']){
			return array('Flag'=>102,'FlagString'=>'无效参数');
		}
		return httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$uin)));
	}
	
	public function vipDel($info){
		if($info['Uin'] <1 ||  $info['GroupId'] < 1 ) return array('Flag'=>101,'FlagString'=>'参数有误');
		$row = $this->vipInfo($info);
		if($row['Flag'] != 100) return $row;
		$sql = "DELETE FROM ".DB_NAME_GROUP.".tbl_vip WHERE  uin = {$info['Uin']} AND group_id = {$info['GroupId']} LIMIT 1";
		if($this->db->query($sql)){
			//删除角色
			$roleData=array(
				'extparam'=>array(
					'Tag'=>'DeleteGroupRole',
					'GroupId'=>$info['GroupId'],
					'Uin'=>$info['Uin'],
					'RoleId'=>array(10126)
				)
			);
			$res=httpPOST(ROLE_API_PATH,$roleData);
			if($res['Flag']!=100){
				return array('Flag'=>101,'FlagString'=>'失败啦');
			}
			return array('Flag'=>100,'FlagString'=>'成功');
		}
		return array('Flag'=>101,'FlagString'=>'失败啦');
	}
	
	public function vipUpdate($info){
		$row = $this->vipInfo($info);
		if($row['Flag'] != 100) return $row;
		if(!empty($info['check_desc'])){
			$set = ' ,check_desc = "'.$info['check_desc'].'"';
		}
		$sql = "UPDATE ".DB_NAME_GROUP.".tbl_vip SET name = '{$info['name']}', sex = '{$info['sex']}', age = '{$info['age']}', phone = '{$info['phone']}', qq = '{$info['qq']}',province='{$info['province']}',city='{$info['city']}',`status`='{$info['status']}' {$set} WHERE uin = {$info['Uin']} AND group_id = {$info['GroupId']} LIMIT 1";
		if($this->db->query($sql)){
			if($info['status'] == 1){
				//添加角色
				$roleData=array(
					'extparam'=>array(
						'Tag'=>'AddGroupRole',
						'GroupId'=>$info['GroupId'],
						'Uin'=>$info['Uin'],
						'RoleId'=>10126,
						'RoomId'=>0
					)
				);
				$res=httpPOST(ROLE_API_PATH,$roleData);
				if($res['Flag']!=100){
					return array('Flag'=>101,'FlagString'=>$res['FlagString']);
				}
				$this->setMessage($info['Uin'],'您已成为站"'.$info['GroupId'].'"的站内会员,在该站下所有房间内享有尊贵特权!');
			}else if($info['status']){
				//删除角色
				$roleData=array(
					'extparam'=>array(
						'Tag'=>'DeleteGroupRole',
						'GroupId'=>$info['GroupId'],
						'Uin'=>$info['Uin'],
						'RoleId'=>array(10126)
					)
				);
				$res=httpPOST(ROLE_API_PATH,$roleData);
				if($res['Flag']!=100){
					return array('Flag'=>101,'FlagString'=>'失败啦');
				}
				$this->setMessage($info['Uin'],'很抱歉，您在站"'.$info['GroupId'].'"的会员申请未能通过。原因如下：'.$info['check_desc']);
			}
			return array('Flag'=>100,'FlagString'=>'成功');
		}
		return array('Flag'=>101,'FlagString'=>'失败啦');
	}
	
	public function vipSet($info){
		$this->platform_db = db::connect(config('database','default'));
		if($info['Set_vip'] <1 ||  $info['GroupId'] < 1 ||  $info['Uin'] < 1){
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
		$sql = "UPDATE ".DB_NAME_GROUP.".`tbl_groups` SET vip_set ={$info['Set_vip']} WHERE uin ={$info['Uin']} AND groupid = {$info['GroupId']} LIMIT 1";
		if($this->platform_db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'成功');
		}
		return array('Flag'=>101,'FlagString'=>'失败啦');
	}
/*
	public function vipRank(){
		$sql = "SELECT COUNT(*) AS vip_nums,v.group_id,g.name FROM ".DB_NAME_GROUP.".tbl_vip v INNER JOIN ".DB_NAME_GROUP.".tbl_groups g ON(v.`group_id`=g.`groupid`) GROUP BY v.group_id ORDER BY vip_nums DESC LIMIT 10";
		$ranks = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'成功','Rank'=>$ranks);
	}
*/	
	private function setMessage($uin,$content){
		$hm = new handleMatter();
		$handleData=array(
			'uin'=>$uin,
			'content'=>$content,
			'link'=>'',
			'link_name'=>''
		);
		$hm->add($handleData);
	}
	
	private function showPage($total, $perpage = 20) {
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
