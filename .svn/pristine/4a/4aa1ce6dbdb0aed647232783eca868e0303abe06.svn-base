<?php
/*
class Medal
{
	protected $db;
	public function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	public function EditMedalType($post){
		$sql = "REPLACE INTO ".DB_NAME_IM.".medal_type(`id`,`name`,`business_id`,`order`,`category`,`status`)VALUES('{$post['id']}','{$post['name']}','{$post['business_id']}','{$post['order']}','{$post['category']}','{$post['status']}');";
		if( ! $this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'操作失败');
		}else{
			return array('Flag'=>100,'FlagString'=>'操作成功');
		}
	}
	
	public function DeleteMedalType($id){
		$sql = "DELETE FROM ".DB_NAME_IM.".medal_type WHERE id={$id}";
		if( ! $this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'操作失败');
		}else{
            $sql = "DELETE FROM ".DB_NAME_IM.".medal_list WHERE typeid={$id}";
            $this->db->query($sql);
			return array('Flag'=>100,'FlagString'=>'操作成功');
		}
	}
	
	public function EditMedal($post){
		$sql = "REPLACE INTO ".DB_NAME_IM.".medal_list(`id`,`name`,`typeid`,`descr1`,`descr2`,`icontype`,`iconid`,`icon`,`listshow`,`status`)VALUES
				('{$post['id']}','{$post['name']}','{$post['typeid']}','{$post['descr1']}','{$post['descr2']}','{$post['icontype']}','{$post['iconid']}','{$post['icon']}','{$post['listshow']}','{$post['status']}');";
		if( ! $this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'操作失败');
		}else{
			return array('Flag'=>100,'FlagString'=>'操作成功');
		}
	}
	
	public function DeleteMedal($id){
		$sql = "DELETE FROM ".DB_NAME_IM.".medal_list WHERE id={$id}";
		if( ! $this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'操作失败');
		}else{
			return array('Flag'=>100,'FlagString'=>'操作成功');
		}
	}
	
	public function GetMedalType($id){
		$where = 1;
		if($id > 0){
			$where .= " AND id={$id}";
		}
		$sql = "SELECT * FROM ".DB_NAME_IM.".medal_type WHERE {$where} ORDER BY `order` DESC";
		$result = $this->db->get_results($sql);
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$result);
	}
	
	public function GetMedalList($id,$typeid){
		$where = 1;
		if($id > 0){
			$where .= " AND id={$id}";
		}elseif($typeid > 0){
            $where .= " AND typeid={$typeid}";
        }
		$sql = "SELECT * FROM ".DB_NAME_IM.".medal_list WHERE {$where} ORDER BY `id` ASC";
		$result = $this->db->get_results($sql);
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$result);
	}
	
}
*/