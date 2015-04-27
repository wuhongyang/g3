<?php

class admin {

	private $db = null;

	public function __construct() {
		$this->db = db::connect(config('database','default'));
	}

	public function __destruct() {
		unset ($this->db);
	}

	public function clusterCreate($array){
		$cluster_name	= $array['Cluster'];
		$status			= $array['Status'];
		$Cid 			= $array['Cid'];
		if($cluster_name != '' && $status >=0){
			if(empty($Cid)){
				$sql = 'SELECT id FROM '.DB_NAME_ADMIN.'.tbl_cluster WHERE cluster_name = "'.$cluster_name.'"';
				$var = $this->db->get_var($sql);
				if($var){
					return array('Flag'=>'101','url'=>'admin.php?module=cluster_create','FlagString'=>'[提示]：权限组分类已经存在，请重新命名!');
				}else{
					$sql = "INSERT INTO ".DB_NAME_ADMIN.".tbl_cluster (cluster_name , `status` ,uptime ) VALUES ('$cluster_name' , $status,".time().")";
					if($this->db->query($sql)){
						return array('Flag'=>'100','url'=>'admin.php?module=cluster_list','FlagString'=>'[提示]：创建权限组分类，操作成功!');
					}else{
						return array('Flag'=>'102','url'=>'admin.php?module=cluster_create','FlagString'=>'[提示]：创建权限组分类，操作失败!');
					}
				}
			}elseif($Cid > 0){
				$var = $this->clusterInfo(array('Cid'=>$Cid));
				if(empty($var)){
					return array('Flag'=>'103','url'=>'admin.php?module=cluster_editor&cid='.$Cid,'FlagString'=>'[提示]：权限组分类不存在，操作失败!');
				}else{
					$sql = "UPDATE ".DB_NAME_ADMIN.".tbl_cluster SET cluster_name = '$cluster_name' , status= $status,uptime = ".time()." WHERE id = $Cid";
					if($this->db->query($sql)){
						return array('Flag'=>'100','url'=>'admin.php?module=cluster_list','FlagString'=>'[提示]：编辑权限组分类，操作成功!');
					}else{
						return array('Flag'=>'105','url'=>'admin.php?module=cluster_editor&cid='.$Cid,'FlagString'=>'[提示]：编辑权限组分类，操作失败!');
					}
				}
			}
		}
	}
	
	public function clusterInfo($array){
		$cid = $array['Cid'];
		if($cid >0){
			return $this->db->get_row('SELECT cluster_name ,`status` FROM '.DB_NAME_ADMIN.'.tbl_cluster WHERE id = '.$cid,'ASSOC');
		}else{
			return $this->db->get_results('SELECT cluster_name ,`id` FROM '.DB_NAME_ADMIN.'.tbl_cluster WHERE `status` = 1','ASSOC');
		}
	}
	
	public function clusterList($array){
		$cluster = $array['Cluster'];
		$status = $array['Status']-1;
		$where = '';
		if(!empty($cluster)){
			$where .= ' AND cluster_name like "'.$cluster.'%"';
		}
		if($status >= 0){
			$where .= ' AND status = '.$status;
		}
		if(!empty($where)){
			$where = ' WHERE '.ltrim($where,' AND ');
		}
		$total = $this->db->get_var('SELECT COUNT(id) FROM '.DB_NAME_ADMIN.'.tbl_cluster '.$where.';');
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = 'SELECT * FROM '.DB_NAME_ADMIN.'.tbl_cluster '.$where.' ORDER BY id ASC LIMIT '.$page_arr['limit'].';';
			$list = $this->db->get_results($sql,'ASSOC');
			foreach($list as $key =>$v){
				$list[$key]['uptime'] = date('Y-m-d', $v['uptime']);
			}
    		$list['page'] = $page_arr['page'];
		}
		return $list;
	}
	
	public function groupConfig($array,$param){
		$id = $array['Id'];
		$uin = $param['Uin'];
		if($uin > 0){
			$login_info = $this->db->get_row('SELECT g.id,g.cid,g.group_name,g.levels FROM '.DB_NAME_ADMIN.'.tbl_group AS g,'.DB_NAME_ADMIN.'.tbl_user AS u WHERE g.id = u.gid AND u.passid = '.$uin ,'ASSOC');
			if($login_info['id'] == $id){
				$list['group_info'] = $login_info;
			}elseif($id > 0){
				$list['group_info'] = $this->db->get_row('SELECT id,cid,group_name,levels FROM '.DB_NAME_ADMIN.'.tbl_group WHERE id = '.$id ,'ASSOC');
			}else{
				$list['group_info']['levels'] = $login_info['levels'];
			}
			$sql = 'SELECT id,cluster_name FROM '.DB_NAME_ADMIN.'.tbl_cluster WHERE `status` = 1';
			$list['cluster'] = $this->db->get_results($sql,'ASSOC');
			$param = array(
				'extparam' => array('Tag'=>'GetAdminLeftMenu','Levels'=>$login_info['levels'],'Gid'=>$login_info['id'])
			);
			$level_list = httpPOST(CCS_API_PATH,$param);
			$list['login_info'] = $level_list['Result'];//当前登陆账号所拥有的权限
			$list['group_info']['levels'] = json_decode($list['group_info']['levels'],true);//当前权限组权限
		}
		return $list;
	}
	
	public function groupList($array,$param){
		$Group	= $array['Group'];
		$status = $array['Status']-1;
		$cid	= $array['Cid'];
		$uin = $param['Uin'];
		$where = '';
		if(!empty($Group)){
			$where .= ' AND g.group_name like "'.$Group.'%"';
		}
		if($status >= 0){
			$where .= ' AND g.`status` = '.$status;
		}
		if($cid > 0){
			$where .= ' AND g.`cid` = '.$cid;
		}
		$total = $this->db->get_var('SELECT COUNT(c.id) FROM '.DB_NAME_ADMIN.'.tbl_group g ,'.DB_NAME_ADMIN.'.tbl_cluster c WHERE c.id = g.cid'.$where.';');
		if($total > 0 ){
			$page_arr = $this->showPage($total);
			$sql = 'SELECT g.*,c.cluster_name FROM '.DB_NAME_ADMIN.'.tbl_group g ,'.DB_NAME_ADMIN.'.tbl_cluster c WHERE c.id = g.cid'.$where.' ORDER BY id ASC LIMIT '.$page_arr['limit'].';';
			$list = $this->db->get_results($sql,'ASSOC');
			foreach($list as $key =>$v){
				$list[$key]['uptime'] = date('Y-m-d', $v['uptime']);
			}
    		$list['page'] = $page_arr['page'];
		}
		return $list;
	}
	
	public function groupCreate($array){
		if($array['CId'] >0 && !empty($array['Group_name'])){
			$gid = $array['GId'];
			$levels = explode(',',$array['Levels']);
			foreach($levels as $key=>$value){
				$level = explode('_',$value);
				$level_array[$level[0]][$level[1]][$level[2]] = $level[2];
			}
			$levels = json_encode($level_array);
			if(empty($gid)){
				$sql = 'SELECT id FROM '.DB_NAME_ADMIN.'.tbl_cluster WHERE id = '.$array['CId'].' AND `status` = 1';
				$var = $this->db->get_var($sql,'ASSOC');
				if(empty($var))
					return array('Flag'=>'101','url'=>'admin.php?module=group_create','FlagString'=>'[提示]：权限组分类不存在，操作失败!');

				$group = $this->db->get_var('SELECT * FROM '.DB_NAME_ADMIN.'.tbl_group WHERE `group_name` = "'.$array['Group_name'].'"');
				if(!empty($group))
					return array('Flag'=>'102','url'=>'admin.php?module=group_create','FlagString'=>'[提示]：权限组名称已存在，操作失败!');

				$sql = 'INSERT INTO '.DB_NAME_ADMIN.'.tbl_group (cid , group_name , levels , uptime ) VALUES ('.$array['CId'].',"'.$array['Group_name'].'" , \''.$levels.'\','.time().')';
			}else{
				$var = $this->db->get_var('SELECT id FROM '.DB_NAME_ADMIN.'.tbl_group WHERE id = '.$gid);
				if(empty($var))
					return array('Flag'=>'103','url'=>'admin.php?module=group_create&id='.$gid,'FlagString'=>'[提示]：权限组名称不存在，操作失败!');

				$sql = 'SELECT id FROM '.DB_NAME_ADMIN.'.tbl_cluster WHERE id = '.$array['CId'].' AND `status` = 1';
				$var = $this->db->get_var($sql,'ASSOC');
				if(empty($var))
					return array('Flag'=>'101','url'=>'admin.php?module=group_create&id='.$gid,'FlagString'=>'[提示]：权限组分类不存在，操作失败!');

				$sql = 'UPDATE '.DB_NAME_ADMIN.'.tbl_group SET group_name = "'.$array['Group_name'].'" , cid ='.$array['CId'].' , levels = \''.$levels.'\',uptime = '.time().' WHERE id = '.$gid;
			}
			if($this->db->query($sql))
				return array('Flag'=>'100','url'=>'admin.php?module=group_list','FlagString'=>'[提示]：设置权限，操作成功!');
		}
		return array('Flag'=>'104','url'=>'admin.php?module=group_list','FlagString'=>'[提示]：操作失败!');
	}
	
	public function gruopSelect($array){
		return $this->db->get_results('SELECT id, group_name AS name FROM '.DB_NAME_ADMIN.'.tbl_group WHERE cid = '.$array['Cid'],'ASSOC');
	}
	
	public function userCreate($array){
		$passid = $array['Passid'];
		$passname = $array['Passname'];
		$cid = $array['Cid'];
		$gid = $array['Gid'];
		$status = $array['Status'];
		$pid = $array['Pid'];
		if($passid >0 && $cid >0 && $status>=0 && !empty($passname)){
			$result = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$passid)));
			if($result['Flag'] !== 100) return array('Flag'=>'102','FlagString'=>'[提示]：用户不存在!');
			if(empty($pid)){
				$var = $this->db->get_var('SELECT id FROM '.DB_NAME_ADMIN.'.tbl_user WHERE passid = '.$passid);
				if(!empty($var))
					return array('Flag'=>'101','url'=>'level.php?module=user_create','FlagString'=>'[提示]：用户已经存在，请重新输入!');
				$data = array('group_id'=>10000);
				$rst = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'EditPassport','Data'=>$data,'Uin'=>$passid)));
				if($rst['Flag'] !=100) return array('Flag'=>'102','url'=>'level.php?module=user_list','FlagString'=>'[提示]：添加失败!');
					
				$sql = 'INSERT INTO '.DB_NAME_ADMIN.'.tbl_user (passid ,passname , cid , gid,status ,uptime) VALUES ('.$passid.',"'.$passname.'",'.$cid.','.$gid.','.$status.','.time().')';
			}else{
				$var = $this->db->get_var('SELECT id FROM '.DB_NAME_ADMIN.'.tbl_user WHERE id = '.$pid);
				if(empty($var))
					return array('Flag'=>'103','url'=>'level.php?module=user_editor&pid='.$pid,'FlagString'=>'[提示]：用户不存在，操作失败!');
				$sql = 'UPDATE '.DB_NAME_ADMIN.'.tbl_user SET passid='.$passid.',passname="'.$passname.'",cid='.$cid.',gid='.$gid.',status='.$status.' WHERE id='.$pid;
			}
			if($this->db->query($sql))
				return array('Flag'=>'100','url'=>'level.php?module=user_list','FlagString'=>'[提示]：操作成功!');
			
			return array('Flag'=>'102','url'=>'level.php?module=user_list','FlagString'=>'[提示]：操作失败!');
		}
		return array('Flag'=>'102','url'=>'level.php?module=user_list','FlagString'=>'[提示]：参数有误!');
	}
	
	public function userinfo($array){
		$pid = $array['Pid'];
		if($pid > 0){
			return $this->db->get_row('SELECT * FROM '.DB_NAME_ADMIN.'.tbl_user WHERE id = '.$pid,'ASSOC');
		}
	}
	
	public function userList($array) {
		$Group	= $array['Group'];
		$status = $array['Status']-1;
		$cid	= $array['Cid'];
		$where = '';
		if(!empty($Group)){
			$where .= ' AND g.group_name like "'.$Group.'%"';
		}
		if($status >= 0){
			$where .= ' AND u.`status` = '.$status;
		}
		if($cid > 0){
			$where .= ' AND u.`cid` = '.$cid;
		}
		$total = $this->db->get_var('SELECT COUNT(u.id) FROM '.DB_NAME_ADMIN.'.tbl_user u,'.DB_NAME_ADMIN.'.tbl_group g WHERE g.id=u.gid'.$where.';');
		if($total > 0 ){
			$page_arr = $this->showPage($total);
			$sql = 'SELECT u.* ,g.group_name ,c.cluster_name FROM '.DB_NAME_ADMIN.'.tbl_user u ,'.DB_NAME_ADMIN.'.tbl_group g ,'.DB_NAME_ADMIN.'.tbl_cluster c WHERE g.id = u.gid AND c.id = u.cid'.$where.' ORDER BY id ASC LIMIT '.$page_arr['limit'].';';
			$list = $this->db->get_results($sql,'ASSOC');
			foreach($list as $key =>$v){
				$list[$key]['uptime'] = date('Y-m-d', $v['uptime']);
			}
    		$list['page'] = $page_arr['page'];
		}
		return $list;
	}
	
	private function showPage($total, $perpage = 20) {
		if ($total > 0) {
			$page = new extpage(array (
				'total' => $total,
				'perpage' => $perpage
			));
			$pageArr['page'] = $page->show();
			$pageArr['limit'] = $page->limit();
			unset ($page);
		}
		return $pageArr;
	}
}
?>