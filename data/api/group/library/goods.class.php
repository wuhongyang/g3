<?php
class GoodsConf{
	//数据库指针
	protected $db = null;

	//构造函数
	public function __construct() {
		$this->db = domain::main()->GroupDBConn();
		$this->platform_db = db::connect(config('database','default'));
	}

	public function subCateList($group_id,$cate_id){
		$cate_id = intval($cate_id);
		$cateInfo = $this->cateInfo($cate_id,$group_id);
		if($cateInfo['Flag'] != 100){
			return $cateInfo;
		}
		$sql = "SELECT * FROM ".DB_NAME_SHOP.".goods_sub_cat WHERE cate_id={$cate_id} ORDER BY `order`";
		$subCateList = $this->db->get_results($sql, ASSOC);
		if(!$subCateList){
			return array('Flag'=>102, 'FlagString'=>'没有子分类','CateInfo'=>$cateInfo['Info']);
		}
		return array('Flag'=>100, 'FlagString'=>'ok', 'SubCateList'=>$subCateList,'CateInfo'=>$cateInfo['Info']);
	}

	public function subCateInfo($id, $group_id){
		$id = intval($id);
		$sql = "SELECT * FROM ".DB_NAME_SHOP.".goods_sub_cat WHERE id={$id} AND group_id={$group_id}";
		if($info = $this->db->get_row($sql, ASSOC)){
			return array('Flag'=>100,'FlagString'=>'OK', 'Info'=>$info);
		}
		return array('Flag'=>101,'FlagString'=>'获取子分类失败');
	}

	public function subCateAdd($data){
		$data['name'] = htmlspecialchars(addslashes(mb_substr($data['name'], 0, 45)));
		$data['status'] = intval($data['status']) > 0 ? 1 : 0;
		$cateInfo = $this->cateInfo($data['category_id'],$data['group_id']);
		if($cateInfo['Flag'] != 100){
			return $cateInfo;
		}
		$sql = "SELECT id FROM ".DB_NAME_SHOP.".goods_sub_cat WHERE cate_id={$cateInfo['Info']['id']} AND `name`='{$data['name']}' ";
		if($this->db->get_var($sql) > 0){
			return array('Flag'=>102, 'FlagString'=>'该分类下已存在相同名称，添加失败');
		}
		$this->db->start_transaction();
		$sql = "INSERT INTO ".DB_NAME_SHOP.".goods_sub_cat(`name`,`group_id`,`cate_id`,`status`) VALUES('{$data['name']}',{$data['group_id']},{$cateInfo['Info']['id']},{$data['status']})";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>102,'FlagString'=>'数据库异常，请联系管理员');
		}
		$insert_id = $this->db->insert_id();
		$sql = "UPDATE ".DB_NAME_SHOP.".goods_sub_cat SET `order`={$insert_id} WHERE id={$insert_id}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>102,'FlagString'=>'数据库异常，请联系管理员');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'添加'.$cateInfo['cate_name'].'分类成功');
	}

	public function subCateEdit($id, $data){
		$data['name'] = htmlspecialchars(addslashes(mb_substr($data['name'], 0, 45)));
		$data['status'] = intval($data['status']) > 0 ? 1 : 0;
		$id = intval($id);
		$cateInfo = $this->cateInfo($data['category_id'],$data['group_id']);
		if($cateInfo['Flag'] != 100){
			return $cateInfo;
		}
		$sql = "SELECT id FROM ".DB_NAME_SHOP.".goods_sub_cat WHERE id!={$id} AND cate_id={$cateInfo['Info']['id']} AND name='{$data['name']}'";
		if($this->db->get_var($sql) > 0){
			return array('Flag'=>102, 'FlagString'=>'该分类下已存在相同名称，编辑失败');
		}
		$sql = "UPDATE ".DB_NAME_SHOP.".goods_sub_cat SET `name`='{$data['name']}',`status`={$data['status']} WHERE id={$id} AND group_id={$data['group_id']} AND cate_id={$cateInfo['Info']['id']}";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'编辑分类失败');
		}
		return array('Flag'=>100,'FlagString'=>'编辑分类成功');
	}

	public function subCateMove($data){
		$data['id'] = intval($data['id']);
		$cateInfo = $this->cateInfo($data['cate_id'],$data['group_id']);
		if($cateInfo['Flag'] != 100){
			return $cateInfo;
		}
		$sql = "SELECT `order` FROM ".DB_NAME_SHOP.".goods_sub_cat WHERE id={$data['id']} AND cate_id={$cateInfo['Info']['id']} LIMIT 1";
		$order = $this->db->get_var($sql);
		if($data['option'] == 'raise'){
			$sql = "SELECT `id`,`order` FROM ".DB_NAME_SHOP.".goods_sub_cat WHERE cate_id={$cateInfo['Info']['id']} AND `order`<{$order} ORDER BY `order` DESC LIMIT 1";
			$dest = $this->db->get_row($sql, ASSOC);
			if(empty($dest)){
				return array('Flag'=>102,'FlagString'=>'无法上移');
			}
		}elseif($data['option'] == 'down'){
			$sql = "SELECT `id`,`order` FROM ".DB_NAME_SHOP.".goods_sub_cat WHERE cate_id={$cateInfo['Info']['id']} AND `order`>{$order} ORDER BY `order` ASC LIMIT 1";
			$dest = $this->db->get_row($sql, ASSOC);
			if(empty($dest)){
				return array('Flag'=>102,'FlagString'=>'无法下移');
			}
		}else{
			return array('Flag'=>102,'FlagString'=>'缺少必要参数');
		}
		$source = array('id'=>$data['id'],'order'=>$order);
		return $this->swap($source, $dest, DB_NAME_SHOP.".goods_sub_cat");
	}
	
	function saveCate($id, $cate_name, $cate_id, $img_path, $img_src, $state, $group_id){
		if($id){
			return $this->updateCate($id, $cate_name, $cate_id, $img_path, $img_src, $state, $group_id);
		}else{
			return $this->addCate($cate_name, $cate_id, $img_path, $img_src, $state, $group_id);
		}
	}
	
	function addCate($cate_name, $cate_id, $img_path, $img_src, $state, $group_id){
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`goods_cate` where cate_name = '".$cate_name."' AND group_id = '".$group_id."'";
		$exist = $this->db->get_var($sql);
		if($exist > 0){
			return array("Flag"=>102, "FlagString"=>"已存在分类名称");
		}
		
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`goods_cate` where cate_id = '".$cate_id."' AND group_id = '".$group_id."'";
		$exist = $this->db->get_var($sql);
		if($exist > 0){
			return array("Flag"=>102, "FlagString"=>"已存在相同分类");
		}
		
		$sql = "SELECT MAX(`order`) FROM ".DB_NAME_SHOP.".goods_cate WHERE group_id = ".$group_id;
		$max_order = $this->db->get_var($sql) + 1;
		
		$sql = "INSERT INTO ".DB_NAME_SHOP.".`goods_cate` (`cate_name`, `cate_id`, `img_path`, `img_src`, `state`, `group_id`, `order`)
		 VALUES ('".$cate_name."', '".$cate_id."', '".$img_path."', '".$img_src."', '".$state."', '".$group_id."', '".$max_order."');";
		$done = $this->db->query($sql);
		if($done){
			return array("Flag"=>100, "FlagString"=>"操作成功");
		}else{
			return array("Flag"=>102, "FlagString"=>"操作失败");
		}
	}
	
	function updateCate($id, $cate_name, $cate_id, $img_path, $img_src, $state, $group_id){
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`goods_cate` where cate_name = '".$cate_name."' AND group_id = '".$group_id."' AND id != ".$id;
		$exist = $this->db->get_var($sql);
		if($exist > 0){
			return array("Flag"=>102, "FlagString"=>"已存在分类名称");
		}
		
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`goods_cate` where cate_id = '".$cate_id."' AND group_id = '".$group_id."' AND id != ".$id;
		$exist = $this->db->get_var($sql);
		if($exist > 0){
			return array("Flag"=>102, "FlagString"=>"已存在相同分类");
		}
		
		if($img_path == "--"){
			$sql = "UPDATE ".DB_NAME_SHOP.".`goods_cate` set `cate_name` = '".$cate_name."', `cate_id` = '".$cate_id."', `img_path` = '', `img_src` = '".$img_src."', `state` = '".$state."' where id = ".$id;
		}elseif($img_path){
			$sql = "UPDATE ".DB_NAME_SHOP.".`goods_cate` set `cate_name` = '".$cate_name."', `cate_id` = '".$cate_id."', `img_path` = '".$img_path."', `img_src` = '".$img_src."', `state` = '".$state."' where id = ".$id;
		}else{
			$sql = "UPDATE ".DB_NAME_SHOP.".`goods_cate` set `cate_name` = '".$cate_name."', `cate_id` = '".$cate_id."', `img_src` = '".$img_src."', `state` = '".$state."' where id = ".$id;
		}
		$done = $this->db->query($sql);
		if($done){
			return array("Flag"=>100, "FlagString"=>"操作成功");
		}else{
			return array("Flag"=>102, "FlagString"=>"操作失败");
		}
	}
	
	function cateList($group_id){
	    $sql = "SELECT id,`name` FROM ".DB_NAME_SHOP.".`category` WHERE id <> 4 AND status=1";
        $category = $this->platform_db->get_results($sql, "ASSOC");
       
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`goods_cate` WHERE group_id = ".$group_id."";
		$total = $this->db->get_var($sql);
		$page_arr = $this->showPage($total);
		
		$sql = "SELECT `id`,`cate_name`,`cate_id`,`state` FROM ".DB_NAME_SHOP.".`goods_cate` WHERE group_id = ".$group_id."  ORDER BY `order` LIMIT ".$page_arr['limit'];
		$data = $this->db->get_results($sql, "ASSOC");
		foreach((array)$data as $k=>$one){
			if($one['cate_id'] != -1){
				$table = DB_NAME_SHOP.".goods";
			}else{
				$table = DB_NAME_SHOP.".goods_package";
			}
			$sql = "SELECT COUNT(*) FROM ".$table." WHERE cate_id = ".$one['id']." AND group_id = ".$group_id;
			$data[$k]['count'] = $this->db->get_var($sql);
			
		}
		return array("Flag"=>100, "FlagString"=>"查找成功", "Data"=>$data, "Category"=>$category, "Page"=>$page_arr['page']);
	}
	
	function cateMove($cate_id, $option, $group_id){
		$table = DB_NAME_SHOP.".goods_cate";
		return $this->move($cate_id, $option, $group_id, $table);
	}
	
	function goodsMove($id, $option, $group_id, $cate_id, $sub_cate_id){
		$sql = "SELECT cate_id FROM ".DB_NAME_SHOP.".goods_cate WHERE id = ".$cate_id;
		$c_cate_id = $this->db->get_var($sql);
		$table = DB_NAME_SHOP.".goods";
		if($c_cate_id == -1){
			$table = DB_NAME_SHOP.".goods_package";
		}
		return $this->move($id, $option, $group_id, $table, $cate_id, $sub_cate_id);
	}
	
	private function move($id, $option, $group_id, $table, $cate_id="", $usb_cate_id=0){
		$where = "";
		if($cate_id > 0){
			$where .= " AND cate_id = ".$cate_id;
		}
		if($sub_cate_id > 0){
			$where .= " AND sub_cate_id = ".$usb_cate_id;
		}
		$sql = "SELECT `order` FROM ".$table." WHERE id = ".$id." AND group_id = ".$group_id.$where;
		$order = $this->db->get_var($sql);
		$sql = "SELECT MAX(`order`) as a,MIN(`order`) as i FROM ".$table." WHERE group_id = ".$group_id.$where;
		$m_order = $this->db->get_row($sql, "ASSOC");
		if($option == "raise" && $order != $m_order['i']){
			$sql = "SELECT `id`,`order` FROM ".$table." WHERE `order` < ".$order." AND group_id = ".$group_id.$where." order by `order` desc LIMIT 1";
			$res = $this->db->get_row($sql, "ASSOC");
			$ex_order = $res['order'];
			$ex_cate_id = $res['id'];
		}elseif($option == "down" && $order != $m_order['a']){
			$sql = "SELECT `id`,`order` FROM ".$table." WHERE `order` > ".$order." AND group_id = ".$group_id.$where." order by `order` LIMIT 1";
			$res = $this->db->get_row($sql, "ASSOC");
			$ex_order = $res['order'];
			$ex_cate_id = $res['id'];
		}else{
			return array("Flag"=>102, "FlagString"=>"无法移动");
		}
		return $this->swap(array('id'=>$id,'order'=>$order), array('id'=>$ex_cate_id,'order'=>$ex_order), $table);
	}

	private function swap($source, $dest, $table){
		$this->db->start_transaction();
		$sql = "UPDATE ".$table." SET `order` = '".$dest['order']."' WHERE `id` = '".$source['id']."'; ";
		$done1 = $this->db->query($sql);
		$sql = "UPDATE ".$table." SET `order` = '".$source['order']."' WHERE `id` = '".$dest['id']."'; ";
		$done2 = $this->db->query($sql);
		if($done1 && $done2){
			$this->db->commit();
			return array("Flag"=>100, "FlagString"=>"操作成功");
		}else{
			$this->db->rollback();
			return array("Flag"=>102, "FlagString"=>"操作失败");
		}
	}
	
	function cateDetail($id, $group_id){
		$sql = "SELECT * FROM ".DB_NAME_SHOP.".`goods_cate` where id = ".$id." AND group_id = ".$group_id;
		$row = $this->db->get_row($sql, "ASSOC");
		$row['img_path'] = $row['img_path']?cdn_url(PIC_API_PATH."/p/".$row['img_path']."/0/0.jpg"):"";
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$row);
	}
	
	function goodsList($cate_id, $sub_cate_id, $group_id){
		$subCateInfo = $this->subCateInfo($sub_cate_id, $group_id);
		if($subCateInfo['Flag'] != 100){
			return array("Flag"=>102, "FlagString"=>"不存在该分类");
		}
		$subCateInfo = $subCateInfo['Info'];
		if($cate_id != -1){
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`goods` WHERE group_id={$group_id} AND cate_id={$subCateInfo['cate_id']} AND sub_cate_id={$subCateInfo['id']}";
			//$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`goods` where sub_cate_id = ".$subCateInfo['id']." AND group_id = ".$group_id;
			$total = $this->db->get_var($sql);
			$page_arr = $this->showPage($total);
			$sql = "SELECT id, `commodity_name` AS `name`, `price`,`duration`,`state` FROM ".DB_NAME_SHOP.".`goods` WHERE group_id={$group_id} AND cate_id={$subCateInfo['cate_id']} AND sub_cate_id={$subCateInfo['id']} ORDER by `order` LIMIT {$page_arr['limit']}";
			//$sql = "SELECT id, `commodity_name` AS `name`, `price`,`duration`,`state` FROM ".DB_NAME_SHOP.".`goods` where sub_cate_id = ".$subCateInfo['id']." AND group_id = ".$group_id." ORDER by `order` LIMIT ".$page_arr['limit'];
		}else{
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`goods_package` WHERE group_id={$group_id} AND cate_id={$subCateInfo['cate_id']} AND sub_cate_id={$subCateInfo['id']}";
			//$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`goods_package` where sub_cate_id = ".$subCateInfo['id']." AND group_id = ".$group_id;
			$total = $this->db->get_var($sql);
			$page_arr = $this->showPage($total);
			$sql = "SELECT id, `package_name` AS `name`, `price`,`state` FROM ".DB_NAME_SHOP.".`goods_package` WHERE group_id={$group_id} AND cate_id={$subCateInfo['cate_id']} AND sub_cate_id={$subCateInfo['id']} ORDER by `order` LIMIT {$page_arr['limit']}";
			//$sql = "SELECT id, `package_name` AS `name`, `price`,`state` FROM ".DB_NAME_SHOP.".`goods_package` where sub_cate_id = ".$subCateInfo['id']." AND group_id = ".$group_id." ORDER by `order` LIMIT ".$page_arr['limit'];
		}
		$res = $this->db->get_results($sql, "ASSOC");
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$res, "CateInfo"=>$subCateInfo, "Page"=>$page_arr['page']);
	}
	
	function allCateGoods($group_id, $goods_id){
		$sql = "SELECT a.*,b.`scope` FROM (SELECT a.id AS goods_id,
		a.`commodity_id`,a.commodity_name,b.cate_name,b.id AS cate_id 
		FROM ".DB_NAME_SHOP.".`goods` AS a LEFT JOIN ".DB_NAME_SHOP.".`goods_cate`
		 AS b ON a.`cate_id` = b.`id` WHERE a.group_id = '".$group_id."') AS a
		 LEFT JOIN ".DB_NAME_TPL.".`commodity` AS b ON a.commodity_id = b.id";
		$res = $this->platform_db->get_results($sql, "ASSOC");
		$data = array();
		foreach($res as $one){
			if($goods_id && $goods_id == $one['goods_id']){
				continue;
			}
			if(!$data[$one['scope']][$one['cate_id']]['cate_name']){
				$data[$one['scope']][$one['cate_id']]['cate_name'] = $one['cate_name'];
			}
			$data[$one['scope']][$one['cate_id']][] = $one;
		}
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$data);
	}
	
	function packageGoods($cate_id, $group_id){
		$sql = "SELECT `cate_name`,`cate_id` FROM ".DB_NAME_SHOP.".`goods_cate` where id = ".$cate_id." AND group_id = ".$group_id;
		$row = $this->db->get_row($sql, "ASSOC");
		$cate_name = $row['cate_name'];
		$cate_id = $row['cate_id'];
		$module_id = domain::main()->GroupKeyVal($group_id,'module_id');
		$sql = "SELECT id, `name`, role_id, scope, price, expire,`type`,is_gift FROM ".DB_NAME_TPL.".`commodity` WHERE tpl_id = ".$module_id." AND category = ".$cate_id." AND `status` = 1";
        $res = $this->platform_db->get_results($sql, "ASSOC");
		foreach($res as $k=>$one){
			if($one['type'] == 1){
				$res[$k]['consult'] = ceil($one['price']/$one['expire'])."/天";
			}elseif($one['type'] == 2){
				$res[$k]['consult'] = $one['price'];
			}
			if($one['scope'] == 1){
				$res[$k]['scope_name'] = "整站";
			}elseif($one['scope'] == 2){
				$res[$k]['scope_name'] = "指定房间（只在购买的对应房间生效）";
			}
			if($one['role_id']){
				$sql = "SELECT `name` FROM ".DB_NAME_TPL.".role WHERE id = ".$one['role_id'];
				$res[$k]['role_name'] = $this->platform_db->get_var($sql);
			}else{
				$res[$k]['role_name'] = "无";
			}
		}
		return array("Flag"=>100, "FlagString"=>"查询成功", "GoodsName"=>$res, "CateId"=>$cate_id);
	}
	
	function scopeGoods($group_id){
		
		$sql = "SELECT cate_name,id,cate_id FROM ".DB_NAME_SHOP.".goods_cate WHERE group_id = ".$group_id;
		$res = $this->db->get_results($sql, "ASSOC");
		$cate_arr = array();
		$package_ids = array();
		foreach($res as $one){
			if($one['cate_id'] != -1){
				$cate_arr[$one['id']] = array("name"=>$one['cate_name']);
			}else{
				$package_ids[] = $one['id'];
			}
		}
		
		$sql = "SELECT a.`id`,a.`commodity_name` as `name`,a.`cate_id`,a.`commodity_id` FROM ".DB_NAME_SHOP.".`goods` AS a WHERE a.group_id = ".$group_id." AND a.cate_id NOT IN (".join(",", $package_ids).")";
		$goods = $this->db->get_results($sql, "ASSOC");

		$scope_arr = array("1"=>$cate_arr, "2"=>$cate_arr);
		
		foreach($goods as $one){
			$sql = "SELECT b.`scope`,b.`price`,b.`expire`,b.`type` FROM ".DB_NAME_TPL.".`commodity` AS b WHERE b.`id`=".$one['commodity_id'];
			$commodity = $this->platform_db->get_row($sql,"ASSOC");
			$one = array_merge($one,$commodity);
			$scope_arr[$one['scope']][$one['cate_id']][] = $one;
		}
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$scope_arr);
	}
	
	function saveGoods($cate_id, $group_id, $data){
		//$subCateInfo = $this->subCateInfo($cate_id, $group_id);
		//if($subCateInfo['Flag'] != 100){
		//	return $subCateInfo;
		//}
		//$subCateInfo = $subCateInfo['Info'];
		$sql = "SELECT `cate_id` FROM ".DB_NAME_SHOP.".`goods_cate` where id = ".$cate_id." AND group_id = ".$group_id;
		$c_cate_id = $this->db->get_var($sql, "ASSOC");

        $module_id = domain::main()->GroupKeyVal($group_id,'module_id');
        $sql = "SELECT COUNT(*) FROM ".DB_NAME_TPL.".`commodity` WHERE tpl_id = ".$module_id." AND category = ".$c_cate_id;
        $exist = $this->platform_db->get_var($sql);
        
		if(!$exist){
			return array("Flag"=>102, "FlagString"=>"不存在改商品");
		}
		
		$sql = "SELECT price, expire,`type`,is_gift,scope FROM ".DB_NAME_TPL.".`commodity` WHERE id = ".$data['CommodityId'];
		$res = $this->platform_db->get_row($sql, "ASSOC");
		
		if($data['Id'] == $data['GiftGoodsId']){
			return array("Flag"=>102, "FlagString"=>"不能以同一个商品为礼物");
		}
		
		if($res['type'] == 1){
			if($data['Duration'] <= 0){
				return array("Flag"=>102, "FlagString"=>"使用时长必须大于0");
			}
			$price = $data['Price'];
			$min_price = ceil($res['price']/$res['expire'])*$data['Duration'];
		}elseif($res['type'] == 2){
			$price = $data['Price'];
			$min_price = ceil($res['price']);
		}
		
		if(!$res['is_gift']){
			$data['IsGift'] = 0;
		}
		
		if($price < $min_price){
			return array("Flag"=>102, "FlagString"=>"不得低于参考价");
		}
		
		if($data['IsGift'] && $data['GiftCateId'] && $data['GiftGoodsId']){
			$sql = "SELECT commodity_id FROM ".DB_NAME_SHOP.".`goods` WHERE id={$data['GiftGoodsId']}";
			if($row = $this->db->get_row($sql, "ASSOC")){
				$sql = "SELECT `scope` FROM ".DB_NAME_TPL.".`commodity` WHERE id={$row['commodity_id']}";
				$row['scope'] = $this->platform_db->get_var($sql);
			}
			if($row['scope'] != $res['scope']){
				return array("Flag"=>102, "FlagString"=>"礼物作用域需要相同");
			}
			$gift_arr = array(
					"gift_cate_id"=>$data['GiftCateId'],
					"gift_goods_id"=>$data['GiftGoodsId'],
					"commodity_id"=>$row['commodity_id']
					);
			$gift_json = json_encode($gift_arr);
		}
		
		if($data['Id']){
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`goods` WHERE id != ".$data['Id']." AND group_id = ".$group_id." AND cate_id={$cate_id} AND sub_cate_id={$data['SubCateId']} AND commodity_name = '".$data['CommodityName']."'";
			$count = $this->db->get_var($sql);
			if($count > 0){
				return array("Flag"=>102, "FlagString"=>"已存在相同名称商品");
			}
			$sql ="UPDATE ".DB_NAME_SHOP.".`goods` SET 
			`commodity_name` = '".$data['CommodityName']."' ,`desc` = '".$data['Content']."' ,`duration` = '".$data['Duration']."' ,
			`price` = '".$data['Price']."' ,`state` = '".$data['State']."',`sub_cate_id`=".intval($data['SubCateId']).",`is_gift` = '".$data['IsGift']."',`gift_detail` = '".$gift_json."' 
			WHERE `id` = '".$data['Id']."' AND group_id = '".$group_id."'; ";
			$done = $this->db->query($sql);
		}else{
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`goods` WHERE group_id = ".$group_id." AND cate_id={$cate_id} AND sub_cate_id={$data['SubCateId']} AND commodity_name = '".$data['CommodityName']."'";
			$count = $this->db->get_var($sql);
			if($count > 0){
				return array("Flag"=>102, "FlagString"=>"已存在相同名称商品");
			}
			$sql = "SELECT MAX(`order`) FROM ".DB_NAME_SHOP.".`goods` WHERE cate_id = ".$cate_id." AND group_id = ".$group_id;
			$max_order = $this->db->get_var($sql) + 1;
			$sql = "INSERT INTO ".DB_NAME_SHOP.".`goods` (`commodity_id`, `commodity_name`, `desc`, `duration`, `price`, `state`, `cate_id`, `sub_cate_id`, `order`, `group_id`,`is_gift`,`gift_detail`)
			VALUES ('".$data['CommodityId']."', '".$data['CommodityName']."', '".$data['Content']."', '".$data['Duration']."', '".$data['Price']."', '".$data['State']."', '".$cate_id."',".intval($data['SubCateId']).", '".$max_order."', '".$group_id."', '".$data['IsGift']."', '".$gift_json."');";
			$done = $this->db->query($sql);
		}
		
		if($done){
			return array("Flag"=>100, "FlagString"=>"操作成功");
		}else{
			return array("Flag"=>102, "FlagString"=>"操作失败");
		}
	}
	
	function savePackage($cate_id, $group_id, $data){
		$sql = "SELECT `cate_id` FROM ".DB_NAME_SHOP.".`goods_cate` where id = ".$cate_id." AND group_id = ".$group_id;
		$c_cate_id = $this->db->get_var($sql, "ASSOC");
		if($c_cate_id != -1){
			return array("Flag"=>102, "FlagString"=>"只能在套餐类下添加套餐包");
		}
		
        $module_id = domain::main()->GroupKeyVal($group_id,'module_id');
        $sql = "SELECT id FROM ".DB_NAME_TPL.".`commodity` WHERE tpl_id = ".$module_id;
        $content = $this->platform_db->get_results($sql);
        
		$ids = array();
		foreach($content as $one){
			$ids[] = $one['id'];
		}
		$sql = "SELECT `id`,commodity_id FROM ".DB_NAME_SHOP.".`goods` WHERE id IN (".join(",", $data['CommodityId']).")";
		$res = $this->db->get_results($sql, "ASSOC");
		$id_commodity = array();
		foreach($res as $one){
			$sql = "SELECT id, price, expire,`type` FROM ".DB_NAME_TPL.".`commodity` WHERE id = ".$one['commodity_id'];
			$id_commodity[$one['id']] = $this->platform_db->get_row($sql, "ASSOC");
		}
		$price = $data['Price'];
		$min_price = 0;
		$use_key = array();
		foreach($data['CommodityId'] as $k=>$one){
			if(!$data['Value'][$k] || $one <= 0 || !in_array($this->db->get_var("SELECT `commodity_id` from ".DB_NAME_SHOP.".goods WHERE id = ".$one), $ids)){
				continue;
			}
			if($id_commodity[$one]['type'] == 1){
				$min_price += ceil($id_commodity[$one]['price']/$id_commodity[$one]['expire'])*$data['Value'][$k];
			}elseif($id_commodity[$one]['type'] == 2){
				$min_price += ceil($id_commodity[$one]['price']*$data['Value'][$k]);
			}
			$use_key[] = $k;
		}
		if($min_price == 0){
			return array("Flag"=>102, "FlagString"=>"商品信息不完整");
		}
		if($min_price > $price){
			return array("Flag"=>102, "FlagString"=>"不得低于参考价");
		}
		
		$goods_arr = array();
		foreach($use_key as $k){
				$goods_arr[] = array("cate_id"=>$data['CateId'][$k], "commodity_id"=>$data['CommodityId'][$k], "value"=>$data['Value'][$k]);
		}
		if(!$data['Id']){
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`goods_package` WHERE group_id = ".$group_id." AND cate_id={$cate_id} AND sub_cate_id={$data['SubCateId']} AND package_name = '".$data['PackageName']."'";
			$count = $this->db->get_var($sql);
			if($count > 0){
				return array("Flag"=>102, "FlagString"=>"已存在相同名称商品");
			}
			
			$sql = "SELECT MAX(`order`) FROM ".DB_NAME_SHOP.".`goods_package` WHERE cate_id = ".$cate_id." AND sub_cate_id={$data['SubCateId']} AND group_id = ".$group_id;
			$max_order = $this->db->get_var($sql) + 1;
			
			$sql = "INSERT INTO ".DB_NAME_SHOP.".`goods_package` (`package_name`, `desc`, `scope`, `goods`, `price`, `original_price`, `package_img`, `state`, `order`, `cate_id`,`sub_cate_id`, `group_id`)
			VALUES ('".$data['PackageName']."', '".$data['Desc']."', '".$data['Scope']."', '".json_encode($goods_arr)."', '".$data['Price']."', '".$data['OriginalPrice']."', '".$data['Img']."', '".$data['State']."', '".$max_order."', '".$cate_id."',{$data['SubCateId']}, '".$group_id."');";
			$done = $this->db->query($sql);
		}else{
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_SHOP.".`goods_package` WHERE id != ".$data['Id']." AND group_id = ".$group_id." AND cate_id={$cate_id} AND sub_cate_id={$data['SubCateId']} AND package_name = '".$data['PackageName']."'";
			$count = $this->db->get_var($sql);
			if($count > 0){
				return array("Flag"=>102, "FlagString"=>"已存在相同名称商品");
			}
			if($data['Img']){
				$sql = "UPDATE ".DB_NAME_SHOP.".`goods_package` set `package_name` = '".$data['PackageName']."', `desc` = '".$data['Desc']."', `scope` = '".$data['Scope']."',
				`goods` = '".json_encode($goods_arr)."', `price` = '".$data['Price']."', `original_price` = '".$data['OriginalPrice']."',
				`package_img` = '".$data['Img']."', `state` = '".$data['State']."' WHERE `group_id` = ".$group_id." AND `id` = ".$data['Id'];
			}else{
				$sql = "UPDATE ".DB_NAME_SHOP.".`goods_package` set `package_name` = '".$data['PackageName']."', `desc` = '".$data['Desc']."', `scope` = '".$data['Scope']."',
				`goods` = '".json_encode($goods_arr)."', `price` = '".$data['Price']."', `original_price` = '".$data['OriginalPrice']."',
				`state` = '".$data['State']."' WHERE `group_id` = ".$group_id." AND `id` = ".$data['Id'];
			}
			
			$done = $this->db->query($sql);
		}
		
		if($done){
			return array("Flag"=>100, "FlagString"=>"操作成功");
		}else{
			return array("Flag"=>102, "FlagString"=>"操作失败");
		}
	}
	
	function goodsDetails($id, $group_id){
		$sql = "SELECT * FROM ".DB_NAME_SHOP.".goods WHERE id = ".$id." AND group_id = ".$group_id;
		$row = $this->db->get_row($sql, "ASSOC");
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$row);
	}
	
	function packageDetail($id, $group_id){
		$sql = "SELECT * FROM ".DB_NAME_SHOP.".goods_package WHERE id = ".$id." AND group_id = ".$group_id;
		$row = $this->db->get_row($sql, "ASSOC");
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$row);
	}

	private function cateInfo($cate_id,$group_id){
		$cate_id = intval($cate_id);
		$sql = "SELECT * FROM ".DB_NAME_SHOP.".goods_cate WHERE id={$cate_id} AND group_id={$group_id}";
		if($row = $this->db->get_row($sql)){
			return array('Flag'=>100, 'FlagString'=>'OK', 'Info'=>$row);
		}
		return array('Flag'=>101, 'FlagString'=>'不存在该分类');
	}
	
	protected function showPage($total, $perpage = 20) {
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