<?php
class video{
	
	public function __construct(){
		$this->db = domain::main()->GroupDBConn('mysql');
	}
	
	function save($id, $name, $link, $pic_md5, $group_id, $uin){
        if(mb_strlen($name, "utf-8") > 20){
            return array("Flag"=>101, "FlagString"=>"名称不能超过20个字符");
        }
        if(!$name || !$link || !$pic_md5){
            return array("Flag"=>101, "FlagString"=>"名字，连接，图片不能为空");
        }
        
		if($id){
			return $this->update($id, $name, $link, $pic_md5, $group_id, $uin);
		}else{
			return $this->add($name, $link, $pic_md5, $group_id, $uin);
		}
	}
	
	private function add($name, $link, $pic_md5, $group_id, $uin){
		$sql 	= "SELECT COUNT(*) FROM ".DB_NAME_IM.".video WHERE group_id = ".$group_id." AND uin = ".$uin." AND `name` = '".$name."'";
		$exist 	= $this->db->get_var($sql);
		if($exist){
			return array("Flag"=>102, "FlagString"=>"已存在同名视频");
		}
		
		$sql	= "INSERT INTO ".DB_NAME_IM.".`video` (`name`, `link`, `group_id`, `uin`, `pic`) VALUES ('".$name."', '".$link."', '".$group_id."', '".$uin."', '".$pic_md5."'); ";
		$done	= $this->db->query($sql);
		if(!$done){
			return array("Flag"=>102, "FlagString"=>"添加失败");
		}
		
		return array("Flag"=>100, "FlagString"=>"添加成功");
	}
	
	private function update($id, $name, $link, $pic_md5, $user_group_id, $user_uin){
		$sql 					= "SELECT group_id,uin FROM ".DB_NAME_IM.".video WHERE id = ".$id;
		list($group_id, $uin)	= $this->db->get_row($sql);
		if($group_id != $user_group_id || $uin != $user_uin){
			return array("Flag"=>102, "FlagString"=>"更新错误");
		}
		
		$sql	= "UPDATE ".DB_NAME_IM.".`video` SET `name` = '".$name."' ,`link` = '".$link."', `pic` = '".$pic_md5."' WHERE `id` = '".$id."'; ";
		$done	= $this->db->query($sql);
		if(!$done){
			return array("Flag"=>102, "FlagString"=>"更新失败");
		}
		
		return array("Flag"=>100, "FlagString"=>"更新成功");
		
	}
	
	function video_list($id, $group_id, $uin){
	    if($id){
            $sql	= "SELECT id, `name`, link, pic FROM ".DB_NAME_IM.".video WHERE id = ".$id." AND group_id = ".$group_id." AND uin = ".$uin;
            $list	= $this->db->get_row($sql, "ASSOC");
	    }else{
            $sql	= "SELECT id, `name`, link, pic FROM ".DB_NAME_IM.".video WHERE group_id = ".$group_id." AND uin = ".$uin." ORDER BY id DESC";
            $list	= $this->db->get_results($sql, "ASSOC");   
	    }
		
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$list);
	}
	
	function del($id, $user_group_id, $user_uin){
		$sql 					= "SELECT group_id,uin FROM ".DB_NAME_IM.".video WHERE id = ".$id;
		list($group_id, $uin)	= $this->db->get_row($sql);
		if($group_id != $user_group_id || $uin != $user_uin){
			return array("Flag"=>102, "FlagString"=>"删除错误");
		}
		
		$sql	= "DELETE FROM ".DB_NAME_IM.".`video` WHERE `id` = '".$id."'; ";
		$done	= $this->db->query($sql);
		if(!$done){
			return array("Flag"=>102, "FlagString"=>"删除失败");
		}
		
		return array("Flag"=>100, "FlagString"=>"删除成功");
	}
}