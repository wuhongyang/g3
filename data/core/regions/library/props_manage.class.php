<?php
/**
 * 后台道具操作类
 * @author sdq
 * @copyright aodiansoft.com
 * @version $Id$
 */
class props_manage extends Regions
{

	protected $db = null;
	
	public function __construct() {
		$this->db = db::connect(config('database','default'));
	}
	
	public function __destruct() {
		unset($this->db);
	}
	
	public function getOPenCity(){
		$open_citys = parent::getOpenCity();
		return array_merge(array('Flag'=>100,'FlagString'=>'成功'),(array)$open_citys);
	}
	
	public function props_add($array){
		$cate_id = $array['CateId'];
		$cmd = $array['Cmd'];
		$cmd_path = $array['CmdPath'];
		$big_case_id = $array['BigCaseId'];
		$case_id = $array['CaseId'];
		$parent_id = $array['ParentId'];
		$props_name = $array['PropsName'];
		$props_desc = $array['PropsDesc'];
		$props_size = $array['PropsSize'];
		$props_status = $array['PropsStatus'];
		$cat_id = $array['CatId'];
		$pic_id = $array['PicId'];
		$swf_cat_id = $array['SwfCatId'];
		$swf_pic_id = $array['SwfPicId'];
		$big_swf_cat_id = $array['BigSwfCatId'];
		$big_swf_pic_id = $array['BigSwfPicId'];
		$screen_size = $array['SCREENSIZE'];
		
		$props_money = $array["PropsMoney"];
		$tax_percent = $array["TaxPercent"];
		$receive_percent = $array["ReceivePercent"];
		$actor_tax = $array["ActorTax"];
		
		$is_prize = $array["IsPrize"];
		$value = array();
		foreach($array['ConfigName'] as $k=>$one){
			if($array['Key'][$k] && $array['Value'][$k]){
				$value[$array['Key'][$k]] = array("config_name"=>$one, "key"=>$array['Key'][$k], "value"=>$array['Value'][$k]);
			}
		}
		if($array['PoolPercent'] && $is_prize){
			$value['POOLPERCENT'] = array("config_name"=>"奖池比例", "key"=>"POOLPERCENT", "value"=>$array['PoolPercent']);
		}
		
		if(!empty($cmd_path) && !empty($big_case_id) && !empty($case_id) && !empty($parent_id) && !empty($props_name)) {
			$this->db->start_transaction();
			// $done2 = $this->config_save($big_case_id, $case_id, $parent_id, $props_money, $tax_percent, $receive_percent, $actor_tax, $props_status);
			$sql = "INSERT INTO ".DB_NAME_REGION.".`tbl_props` (`cmd`, `cmd_path`, `big_case_id`, `case_id`, `parent_id`, `province_id`, `city_id`, `area_id`, `region_id`, `props_name`, `props_desc`, `props_size`, `screen_size`, `cat_id`, `pic_id`, `swf_cat_id`, `swf_pic_id`, `big_swf_cat_id`, `big_swf_pic_id`, `props_status`, `props_order`, `uptime`, `cate_id`, `props_money`, `tax_percent`, `receive_percent`, `actor_tax`, `is_prize`, `value`)
			 VALUES ('".$cmd."', '".$cmd_path."', '".$big_case_id."', '".$case_id."', '".$parent_id."', '0', '0', '0', '0', '".$props_name."', '".$props_desc."', '".$props_size."', '".$screen_size."', '".$cat_id."', '".$pic_id."', '".$swf_cat_id."', '".$swf_pic_id."', '".$big_swf_cat_id."', '".$big_swf_pic_id."', '".$props_status."', '0', '".time()."', '".$cate_id."', '".$props_money."', '".$tax_percent."', '".$receive_percent."', '".$actor_tax."', '".$is_prize."', '".addslashes(json_encode($value))."'); ";
			$done1 = $this->db->query($sql);
			if($done1) {
				$sql = 'UPDATE '.DB_NAME_REGION.'.tbl_props SET props_order='.$this->db->insert_id().' WHERE id='.$this->db->insert_id();
				$this->db->query($sql);
				$this->db->commit();
				$result = array("Flag"=>100,"FlagString"=>"添加成功");
			} else {
				$this->db->rollback();
				$result = array("Flag"=>101,"FlagString"=>"添加失败");
			}
		} else {
			$result = array("Flag"=>102,"FlagString"=>"参数错误");
		}
		return $result;
	}
	
	public function props_update($array) {
		$id = $array['Id'];
		$cate_id = $array['CateId'];
		$cmd = $array['Cmd'];
		$cmd_path = $array['CmdPath'];
		$big_case_id = $array['BigCaseId'];
		$case_id = $array['CaseId'];
		$parent_id = $array['ParentId'];
		$props_name = $array['PropsName'];
		$props_desc = $array['PropsDesc'];
		$props_size = $array['PropsSize'];
		$props_status = $array['PropsStatus'];
		$cat_id = $array['CatId'];
		$pic_id = $array['PicId'];
		$swf_cat_id = $array['SwfCatId'];
		$swf_pic_id = $array['SwfPicId'];
		$big_swf_cat_id = $array['BigSwfCatId'];
		$big_swf_pic_id = $array['BigSwfPicId'];
		$screen_size = $array['SCREENSIZE'];
		
		$props_money = $array["PropsMoney"];
		$tax_percent = $array["TaxPercent"];
		$receive_percent = $array["ReceivePercent"];
		$actor_tax = $array["ActorTax"];
		
		$is_prize = $array["IsPrize"];
		$value = array();
		foreach($array['ConfigName'] as $k=>$one){
			if($array['Key'][$k] && $array['Value'][$k]){
				$value[$array['Key'][$k]] = array("config_name"=>$one, "key"=>$array['Key'][$k], "value"=>$array['Value'][$k]);
			}
		}
		if($array['PoolPercent'] && $is_prize){
			$value['POOLPERCENT'] = array("config_name"=>"奖池比例", "key"=>"POOLPERCENT", "value"=>$array['PoolPercent']);
		}
		
		if(!empty($id) && !empty($cmd_path) && !empty($big_case_id) && !empty($case_id) && !empty($parent_id) && !empty($props_name) && !empty($cate_id)) {
			$sql = 'UPDATE '.DB_NAME_REGION.'.tbl_props SET `cmd`="'.$cmd.'",`cmd_path`="'.$cmd_path.'",`big_case_id`="'.$big_case_id.'",`case_id`="'.$case_id.'",`parent_id`="'.$parent_id.'",`props_name`="'.$props_name.'",`props_desc`="'.$props_desc.'",`props_size`="'.$props_size.'",`cat_id`="'.$cat_id.'",`pic_id`="'.$pic_id.'",`swf_cat_id`="'.$swf_cat_id.'",`swf_pic_id`="'.$swf_pic_id.'",`big_swf_cat_id`="'.$big_swf_cat_id.'",`big_swf_pic_id`="'.$big_swf_pic_id.'",`props_status`="'.$props_status.'",`uptime`="'.time().'",`screen_size`="'.$screen_size.'",`cate_id`="'.$cate_id.'",`props_money`="'.$props_money.'",`tax_percent`="'.$tax_percent.'",`receive_percent`="'.$receive_percent.'",`actor_tax`="'.$actor_tax.'", `is_prize`="'.$is_prize.'", `value`=\''.addslashes(json_encode($value)).'\' WHERE `id`="'.$id.'";';
			$this->db->start_transaction();
			$done1 = $this->db->query($sql);
			// $done2 = $this->config_save($big_case_id, $case_id, $parent_id, $props_money, $tax_percent, $receive_percent, $actor_tax, $props_status);
			if($done1) {
				$this->db->commit();
				$result = array("Flag"=>100,"FlagString"=>"更新成功");
			} else {
				$this->db->rollback();
				$result = array("Flag"=>101,"FlagString"=>"更新失败");
			}
		} else {
			$result = array("Flag"=>102,"FlagString"=>"参数错误");
		}
		return $result;
	}
	
	/**已失效**/
	private function config_save($big_case_id, $case_id, $parent_id, $props_money, $tax_percent, $receive_percent, $actor_tax, $props_status){
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_SYSTEM_CONFIG.".`props_config` WHERE bigcase_id = ".$big_case_id." AND case_id = ".$case_id." AND parent_id = ".$parent_id;
		$count = $this->db->get_var($sql);
		if($count == 0){
			$sql = "SELECT parent_name FROM ".DB_NAME_CCS.".`tbl_parent` WHERE parent_id = ".$parent_id;
			$parent_name = $this->db->get_var($sql);
			$sql = "INSERT INTO ".DB_NAME_SYSTEM_CONFIG.".`props_config` (`bigcase_id`, `case_id`, `parent_id`, `parent_name`, `props_money`, `tax_percent`, `receive_percent`, `status`, `uptime`)
			 VALUES ('".$big_case_id."', '".$case_id."', '".$parent_id."', '".$parent_name."', '".$props_money."', '".$tax_percent."', '".$receive_percent."', '".$props_status."', '".time()."'); ";
		}else{
			$sql = "UPDATE ".DB_NAME_SYSTEM_CONFIG.".`props_config` SET `props_money` = '".$props_money."' ,`tax_percent` = '".$tax_percent."' ,`receive_percent` = '".$receive_percent."' ,`actor_tax` = '".$actor_tax."' ,`status` = '".$props_status."' ,`uptime` = '".time()."'
			 WHERE  bigcase_id = ".$big_case_id." AND case_id = ".$case_id." AND parent_id = ".$parent_id;
		}
		return $this->db->query($sql);
	}
	
	public function props_list($data,$id=''){
        if(isset($data['idin'])){
            $where .= " AND p.id IN({$data['idin']})";
            $order = " ORDER BY INSTR('{$data['idin']}',p.id)";
        }else{
            $order = " ORDER BY p.props_order DESC";
        }
		if($data['big_case_id'] > 0){
			$data['big_case_id'] = intval($data['big_case_id']);
			$where .= ' AND p.big_case_id="'.$data['big_case_id'].'"';
		}
		if($data['case_id'] > 0){
			$data['case_id'] = intval($data['case_id']);
			$where .= ' AND p.case_id="'.$data['case_id'].'"';
		}
		if($data['parent_id'] > 0){
			$data['parent_id'] = intval($data['parent_id']);
			$where .= ' AND p.parent_id="'.$data['parent_id'].'"';
		}
		if(isset($data['props_size']) && $data['props_size'] > -1){
			$where .= ' AND p.props_size="'.intval($data['props_size']).'"';
		}
		if(isset($data['props_status']) && intval($data['props_status']) != -1){
			$where .= ' AND p.props_status="'.intval($data['props_status']).'"';
		}
		if(!empty($data['cate_id']) && $data['cate_id'] > -1){
			$where .= ' AND p.cate_id="'.intval($data['cate_id']).'"';
		}
        if($data['is_tricky'] && $data['tpl_id']){
            $cate_id = $this->tricky_cate_id($data['tpl_id'], $data['is_tricky']);
            $where .= ' AND p.cate_id="'.$cate_id.'"';
        }
        
		if(!empty($id)){
			$where .= " AND p.id='".$id."'";
		}
		if(!empty($where)){
			$where = ' WHERE '.ltrim($where,' AND ');
		}
        
		$total = $this->db->get_var('SELECT COUNT(p.id) FROM '.DB_NAME_REGION.'.tbl_props AS p LEFT JOIN '.DB_NAME_SYSTEM_CONFIG.'.pic_manager AS m ON p.pic_id=m.id'.$where.';');
		$sql = 'SELECT p.*,m.img_path FROM '.DB_NAME_REGION.'.tbl_props AS p LEFT JOIN '.DB_NAME_SYSTEM_CONFIG.'.pic_manager AS m ON p.pic_id=m.id  '.$where.$order;
        $list = $this->db->get_results($sql,'ASSOC');
        $sql = "SELECT * FROM ".DB_NAME_TPL.".`tbl_gift_cate`";
        $cate_list = $this->db->get_results($sql, "ASSOC");
        $cate_arr = array();
        foreach($cate_list as $value){
        	$cate_arr[$value['cate_id']] = $value['cate_name'];
        }
        
		foreach((array)$list as $key => $row){
			$list[$key]['gif_img_path'] = cdn_url(PIC_API_PATH.'/p/'.$row['img_path'].'/0/0.swf');
			$sql = 'SELECT m.img_path FROM '.DB_NAME_REGION.'.tbl_props AS p LEFT JOIN '.DB_NAME_SYSTEM_CONFIG.'.pic_manager AS m ON p.swf_pic_id=m.id WHERE p.id='.$row['id'];
			$img_path = $this->db->get_var($sql);
			$list[$key]['swf_img_path'] = cdn_url(PIC_API_PATH.'/p/'.$img_path.'/0/0.swf');
			$list[$key]['cate_name'] = $cate_arr[$row['cate_id']];
			if($row['props_size'] > 0){
                $sql = 'SELECT m.img_path FROM '.DB_NAME_REGION.'.tbl_props AS p LEFT JOIN '.DB_NAME_SYSTEM_CONFIG.'.pic_manager AS m ON p.big_swf_pic_id=m.id WHERE p.id='.$row['id'];
                $img_path = $this->db->get_var($sql);
                if($img_path){
                    $list[$key]['big_swf_img_path'] = cdn_url(PIC_API_PATH.'/p/'.$img_path.'/0/0.swf');
                }
            }
            
            $value = json_decode($row['value'], true);
            $list[$key]['pool_percent'] = $value['POOLPERCENT']['value'];
            unset($value['POOLPERCENT']);
            $list[$key]['value'] = json_encode($value);
		}
		return empty($id) ? array('Flag'=>100,'FlagString'=>'成功','list'=>$list,'total'=>$total,'cate_id'=>$cate_id) : array('Flag'=>100,'FlagString'=>'成功','list'=>$list[0],'total'=>$total,'cate_id'=>$cate_id);	
	}
    
    private function tricky_cate_id($tpl_id, $tricky){
        $sql     = "SELECT cate_id FROM ".DB_NAME_TPL.".`tbl_gift_cate` WHERE tpl_id = ".$tpl_id." AND is_tricky = ".$tricky;
        $cate_id = $this->db->get_var($sql);
        
        if($cate_id){
            return $cate_id;
        }
        if($tricky == 1){
            $name = "整蛊道具";
        }elseif($tricky == 2){
            $name = "沙发";
        }
        $sql = "INSERT INTO ".DB_NAME_TPL.".`tbl_gift_cate` (`cate_name`, `status`, `order`, `tpl_id`, `is_tricky`) VALUES ('".$name."', '1', '0', '".$tpl_id."', '".$tricky."'); ";
        if(!$this->db->query($sql)){
            return 0;
        }
        
        return $this->db->insert_id();
    }
	
	public function props_del($id) {
		if(!empty($id)) {
			$sql = 'DELETE FROM '.DB_NAME_REGION.'.tbl_props WHERE id="'.$id.'"';
			if($this->db->query($sql)) {
				$result = array("Flag"=>100,"FlagString"=>"删除成功");
			} else {
				$result = array("Flag"=>101,"FlagString"=>"删除失败");
			}
		} else {
			$result = array("Flag"=>102,"FlagString"=>"参数错误");
		}
		return $result;
	}
	
	//礼物排序，置顶
	function props_order($id,$type){
		switch($type){
			case 'up':
				$order  = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_REGION.".tbl_props WHERE id={$id}",'ASSOC');
				$switch = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_REGION.".tbl_props WHERE props_order>{$order['props_order']} ORDER BY props_order ASC LIMIT 1",'ASSOC');
				break;
			case 'down':
				$order  = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_REGION.".tbl_props WHERE id={$id}",'ASSOC');
				$switch = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_REGION.".tbl_props WHERE props_order<{$order['props_order']} ORDER BY props_order DESC LIMIT 1",'ASSOC');
				break;
			case 'top':
				$order  = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_REGION.".tbl_props WHERE id={$id}",'ASSOC');
				$switch = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_REGION.".tbl_props ORDER BY props_order DESC LIMIT 1","ASSOC");
				break;
		}
		if(empty($switch)) return array('Flag'=>102,'FlagString'=>'已经到顶端或底端');
		$set1 = "UPDATE ".DB_NAME_REGION.".tbl_props SET props_order={$switch['props_order']} WHERE id={$order['id']}";
		$set2 = "UPDATE ".DB_NAME_REGION.".tbl_props SET props_order={$order['props_order']} WHERE id={$switch['id']}";
		
		if( ! $this->db->query($set1) || !$this->db->query($set2)){
			return array('Flag'=>103,'FlagString'=>'排序失败');
		}else{
			return array('Flag'=>100,'FlagString'=>'成功');
		}
	}
	
	
	function save_gift_cate($name,$status,$id=0,$tpl_id){
		$name = addslashes(htmlspecialchars($name));
		if(!in_array($status, array(0, 1))){
			return array("Flag"=>102,"FlagString"=>"参数错误");
		}
		if($id==0){
            if(!$tpl_id){
                return array("Flag"=>102,"FlagString"=>"参数错误");
            }
            $sql = "SELECT MAX(`order`) FROM ".DB_NAME_TPL.".`tbl_gift_cate` WHERE tpl_id = ".$tpl_id;
            $max_order = $this->db->get_var($sql);
			$sql = "INSERT INTO ".DB_NAME_TPL.".`tbl_gift_cate` (`cate_name`, `status`, `tpl_id`, `order`) VALUES ('".$name."', '".$status."', '".$tpl_id."', '".($max_order+1)."');";
			if($this->db->query($sql)){
				return array('Flag'=>100,'FlagString'=>'添加成功');
			}else{
				return array('Flag'=>102,'FlagString'=>'添加失败，确认是否存在相同名称分类');
			}	
		}else{
			$id = intval($id);
			$sql = "UPDATE ".DB_NAME_TPL.".`tbl_gift_cate` SET `cate_name` = '".$name."' ,`status` = '".$status."' WHERE `cate_id` = '".$id."';";
            if($this->db->query($sql)){
				return array('Flag'=>100,'FlagString'=>'更新成功');
			}else{
				return array('Flag'=>102,'FlagString'=>'更新失败，确认是否存在相同名称分类');
			}
		}
	}
	
	function gift_cate_list($id=0, $no_page=false, $tpl_id, $is_tricky){
	    if($tpl_id){
	       $where = " AND tpl_id = ".$tpl_id;
	    }
        if($is_tricky){
            $where .= " AND is_tricky = ".$is_tricky;
        }else{
            $where .= " AND is_tricky = 0";
        }
		if($id == 0 && !$no_page){
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_TPL.".`tbl_gift_cate` WHERE 1 ".$where;
			$total = $this->db->get_var($sql);
			$res = array();
			if($total > 0){
				$page_arr = $this->showPage($total);
				$sql = "SELECT * FROM ".DB_NAME_TPL.".`tbl_gift_cate` WHERE 1 ".$where." ORDER BY `order` LIMIT ".$page_arr['limit'];
				$res = $this->db->get_results($sql, "ASSOC");
				$sql = "SELECT ";
				foreach($res as $one){
					$sql .= "(SELECT COUNT(`cate_id`) FROM ".DB_NAME_REGION.".`tbl_props` WHERE cate_id = ".$one['cate_id'].") as c".$one['cate_id'].",";
				}
			
				$row = $this->db->get_row(substr($sql, 0, -1), "ASSOC");
				foreach($res as $k=>$one){
					$res[$k]['count'] = $row["c".$one['cate_id']];
				}
			}
			
			return array('Flag'=>100, 'FlagString'=>'查询成功', 'Page'=>$page_arr['page'], 'List'=>$res);
		}elseif(!$no_page){
			$id = intval($id);
			$sql = "SELECT * FROM ".DB_NAME_TPL.".`tbl_gift_cate` WHERE cate_id = ".$id;
			$row = $this->db->get_row($sql);
			return array('Flag'=>100, 'FlagString'=>'查询成功', 'List'=>$row);
		}elseif($no_page){
			$sql = "SELECT * FROM ".DB_NAME_TPL.".`tbl_gift_cate` WHERE 1 ".$where." ORDER BY `order`";
			$res = $this->db->get_results($sql, "ASSOC");
			return array('Flag'=>100, 'FlagString'=>'查询成功', 'List'=>$res);
		}
	}
    
    function gift_cate_move($move_id, $direct){
        //获取移动分类和对应移动分类的信息
        $sql = "SELECT tpl_id,`order` as move_order FROM ".DB_NAME_TPL.".`tbl_gift_cate` WHERE cate_id = ".$move_id;
        extract($this->db->get_row($sql, "ASSOC"));
        
        $sql = "SELECT MAX(`order`) AS `max_order`, MIN(`order`) AS `min_order` FROM ".DB_NAME_TPL.".`tbl_gift_cate` WHERE tpl_id = ".$tpl_id." AND is_tricky = 0";
        extract($this->db->get_row($sql, "ASSOC"));
        
        if($move_order == $max_order && $direct == "down"){
            return array("Flag"=>102, "FlagString"=>"已在最下,无法移动");
        }
        if($move_order == $min_order && $direct == "up"){
            return array("Flag"=>102, "FlagString"=>"已在最上,无法移动");
        }
        
        $symbol = $direct == "down" ? ">" : "<";
        $sort   = $direct == "down" ? "asc" : "desc";
        $sql = "SELECT cate_id AS exchange_id,`order` AS exchange_order FROM ".DB_NAME_TPL.".`tbl_gift_cate` WHERE `order` ".$symbol." ".$move_order." AND is_tricky = 0 AND tpl_id = ".$tpl_id." ORDER by `order` ".$sort." LIMIT 1";
        extract($this->db->get_row($sql, "ASSOC"));
        
        //对换
        $this->db->start_transaction();
        $sql1 = "UPDATE ".DB_NAME_TPL.".`tbl_gift_cate` SET `order` = '".$move_order."' WHERE `cate_id` = '".$exchange_id."';";
        $sql2 = "UPDATE ".DB_NAME_TPL.".`tbl_gift_cate` SET `order` = '".$exchange_order."' WHERE `cate_id` = '".$move_id."';";
        if($this->db->query($sql1) && $this->db->query($sql2)){
            $this->db->commit();
            return array("Flag"=>100, "FlagString"=>"移动成功");
        }else{
            $this->db->rollback();
            return array("Flag"=>102, "FlagString"=>"移动失败");
        }
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
?>