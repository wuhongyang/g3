<?php
class ImageManager{
	private $db;
	
	function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	function cate_list($tbl, $no_page = false){
		if($no_page){
			$sql = "SELECT * FROM ".DB_NAME_REGION.".".$tbl;
			$result = $this->db->get_results($sql, "ASSOC");
			return array("Flag"=>100, "List"=>$result);
		}else{
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_REGION.".".$tbl;
			$total = $this->db->get_var($sql);
			$page_arr = $this->showPage($total);
			$sql = "SELECT * FROM ".DB_NAME_REGION.".".$tbl." LIMIT ".$page_arr['limit'];
			$result = $this->db->get_results($sql, "ASSOC");
			return array("Flag"=>100, "List"=>$result, "Page"=>$page_arr['page']);
		}
	}
	
	function stamp_cate_get($parent_id){
		$sql = "SELECT * FROM ".DB_NAME_REGION.".`tbl_stamptype` WHERE parent_id = ".$parent_id;
		$row = $this->db->get_row($sql);
		return array("Flag"=>100, "Row"=>$row, "FlagString"=>"查询成功");
	}
	
	function expression_cate_get($parent_id){
		$sql = "SELECT * FROM ".DB_NAME_REGION.".`tbl_expressiontype` WHERE cate_id = ".$parent_id;
		$row = $this->db->get_row($sql);
		return array("Flag"=>100, "Row"=>$row, "FlagString"=>"查询成功");
	}
	
	function stamp_list($parent_id){
		$sql = "SELECT `name` FROM ".DB_NAME_REGION.".`tbl_stamptype` WHERE parent_id = ".$parent_id;
		$name = $this->db->get_var($sql, "ASSOC");
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_REGION.".`tbl_stamp` WHERE parent_id = ".$parent_id;
		$total = $this->db->get_var($sql);
		$page_arr = $this->showPage($total);
		$sql = "SELECT * FROM ".DB_NAME_REGION.".`tbl_stamp` WHERE parent_id = ".$parent_id." ORDER BY stamp_id LIMIT ".$page_arr['limit'];
		$res = $this->db->get_results($sql, "ASSOC");
		return array("Flag"=>100, "List"=>$res, "CateName"=>$name, "Page"=>$page_arr['page'], "FlagString"=>"查询成功");
	}
	
	function expression_list($cate_id){
		$sql = "SELECT `cate_name` FROM ".DB_NAME_REGION.".`tbl_expressiontype` WHERE cate_id = ".$cate_id;
		$name = $this->db->get_var($sql, "ASSOC");
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_REGION.".`tbl_expression` WHERE cate_id = ".$cate_id;
		$total = $this->db->get_var($sql);
		$page_arr = $this->showPage($total);
		$sql = "SELECT * FROM ".DB_NAME_REGION.".`tbl_expression` WHERE cate_id = ".$cate_id." ORDER BY id LIMIT ".$page_arr['limit'];
		$res = $this->db->get_results($sql, "ASSOC");
		return array("Flag"=>100, "List"=>$res, "CateName"=>$name, "Page"=>$page_arr['page'], "FlagString"=>"查询成功");
	}
	
	function stamp_get($stamp_id){
		$sql = "SELECT * FROM ".DB_NAME_REGION.".`tbl_stamp` WHERE stamp_id = ".$stamp_id;
		$row = $this->db->get_row($sql);
		return array("Flag"=>100, "Row"=>$row, "FlagString"=>"查询成功");
	}
	
	function expression_get($expression_id){
		$sql = "SELECT * FROM ".DB_NAME_REGION.".`tbl_expression` WHERE id = ".$expression_id;
		$row = $this->db->get_row($sql);
		return array("Flag"=>100, "Row"=>$row, "FlagString"=>"查询成功");
	}
	
	function stamp_save($stamp_id, $stamp_name, $stamp_img_path){
		if($stamp_img_path){
			$sql = "UPDATE ".DB_NAME_REGION.".`tbl_stamp` SET
			`stamp_name` = '".$stamp_name."' ,`stamp_img_path` = '".$stamp_img_path."' WHERE `stamp_id` = '".$stamp_id."'; ";
		}else{
			$sql = "UPDATE ".DB_NAME_REGION.".`tbl_stamp` SET
			`stamp_name` = '".$stamp_name."' WHERE `stamp_id` = '".$stamp_id."'; ";
		}
		if($this->db->query($sql)){
			return array("Flag"=>100, "FlagString"=>"更新成功");
		}else{
			return array("Flag"=>103, "FlagString"=>"更新失败,同一个类型印章不能有相同的名字");
		}
	}
	
	function expression_save($id, $name, $img_path){
		if($img_path){
			$sql = "UPDATE ".DB_NAME_REGION.".`tbl_expression` SET
			`img_name` = '".$name."' ,`img_path` = '".$img_path."' WHERE `id` = '".$id."'; ";
		}else{
			$sql = "UPDATE ".DB_NAME_REGION.".`tbl_expression` SET
			`img_name` = '".$name."' WHERE `id` = '".$id."'; ";
		}
		if($this->db->query($sql)){
			return array("Flag"=>100, "FlagString"=>"更新成功");
		}else{
			return array("Flag"=>103, "FlagString"=>"更新失败,同一个类型印章不能有相同的名字");
		}
	}
	
	function stamp_del($stamp_id){
		$sql = "DELETE FROM ".DB_NAME_REGION.".`tbl_stamp` WHERE `stamp_id` = '".$stamp_id."'; ";
		if($this->db->query($sql)){
			return array("Flag"=>100, "FlagString"=>"删除成功");
		}else{
			return array("Flag"=>103, "FlagString"=>"删除失败");
		}
	}
	
	function expression_del($id){
		$sql = "DELETE FROM ".DB_NAME_REGION.".`tbl_expression` WHERE `id` = '".$id."'; ";
		if($this->db->query($sql)){
			return array("Flag"=>100, "FlagString"=>"删除成功");
		}else{
			return array("Flag"=>103, "FlagString"=>"删除失败");
		}
	}
	
	function stamp_cate_save($data){
		if($data['raw_parent_id']){
			if($data['parent_id'] != $data['raw_parent_id']){
				$sql = "SELECT COUNT(*) FROM ".DB_NAME_REGION.".`tbl_stamptype` WHERE parent_id = ".$data['parent_id'];
				$exist = $this->db->get_var($sql);
				if($exist){
					return array("Flag"=>102, "FlagString"=>"已存在其他相同三级科目");
				}
			}
			$sql = "UPDATE ".DB_NAME_REGION.".`tbl_stamptype` SET `parent_id` = '".$data['parent_id']."' ,
			`bigcase_id` = '".$data['bigcase_id']."' ,`case_id` = '".$data['case_id']."' ,`name` = '".$data['name']."' ,`status` = '".$data['status']."' ,`price` = '".$data['price']."' ,
			`create_time` = '".time()."' WHERE `parent_id` = '".$data['raw_parent_id']."'; ";
			if($this->db->query($sql)){
				return array("Flag"=>100, "FlagString"=>"更新成功");
			}else{
				return array("Flag"=>103, "FlagString"=>"更新失败，已存在相同名称分类");
			}
		}else{
			$sql = "INSERT INTO ".DB_NAME_REGION.".`tbl_stamptype` (`parent_id`, `bigcase_id`, `case_id`, `name`, `status`, `price`, `create_time`)
			 VALUES ('".$data['parent_id']."', '".$data['bigcase_id']."', '".$data['case_id']."', '".$data['name']."', '".$data['status']."', '".$data['price']."', '".time()."'); ";
			if($this->db->query($sql)){
				return array("Flag"=>100, "FlagString"=>"添加成功");
			}else{
				return array("Flag"=>103, "FlagString"=>"添加失败，已存在相同名称分类");
			}
		}
	}
	
	function expression_cate_save($data){
		if($data['cate_id']){
			$sql = "UPDATE ".DB_NAME_REGION.".`tbl_expressiontype` SET `cate_name` = '".$data['cate_name']."' ,`status` = '".$data['status']."',
			`uptime` = '".time()."', `bigcase_id` = '".$data['bigcase_id']."', `case_id` = '".$data['case_id']."', `parent_id` = '".$data['parent_id']."'
			 WHERE `cate_id` = '".$data['cate_id']."'; ";
			if($this->db->query($sql)){
				return array("Flag"=>100, "FlagString"=>"更新成功");
			}else{
				return array("Flag"=>103, "FlagString"=>"更新失败，已存在相同名称分类");
			}
		}else{
			$sql = "INSERT INTO ".DB_NAME_REGION.".`tbl_expressiontype` (`cate_name`, `status`, `uptime`, `bigcase_id`, `case_id`, `parent_id`)
			VALUES ('".$data['cate_name']."', '".$data['status']."', '".time()."', '".$data['bigcase_id']."', '".$data['case_id']."', '".$data['parent_id']."'); ";
			if($this->db->query($sql)){
				return array("Flag"=>100, "FlagString"=>"添加成功");
			}else{
				return array("Flag"=>103, "FlagString"=>"添加失败，已存在相同名称分类");
			}
		}
	}
	
	function stamp_add($parent_id, $data){
		$t = time();
		$this->db->start_transaction();
		foreach($data['stamp_name'] as $k => $v){
			$sql = "INSERT INTO ".DB_NAME_REGION.".`tbl_stamp` (`parent_id`, `stamp_name`, `stamp_img_path`, `uptime`)
			 VALUES ('".$parent_id."', '".$data['stamp_name'][$k]."', '".$data['stamp_img_path'][$k]."', '".$t."'); ";
			if(!$this->db->query($sql)){
				$this->db->rollback();
				return array("Flag"=>103, "FlagString"=>"添加失败,同一个类型印章不能有相同的名字");
			}
		}
		$this->db->commit();
		return array("Flag"=>100, "FlagString"=>"添加成功");
	}
	
	function expression_add($cate_id, $data){
		$t = time();
		$this->db->start_transaction();
		foreach($data['expression_name'] as $k => $v){
			$sql = "INSERT INTO ".DB_NAME_REGION.".`tbl_expression` (`cate_id`, `img_name`, `img_path`, `uptime`)
			VALUES ('".$cate_id."', '".$data['expression_name'][$k]."', '".$data['expression_img_path'][$k]."', '".$t."'); ";
			if(!$this->db->query($sql)){
				$this->db->rollback();
				return array("Flag"=>103, "FlagString"=>"添加失败,同一个类型印章不能有相同的名字");
			}
		}
		$this->db->commit();
		return array("Flag"=>100, "FlagString"=>"添加成功");
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