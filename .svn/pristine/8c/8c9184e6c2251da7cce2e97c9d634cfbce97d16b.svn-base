<?php
/**
 * 后台道具操作类
 * @author sdq
 * @copyright aodiansoft.com
 * @version $Id$
 */
class FunctionProps extends Regions
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
	
	public function function_props_add($array){
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
        $tpl_id = $array['TplId'];
		
		$props_money = $array['PropsMoney'];
		$tax_percent = $array['TaxPercent'];
		$receive_percent = $array['ReceivePercent'];
		$actor_tax = $array['ActorTax'];
		
		if(!empty($cmd_path) && !empty($big_case_id) && !empty($case_id) && !empty($parent_id) && !empty($props_name)) {
			$sql = 'INSERT INTO '.DB_NAME_TPL.'.tbl_function_props(cmd,cmd_path,big_case_id,case_id,parent_id,props_name,props_desc,props_size,cat_id,pic_id,swf_cat_id,swf_pic_id,props_status,uptime,props_money,tax_percent,receive_percent,actor_tax,tpl_id) VALUES("'.$cmd.'","'.$cmd_path.'","'.$big_case_id.'","'.$case_id.'","'.$parent_id.'","'.$props_name.'","'.$props_desc.'","'.$props_size.'","'.$cat_id.'","'.$pic_id.'","'.$swf_cat_id.'","'.$swf_pic_id.'","'.$props_status.'","'.time().'","'.$props_money.'","'.$tax_percent.'","'.$receive_percent.'","'.$actor_tax.'","'.$tpl_id.'");';
			if($this->db->query($sql)) {
				$sql = 'UPDATE '.DB_NAME_TPL.'.tbl_function_props SET props_order='.$this->db->insert_id().' WHERE id='.$this->db->insert_id();
				$this->db->query($sql);
				$result = array("Flag"=>100,"FlagString"=>"添加成功");
			} else {
				$result = array("Flag"=>101,"FlagString"=>"添加失败");
			}
		} else {
			$result = array("Flag"=>102,"FlagString"=>"参数错误");
		}
		return $result;
	}
	
	public function function_props_update($array) {
		$id = $array['Id'];
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
        $SelectUser = (int)$array['SelectUser'];
        
        $props_money = $array['PropsMoney'];
        $tax_percent = $array['TaxPercent'];
        $receive_percent = $array['ReceivePercent'];
        $actor_tax = $array['ActorTax'];
        
		if(!empty($id) && !empty($cmd_path) && !empty($big_case_id) && !empty($case_id) && !empty($parent_id) && !empty($props_name)) {
			$sql = 'UPDATE '.DB_NAME_TPL.'.tbl_function_props SET `select_user`="'.$SelectUser.'",`cmd`="'.$cmd.'",`cmd_path`="'.$cmd_path.'",`big_case_id`="'.$big_case_id.'",`case_id`="'.$case_id.'",`parent_id`="'.$parent_id.'",`props_name`="'.$props_name.'",`props_desc`="'.$props_desc.'",`props_size`="'.$props_size.'",`cat_id`="'.$cat_id.'",`pic_id`="'.$pic_id.'",`swf_cat_id`="'.$swf_cat_id.'",`swf_pic_id`="'.$swf_pic_id.'",`props_status`="'.$props_status.'",`uptime`="'.time().'",`props_money` = "'.$props_money.'" ,`tax_percent` = "'.$tax_percent.'" ,`receive_percent` = "'.$receive_percent.'" ,`actor_tax` = "'.$actor_tax.'"  WHERE `id`="'.$id.'";';
			if($this->db->query($sql)) {
				$result = array("Flag"=>100,"FlagString"=>"更新成功");
			} else {
				$result = array("Flag"=>101,"FlagString"=>"更新失败");
			}
		} else {
			$result = array("Flag"=>102,"FlagString"=>"参数错误");
		}
		return $result;
	}
	
	public function function_props_list($data,$id=''){
        if(isset($data['idin'])){
            $where .= " AND p.id IN({$data['idin']})";
            $order = " ORDER BY INSTR('{$data['idin']}',p.id)";
        }else{
            $order = " ORDER BY p.props_order DESC";
        }
		if($data['province']!=-1 && !empty($data['province'])){
			$where .= ' AND p.province_id="'.$data['province'].'"';
		}
		if($data['city']!=-1 && !empty($data['city'])){
			$where .= ' AND p.city_id="'.$data['city'].'"';
		}
		if($data['area']!=-1 && !empty($data['area'])){
			$where .= ' AND p.area_id="'.$data['area'].'"';
		}
		if(is_numeric($data['region_id'])){
			if($data['region_id'] == 110100){ //todo 如果是北京站，去除导航站礼物
				$where .= ' AND (p.region_id="'.$data['region_id'].'")';
			}else{
				$where .= ' AND (p.region_id="'.$data['region_id'].'" OR p.region_id="0")';
			}
		}
		if(intval($data['props_size'])!=0){
			$where .= ' AND p.props_size="'.intval($data['props_size']).'"';
		}
		if(isset($data['props_status']) && intval($data['props_status']) != -1){
			$where .= ' AND p.props_status="'.intval($data['props_status']).'"';
		}
		if(!empty($data['cmd'])){
			$where .= " AND p.cmd='{$data['cmd']}'";
		}
        if($data['tpl_id']){
            $where .= " AND p.tpl_id = ".$data['tpl_id'];
        }
		if(!empty($id)){
			$where .= " AND p.id='".$id."'";
		}
		if(!empty($where)){
			$where = " WHERE ".ltrim($where,' AND ');
		}
		$total = $this->db->get_var('SELECT COUNT(p.id) FROM '.DB_NAME_TPL.'.tbl_function_props AS p LEFT JOIN '.DB_NAME_SYSTEM_CONFIG.'.pic_manager AS m ON p.pic_id=m.id'.$where.';');
		$sql = 'SELECT p.*,m.img_path FROM '.DB_NAME_TPL.'.tbl_function_props AS p LEFT JOIN '.DB_NAME_SYSTEM_CONFIG.'.pic_manager AS m ON p.pic_id=m.id '.$where.$order;
		$list = $this->db->get_results($sql,'ASSOC');
		foreach((array)$list as $key => $row){
			$list[$key]['gif_img_path'] = cdn_url(PIC_API_PATH.'/p/'.$row['img_path'].'/0/0.swf');
		}
		$sql = 'SELECT m.img_path FROM '.DB_NAME_TPL.'.tbl_function_props AS p LEFT JOIN '.DB_NAME_SYSTEM_CONFIG.'.pic_manager AS m ON p.swf_pic_id=m.id'.$where.$order;
		$swf_list = $this->db->get_results($sql,'ASSOC');
		foreach((array)$swf_list as $key => $row){
			$list[$key]['swf_img_path'] = cdn_url(PIC_API_PATH.'/p/'.$row['img_path'].'/0/0.swf');
		}
		return empty($id) ? array('Flag'=>100,'FlagString'=>'成功','list'=>$list,'total'=>$total) : array('Flag'=>100,'FlagString'=>'成功','list'=>$list[0],'total'=>$total);	
	}
	
	public function function_props_del($id) {
		if(!empty($id)) {
			$sql = 'DELETE FROM '.DB_NAME_TPL.'.tbl_function_props WHERE id="'.$id.'"';
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
	function function_props_order($id,$type){
		switch($type){
			case 'up':
				$order  = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_TPL.".tbl_function_props WHERE id={$id}",'ASSOC');
				$switch = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_TPL.".tbl_function_props WHERE props_order>{$order['props_order']} ORDER BY props_order ASC LIMIT 1",'ASSOC');
				break;
			case 'down':
				$order  = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_TPL.".tbl_function_props WHERE id={$id}",'ASSOC');
				$switch = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_TPL.".tbl_function_props WHERE props_order<{$order['props_order']} ORDER BY props_order DESC LIMIT 1",'ASSOC');
				break;
			case 'top':
				$order  = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_TPL.".tbl_function_props WHERE id={$id}",'ASSOC');
				$switch = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_TPL.".tbl_function_props ORDER BY props_order DESC LIMIT 1","ASSOC");
				break;
		}
		if(empty($switch)) return array('Flag'=>102,'FlagString'=>'已经到顶端或底端');
		$set1 = "UPDATE ".DB_NAME_TPL.".tbl_function_props SET props_order={$switch['props_order']} WHERE id={$order['id']}";
		$set2 = "UPDATE ".DB_NAME_TPL.".tbl_function_props SET props_order={$order['props_order']} WHERE id={$switch['id']}";
		
		if( ! $this->db->query($set1) || !$this->db->query($set2)){
			return array('Flag'=>103,'FlagString'=>'排序失败');
		}else{
			return array('Flag'=>100,'FlagString'=>'成功');
		}
	}
	
}
?>