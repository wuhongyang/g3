<?php
class help
{
	private $db;

	function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	function classify_list($arr){
		$status = $arr['Status'];
		$id = $arr['Id'];
		$type = $arr['Type'];
		if((!in_array($status, array(0, 1)) && !$id)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		if($type){
			$extra =  " AND type = ".$type;
		}
		if($id && $status != ""){
			$sql = "SELECT * FROM `".DB_NAME_HELP."`.`help_classify` WHERE id ='".$id."' AND status= '".$status."'";
		}elseif($id){
			$sql = "SELECT * FROM `".DB_NAME_HELP."`.`help_classify` WHERE id ='".$id."'";
		}else{
			$sql = "SELECT * FROM `".DB_NAME_HELP."`.`help_classify` WHERE status = '".$status."'";
		}
		$result = $this->db->get_results($sql.$extra, "ASSOC");
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$result);
	}
	
	function substance_list($arr){
		$classify_id = $arr['ClassifyId'];
		$id = $arr['Id'];
		$type = $arr['Type'];
		$limit = $arr['Limit'];
		if($id>0){
			$preid = $id-1;
			$nextid = $id+1;
			$sql = "SELECT * FROM `".DB_NAME_HELP."`.`help_substance` WHERE id={$id}";
			$read = $this->db->get_row($sql, "ASSOC");
			if(empty($read)) return array('Flag'=>101,'FlagString'=>'文章不存在');
			$sql = "SELECT id,classify_id,title,update_time,top FROM `".DB_NAME_HELP."`.`help_substance` WHERE id IN($preid,$nextid)";
			$data = $this->db->get_results($sql, "ASSOC");
			if($data[0]['id'] == $nextid){
				$prev = array();
				$next = $data[0];
			}else{
				$prev = $data[0];
				$next = $data[1];
			}
			return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$read,'Prev'=>$prev,'Next'=>$next);
		}elseif($classify_id){
			$sql = "SELECT COUNT(*) FROM `".DB_NAME_HELP."`.`help_substance`  WHERE classify_id = '".$classify_id."'";
			$total = $this->db->get_var($sql);
			$result = array();
			if($total > 0){
				$page_arr = $this->showpage($total);
				if(empty($limit)) $limit = $page_arr['limit'];
				$select = isset($arr['All'])? '*' : ' id,classify_id,title,update_time,top';
				$sql = "SELECT {$select} FROM `".DB_NAME_HELP."`.`help_substance` WHERE classify_id = '".$classify_id."' ORDER BY top DESC,update_time DESC LIMIT ".$limit;
				$data = $this->db->get_results($sql, "ASSOC");
			}
		}elseif($type && $limit){
			$sql = 'SELECT COUNT(*) FROM `'.DB_NAME_HELP.'`.`help_classify` a LEFT JOIN `'.DB_NAME_HELP.'`.`help_substance` b ON a.`id` = b.`classify_id` WHERE a.`type` = "'.$type.'" AND b.`id` != "" AND a.`status` = 1';
			$total = $this->db->get_var($sql);
			if($total > 0){
				$sql = 'SELECT * FROM `'.DB_NAME_HELP.'`.`help_classify` a LEFT JOIN `'.DB_NAME_HELP.'`.`help_substance` b ON a.`id` = b.`classify_id` WHERE a.`type` = "'.$type.'" AND b.`id` != "" AND a.`status` = 1 ORDER BY b.top DESC,b.`update_time` DESC LIMIT '.$limit;
				$data = $this->db->get_results($sql, "ASSOC");
			}
		}elseif($type){
			$sql = 'SELECT COUNT(*) FROM `'.DB_NAME_HELP.'`.`help_classify` a LEFT JOIN `'.DB_NAME_HELP.'`.`help_substance` b ON a.`id` = b.`classify_id` WHERE a.`type` = "'.$type.'" AND b.`id` != "" AND a.`status` = 1';
			$total = $this->db->get_var($sql);
			$data = array();
			if($total > 0){
				$page_arr = $this->showpage($total);
				$sql = 'SELECT * FROM `'.DB_NAME_HELP.'`.`help_classify` a LEFT JOIN `'.DB_NAME_HELP.'`.`help_substance` b ON a.`id` = b.`classify_id` WHERE a.`type` = "'.$type.'" AND b.`id` != "" ORDER BY b.`update_time` DESC  LIMIT '.$page_arr['limit'];
				$data = $this->db->get_results($sql, "ASSOC");
			}
		}else{
			return array('Flag'=>101, 'FlagString'=>'参数错误');
		}
		$curtime = time();
		foreach($data as $key=>$one_data){
			$one_data['new'] = ($curtime-$one_data['update_time'] < 86400)? 1 : 0;
			$result[$key] = $one_data;
		}
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$result, 'Page'=>$page_arr['page']);
	}
	
	function get_link_list($data){
		$id = $data['Id'];
		if($id){
			$sql = "SELECT * FROM `".DB_NAME_HELP."`.`friendly_link` WHERE id = ".$id;
			$data = $this->db->get_row($sql, "ASSOC");
			return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>(array)$data);
		}
		$limit = $data['Limit']?$data['Limit']:16;
		if($limit > 0)
			$sql = "SELECT * FROM `".DB_NAME_HELP."`.`friendly_link` WHERE 1 ORDER BY `order` ASC LIMIT ".$limit;
		else
			$sql = "SELECT * FROM `".DB_NAME_HELP."`.`friendly_link` WHERE 1 ORDER BY `order` ASC";
		$data = $this->db->get_results($sql, "ASSOC");
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>(array)$data);
	}
	
	private function showPage($total, $perpage = 20) {
		if ($total > 0) {
			require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/library/page.class.php');
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