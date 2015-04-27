<?php
class SpecialNum{
	//数据库指针
	protected $db = null;

	//构造函数
	function __construct() {
		$this->db = domain::main()->GroupDBConn();
	}
	
	function cate_list($group_id){
		if($group_id <= 0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$group_id = intval($group_id);
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`special_num_cate` WHERE group_id = ".$group_id;
		$total = $this->db->get_var($sql);
		$res = array();
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = "SELECT * FROM ".DB_NAME_SHOP.".`special_num_cate` WHERE group_id = ".$group_id." ORDER BY `order` LIMIT ".$page_arr['limit'];
			$res = $this->db->get_results($sql, "ASSOC");
			//获取分类下的靓号数目
			$sqls = array();
			foreach($res as $key=>$value){
				$sqls[] = "(SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`special_num` WHERE category = ".$value['cate_id'].") as '".$key."'";
			}
			$sql = "SELECT ".join(",", $sqls);
			$row = $this->db->get_row($sql);
			foreach($row as $key=>$count){
				$res[$key]['num_count'] = $count;
			}
		}
		return array('Flag'=>100, 'Page'=>$page_arr['page'], 'List'=>$res);
	}
	
	function add_cate($group_name, $group_id, $shop_status, $words){
		if($group_id <= 0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		if(mb_strlen($group_name,'utf-8') > 8 || !$group_name){
			return array('Flag'=>102,'FlagString'=>'分类名称需要在8个字之内');
		}
		if(mb_strlen($words,'utf-8') > 3 && $words){
			return array('Flag'=>102, 'FlagString'=>'角标文字在3个字之内');
		}
		$shop_status = intval($shop_status);
		
		//获取最大排序数加1
		$sql = "SELECT MAX(`order`) FROM ".DB_NAME_SHOP.".`special_num_cate` WHERE group_id = ".$group_id;
		$max = $this->db->get_var($sql);
		$sql = "INSERT INTO ".DB_NAME_SHOP.".`special_num_cate` (`cate_name`, `add_date`, `order`, `group_id`, `status`, `words`)
		 VALUES ('".$group_name."', '".time()."', '".(intval($max)+1)."', '".$group_id."', '".$shop_status."', '".$words."');";
		if($this->db->query($sql)){
			return array("Flag"=>100, 'FlagString'=>"添加成功");
		}else{
			return array("Flag"=>102, 'FlagString'=>"添加失败，已存在相同类型");
		}
	}
	
	function update_cate($group_name, $group_id, $cate_id, $shop_status, $words){
		if($group_id <= 0 || $cate_id <= 0){
			return array('Flag'=>101, 'FlagString'=>'参数错误');
		}
		if(mb_strlen($group_name,'utf-8') > 8 || !$group_name){
			return array('Flag'=>102, 'FlagString'=>'分类名称需要在8个字之内');
		}
		if(mb_strlen($words,'utf-8') > 3 && $words){
			return array('Flag'=>102, 'FlagString'=>'角标文字在3个字之内');
		}
		$shop_status = intval($shop_status);
		
		$sql = "UPDATE ".DB_NAME_SHOP.".`special_num_cate` SET `cate_name` = '".$group_name."',`status`=".$shop_status.", `words`='".$words."' WHERE `group_id` = '".$group_id."' AND `cate_id` = '".$cate_id."';";
		if($this->db->query($sql)){
			return array("Flag"=>100, 'FlagString'=>"更新成功");
		}else{
			return array("Flag"=>102, 'FlagString'=>"更新失败，已存在相同类型");
		}
	}
	
	function delete_cate($group_id, $cate_id){
		if($group_id <= 0 || $cate_id <= 0){
			return array('Flag'=>101, 'FlagString'=>'参数错误');
		}
		//删除分类以下所有靓号
		$this->db->start_transaction();
		$sql = "DELETE FROM ".DB_NAME_SHOP.".`special_num_cate` WHERE `cate_id` = '".$cate_id."' AND `group_id` = '".$group_id."';";
		$done1 = $this->db->query($sql);
		$sql = "DELETE FROM ".DB_NAME_SHOP.".`special_num` WHERE `category` = '".$cate_id."' AND `group_id` = '".$group_id."';";
		$done2 = $this->db->query($sql);
		if($done1 && $done2){
			$this->db->commit();
			return array("Flag"=>100, 'FlagString'=>"删除成功");
		}else{
			$this->db->rollback();
			return array("Flag"=>100, 'FlagString'=>"删除失败");
		}
	}
	
	function get_cate_name($group_id, $cate_id){
		if($group_id <= 0 || $cate_id <= 0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT cate_name FROM ".DB_NAME_SHOP.".`special_num_cate` WHERE group_id = ".$group_id." AND cate_id = ".$cate_id;
		$cate_name = $this->db->get_var($sql);
		if($cate_name){
			return array("Flag"=>100, "CateName"=>$cate_name, 'FlagString'=>'操作成功');
		}else{
			return array("Flag"=>102, 'FlagString'=>'无此分类');
		}
	}
	
	function update_order($cate_id, $group_id, $option){
		if(!in_array($option, array("raise", "down")) || $cate_id <= 0 || $group_id <= 0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT MAX(`order`) AS `max`,MIN(`order`) AS `min` FROM ".DB_NAME_SHOP.".`special_num_cate` WHERE group_id = ".$group_id;
		$res = $this->db->get_row($sql);
		$sql = "SELECT `order` FROM ".DB_NAME_SHOP.".`special_num_cate` WHERE cate_id = ".$cate_id." AND group_id = ".$group_id;
		$order = $this->db->get_var($sql);
		if(!isset($order)){
			return array('Flag'=>101,'FlagString'=>'无此分类');
		}
		//获得交换相邻序号,分类id
		if($option == "raise" && $order  == $res['min']){
			return array('Flag'=>102,'FlagString'=>'已经在顶层');
		}elseif($option == "down" && $order  == $res['max']){
			return array('Flag'=>102,'FlagString'=>'已经在底层');
		}elseif($option == "raise"){
			$sql = "SELECT `cate_id`, `order` FROM ".DB_NAME_SHOP.".`special_num_cate` WHERE group_id = ".$group_id." AND `order` < ".$order." ORDER BY `order` DESC LIMIT 1";
		}elseif($option == "down"){
			$sql = "SELECT `cate_id`, `order` FROM ".DB_NAME_SHOP.".`special_num_cate` WHERE group_id = ".$group_id." AND `order` > ".$order." ORDER BY `order` LIMIT 1";
		}
		$ex_row = $this->db->get_row($sql);
		//交换排序号
		$this->db->start_transaction();
		$done1 = $this->db->query("UPDATE ".DB_NAME_SHOP.".`special_num_cate` SET `order` = '".$ex_row['order']."' WHERE `cate_id` = '".$cate_id."'; ");
		$done2 = $this->db->query("UPDATE ".DB_NAME_SHOP.".`special_num_cate` SET `order` = '".$order."' WHERE `cate_id` = '".$ex_row['cate_id']."'; ");
		if($done1 && $done2){
			$this->db->commit();
			return array('Flag'=>100,'FlagString'=>'操作成功');
		}else{
			$this->db->rollback();
			return array('Flag'=>102,'FlagString'=>'操作失败');
		}
		
	}
	
	function num_list($cate_id, $group_id){
		if($group_id <= 0 || $cate_id <= 0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		
		$sql = "SELECT cate_name FROM ".DB_NAME_SHOP.".`special_num_cate` WHERE cate_id = ".$cate_id." AND group_id = ".$group_id;
		$cate_name = $this->db->get_var($sql);
		if(!$cate_name){
			return array('Flag'=>102,'FlagString'=>'不存在该分类');
		}
		
		$sql = "SELECT count(*) FROM ".DB_NAME_SHOP.".`special_num` WHERE category = ".$cate_id;
		$total = $this->db->get_var($sql);
		$page_arr = $this->showPage($total);
		
		$sql = "SELECT id,`name`,price,uptime FROM ".DB_NAME_SHOP.".`special_num` WHERE category = ".$cate_id." LIMIT ".$page_arr['limit'];
		$list = $this->db->get_results($sql, "ASSOC");
		return array('Flag'=>100,'Page'=>$page_arr['page'], 'List'=>$list, 'CateName'=>$cate_name);
	}
	
	
	function add_num($name, $category, $price, $group_id, $options){
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`special_num_cate` WHERE cate_id = ".$category." AND group_id = ".$group_id;
		$count = $this->db->get_var($sql);
		if(!$count){
			return array('Flag'=>102,'FlagString'=>'不存在该分类');
		}
		//检测是否存在
		if(count($name) != count(array_unique($name))){
			return array('Flag'=>102,'FlagString'=>'不能开设相同的靓号');
		}
		$name_str = is_array($name)?join(",", $name):$name;
		$sql = "SELECT `name` FROM ".DB_NAME_SHOP.".`special_num` WHERE `name` IN (".$name_str.") AND group_id = ".$group_id;
		$res = $this->db->get_results($sql);
		$names = array();
		foreach((array)$res as $one){
			$names[] = $one['name'];
		}
		if($names){
			return array("Flag"=>102, "FlagString"=>"本站靓号".join(",", $names)."已经存在");
		}
		
		if(is_array($name)){
			//添加多个
			$c = count($name);
			$sql = array();
			for($i=0;$i<$c;$i++){
				if(intval($price[$i]) <= 0){
					return array("Flag"=>102, "FlagString"=>"价格不能低于或等于0");
				}
				$res = $this->pre_add_num($name[$i], $category, $price[$i], $group_id, $options[$i]);
				if($res['Flag'] != 100){
					return $res;
				}else{
					$sql[] = $res['Sql'];
				}
			}
			$this->db->start_transaction();
			for($i=0;$i<$c;$i++){
				if(!$this->db->query($sql[$i])){
					$this->db->rollback();
					return array("Flag"=>103, "FlagString"=>"添加失败");
				}
			}
			$this->db->commit();
			return array("Flag"=>100, "FlagString"=>"添加成功");
		}else{
			//添加一个
			$res = $this->pre_add_num($name, $category, $price, $group_id, $options);
			if($res['Flag'] != 100){
				return $res;
			}
			
			if($this->db->query($res['Sql'])){
				return array('Flag'=>100,'FlagString'=>'添加成功');
			}else{
				return array('Flag'=>100,'FlagString'=>'添加失败');
			}
		}
		
	}
	
	function delete_num($group_id, $id){
		$sql = "DELETE FROM ".DB_NAME_SHOP.".`special_num` WHERE `id` = '".$id."' AND `group_id` = '".$group_id."';";
		if($this->db->query($sql)){
			return array("Flag"=>100, 'FlagString'=>"删除成功");
		}else{
			return array("Flag"=>103, 'FlagString'=>"删除失败");
		}
	}

	public function giftNum($data){
		//检测参数
		$data['uin'] = intval($data['uin']);
		$data['special_num'] = intval($data['special_num']);
		$data['pay_uin'] = intval($data['pay_uin']);
		$data['group_id'] = intval($data['group_id']);
		if($data['uin'] < 1 || $data['special_num'] < 1 || $data['pay_uin'] < 0 || $data['group_id'] < 0 ){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		//用户是否存在
		$userinfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$data['uin'])));
		if($userinfo['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'用户不存在');
		}

		//靓号是否存在
		$sql = "SELECT * FROM ".DB_NAME_SHOP.".special_num WHERE group_id={$data['group_id']} AND name={$data['special_num']}";
		$row0 = $this->db->get_row($sql,ASSOC);
		if(empty($row0)){
			return array('Flag'=>103,'FlagString'=>'靓号不存在，请先去靓号分类中开通！');
		}
		
		//用户是否已经有该靓号
		$sql = "SELECT count(*) FROM ".DB_NAME_SHOP.".commodity_stock WHERE group_id={$data['group_id']} AND uin={$data['uin']} AND liang_id={$data['special_num']} AND parent_id=10428";
		if($this->db->get_var($sql) > 0) return array('Flag'=>104,'FlagString'=>'用户已有该靓号');
		
		if($data['service_gift']){//在个人中心赠送
			//该靓号是否是自己未绑定的靓号
			$sql = "SELECT * FROM ".DB_NAME_SHOP.".commodity_stock WHERE group_id={$data['group_id']} AND liang_id={$data['special_num']} AND parent_id=10428 AND uin={$data['pay_uin']} AND other_id=0";
			$row = $this->db->get_row($sql,ASSOC);
			if(empty($row)){
				return array('Flag'=>105,'FlagString'=>'靓号已经被他人绑定');
			}
		}else{//在站后台赠送
			//该靓号是否已被别人买走
			$sql = "SELECT id FROM ".DB_NAME_SHOP.".commodity_stock WHERE group_id={$data['group_id']} AND liang_id={$data['special_num']} AND parent_id=10428";
			if($this->db->get_var($sql) > 0){
				return array('Flag'=>105,'FlagString'=>'靓号已经被他人绑定');
			}
		}
		
		//用户是否已有靓号,如果没有，将该靓号设为启用
		$other_id = 1;
		$sql = "SELECT count(*) FROM ".DB_NAME_SHOP.".commodity_stock WHERE group_id={$data['group_id']} AND uin={$data['uin']} AND parent_id=10428";
		if($this->db->get_var($sql) > 0) $other_id = 0;
		
		if($data['service_gift']){
			//删掉该靓号之前的所属关系&&保存进库存
			$this->db->start_transaction();
			$sql = "DELETE FROM ".DB_NAME_SHOP.".commodity_stock WHERE group_id={$data['group_id']} AND parent_id=10428 AND liang_id={$data['special_num']} AND uin={$data['pay_uin']}";
			$done1 = $this->db->query($sql);
			$sql = "INSERT INTO ".DB_NAME_SHOP.".commodity_stock(uin,cate_id,commodity,case_id,parent_id,group_id,roomid,role_id,other_id,quality,liang_id) VALUES({$data['uin']},{$row0['category']},{$row0['id']},{$row0['case_id']},{$row0['parent_id']},{$data['group_id']},0,0,{$other_id},1,{$data['special_num']})";
			$done2 = $this->db->query($sql);
			if($done1 && $done2){
				$this->db->commit();
			}else{
				$this->db->rollback();
				return array("Flag"=>106, 'FlagString'=>"赠送靓号失败");
			}
		}else{
			$sql = "INSERT INTO ".DB_NAME_SHOP.".commodity_stock(uin,cate_id,commodity,case_id,parent_id,group_id,roomid,role_id,other_id,quality,liang_id) VALUES({$data['uin']},{$row0['category']},{$row0['id']},{$row0['case_id']},{$row0['parent_id']},{$data['group_id']},0,0,{$other_id},1,{$data['special_num']})";
			if(!$this->db->query($sql)) return array("Flag"=>106, 'FlagString'=>"赠送靓号失败");
		}
		
		//记录流水
		$time = time();
		$sql = "INSERT INTO ".DB_NAME_SHOP.".commodity_running(group_id,uin,pay_uin,commodity,case_id,parent_id,name,quantity,money,uptime) VALUES({$data['group_id']},{$data['uin']},{$data['pay_uin']},{$row0['id']},{$row0['case_id']},{$row0['parent_id']},{$data['special_num']},1,0,{$time})";
		$this->db->query($sql);

		return array('Flag'=>100,'FlagString'=>'赠送靓号成功');
	}

	public function numRecord($group_id,$search_data,$no_page=false){
		$where = "WHERE group_id={$group_id} AND parent_id=10428";
		if(isset($search_data['uin']) && $search_data['uin'] > 0){
			$uin = intval($search_data['uin']);
			$where .= " AND uin={$uin}";
		}
		if(isset($search_data['special_num']) && $search_data['special_num'] > 0){
			$special_num = intval($search_data['special_num']);
			$where .= " AND liang_id={$special_num}";
		}
		if(isset($search_data['stock_id']) && $search_data['stock_id'] > 0){
			$id = intval($search_data['stock_id']);
			$where .= " AND id={$id}";
		}
		if($no_page){
			$sql = "SELECT id,uin,other_id,liang_id FROM ".DB_NAME_SHOP.".commodity_stock {$where}";
			$res = $this->db->get_results($sql,ASSOC);
			return array('Flag'=>100, 'List'=>$res);
		}else{
			$sql = "SELECT COUNT(1) FROM ".DB_NAME_SHOP.".commodity_stock {$where}";
			$count = $this->db->get_var($sql);
			if($count > 0){
				$page_arr = $this->showPage($count);
				$sql = "SELECT id,uin,other_id,liang_id FROM ".DB_NAME_SHOP.".commodity_stock {$where} LIMIT {$page_arr['limit']}";
				$res = $this->db->get_results($sql,ASSOC);
			}
			return array('Flag'=>100, 'Page'=>$page_arr['page'], 'List'=>$res);
		}
	}

	public function recycleNum($group_id,$stock_id){
		if($stock_id < 1){
			return array('Flag'=>101,'FlagString'=>'没有库存信息');
		}
		$sql = "SELECT id,uin,liang_id FROM ".DB_NAME_SHOP.".commodity_stock WHERE id={$stock_id}";
		$row = $this->db->get_row($sql,ASSOC);
		if(empty($row)){
			return array('Flag'=>102,'FlagString'=>'没有库存信息');
		}
		$sql = "DELETE FROM ".DB_NAME_SHOP.".commodity_stock WHERE id={$stock_id} AND group_id={$group_id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'靓号收回失败');
		}
		$sql = "DELETE FROM ".DB_NAME_SHOP.".commodity_running WHERE uin={$row['uin']} AND name={$row['liang_id']}";
		$this->db->query($sql);
		return array('Flag'=>100,'FlagString'=>'靓号收回成功');
	}
	
	function bannerImg($group_id, $img_path, $src, $del){
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`banner_img` WHERE group_id = ".$group_id;
		$count = $this->db->get_var($sql);
		if($count){
			if($del){
				$sql = "UPDATE ".DB_NAME_SHOP.".`banner_img` SET `src` = '".$src."', `img_path` = '' WHERE `group_id` = '".$group_id."';";
			}else{
				if($img_path){
					$sql = "UPDATE ".DB_NAME_SHOP.".`banner_img` SET `img_path` = '".$img_path."' ,`src` = '".$src."' WHERE `group_id` = '".$group_id."';";
				}else{
					$sql = "UPDATE ".DB_NAME_SHOP.".`banner_img` SET `src` = '".$src."' WHERE `group_id` = '".$group_id."';";
				}
			}
		}else{
			$sql = "INSERT INTO ".DB_NAME_SHOP.".`banner_img` (`img_path`, `src`, `group_id`) VALUES ('".$img_path."', '".$src."', '".$group_id."'); ";
		}
		if($this->db->query($sql)){
			return array("Flag"=>100, 'FlagString'=>"操作成功");
		}else{
			return array("Flag"=>103, 'FlagString'=>"操作失败");
		}
	}
	
	function getBannerImg($group_id){
		$sql = "SELECT `id`,`img_path`,`src` FROM ".DB_NAME_SHOP.".`banner_img` WHERE group_id = ".$group_id;
		$row = $this->db->get_row($sql);
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$row);
	}
	
	//启用靓号
	function useNum($group_id, $uin, $liang_id){
		if($group_id < 0 || $uin <0 || $liang_id < 0 ){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql1 = "UPDATE ".DB_NAME_SHOP.".commodity_stock SET other_id=0 WHERE group_id=".$group_id." AND uin=".$uin." AND liang_id!=".$liang_id;
		$sql2 = "UPDATE ".DB_NAME_SHOP.".commodity_stock SET other_id=1 WHERE group_id=".$group_id." AND uin=".$uin." AND liang_id=".$liang_id;
		if($this->db->query($sql1) && $this->db->query($sql2)){
			return array('Flag'=>100, 'FlagString'=>'启用靓号成功');
		}else{
			return array('Flag'=>101, 'FlagString'=>'启用靓号失败');
		}
	}
	
	private function pre_add_num($name, $category, $price, $group_id, $options){
		$name = htmlspecialchars(addslashes($name));
		$price = htmlspecialchars(addslashes($price));
		if($category <= 0 || $price < 0 || $group_id <= 0){
			return array('Flag'=>101, 'FlagString'=>'参数错误');
		}
		if(!(preg_match('/^\d{4,7}$/', $name) && intval($name) >= 1000 && intval($name) <= 9999999)){
			return array('Flag'=>101, 'FlagString'=>'号码需要为4-7位数字，并且在1000到9999999之间');
		}
		//合并options数组
		$insert = array("name"=>$name, "category"=>$category, "price"=>$price, "group_id"=>$group_id);
		$insert = array_merge((array)$options, $insert);
		
		$sql = "INSERT INTO ".DB_NAME_SHOP.".`special_num` (`".join("`,`", array_keys($insert))."`,`uptime`) VALUES ('".join("','", array_values($insert))."','".time()."')";
		return array('Flag'=>100, "Sql"=>$sql);
	}
	
	//分页
	private function showPage($total, $perpage = 15) {
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