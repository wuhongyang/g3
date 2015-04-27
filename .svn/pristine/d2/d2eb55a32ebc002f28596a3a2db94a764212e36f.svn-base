<?php
/**
 * 后台道具操作类
 * @author sdq
 * @copyright aodiansoft.com
 * @version $Id$
 */
class GameProps extends Regions
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
	
	public function game_props_add($array){
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
		$props_ico = $array['PropsIco'];
        $tpl_id = $array['TplId'];
		if(!empty($cmd_path) && !empty($big_case_id) && !empty($case_id) && !empty($parent_id) && !empty($props_name)) {
			$sql = 'INSERT INTO '.DB_NAME_TPL.'.tbl_game_props(cmd,cmd_path,big_case_id,case_id,parent_id,props_name,props_desc,props_size,cat_id,pic_id,props_ico,props_status,uptime,tpl_id) VALUES("'.$cmd.'","'.$cmd_path.'","'.$big_case_id.'","'.$case_id.'","'.$parent_id.'","'.$props_name.'","'.$props_desc.'","'.$props_size.'","'.$cat_id.'","'.$pic_id.'","'.$props_ico.'","'.$props_status.'","'.time().'","'.$tpl_id.'");';
			if($this->db->query($sql)) {
				$sql = 'UPDATE '.DB_NAME_TPL.'.tbl_game_props SET props_order='.$this->db->insert_id().' WHERE id='.$this->db->insert_id();
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
	
	public function game_props_update($array) {
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
		$props_ico = $array['PropsIco'];
		if(!empty($id) && !empty($cmd_path) && !empty($big_case_id) && !empty($case_id) && !empty($parent_id) && !empty($props_name)) {
			$sql = 'UPDATE '.DB_NAME_TPL.'.tbl_game_props SET `cmd`="'.$cmd.'",`cmd_path`="'.$cmd_path.'",`big_case_id`="'.$big_case_id.'",`case_id`="'.$case_id.'",`parent_id`="'.$parent_id.'",`props_name`="'.$props_name.'",`props_desc`="'.$props_desc.'",`props_size`="'.$props_size.'",`cat_id`="'.$cat_id.'",`pic_id`="'.$pic_id.'",`props_ico`="'.$props_ico.'",`props_status`="'.$props_status.'",`uptime`="'.time().'" WHERE `id`="'.$id.'";';
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
	
	public function game_props_list($data,$id=''){
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
			$where .= ' AND (p.region_id="'.$data['region_id'].'" OR p.region_id="0")';
		}
		if(intval($data['props_size'])!=0){
			$where .= ' AND p.props_size="'.intval($data['props_size']).'"';
		}
		if(isset($data['props_status']) && intval($data['props_status']) != -1){
			$where .= ' AND p.props_status="'.intval($data['props_status']).'"';
		}
        if($data['tpl_id']){
            $where .= " AND p.tpl_id = ".$data['tpl_id'];
        }
		if(!empty($id)){
			$where .= " and p.id='".$id."'";
		}
		$total = $this->db->get_var('SELECT COUNT(p.id) FROM '.DB_NAME_TPL.'.tbl_game_props AS p LEFT JOIN '.DB_NAME_SYSTEM_CONFIG.'.pic_manager AS m ON p.pic_id=m.id WHERE TRUE'.$where.';');
		$sql = 'SELECT p.*,m.img_path FROM '.DB_NAME_TPL.'.tbl_game_props AS p LEFT JOIN '.DB_NAME_SYSTEM_CONFIG.'.pic_manager AS m ON p.pic_id=m.id WHERE TRUE'.$where.$order;
		$list = $this->db->get_results($sql,'ASSOC');
		foreach((array)$list as $key => $row){
			$list[$key]['img_path'] = cdn_url(PIC_API_PATH.'/p/'.$row['img_path'].'/0/0.jpg');
		}
		return empty($id) ? array('Flag'=>100,'FlagString'=>'成功','list'=>$list,'total'=>$total) : array('Flag'=>100,'FlagString'=>'成功','list'=>$list[0],'total'=>$total);	
	}
	
	public function game_props_del($id) {
		if(!empty($id)) {
			$sql = 'DELETE FROM '.DB_NAME_TPL.'.tbl_game_props WHERE id="'.$id.'"';
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
	function game_props_order($id,$type){
		switch($type){
			case 'up':
				$order  = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_TPL.".tbl_game_props WHERE id={$id}",'ASSOC');
				$switch = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_TPL.".tbl_game_props WHERE props_order>{$order['props_order']} ORDER BY props_order ASC LIMIT 1",'ASSOC');
				break;
			case 'down':
				$order  = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_TPL.".tbl_game_props WHERE id={$id}",'ASSOC');
				$switch = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_TPL.".tbl_game_props WHERE props_order<{$order['props_order']} ORDER BY props_order DESC LIMIT 1",'ASSOC');
				break;
			case 'top':
				$order  = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_TPL.".tbl_game_props WHERE id={$id}",'ASSOC');
				$switch = $this->db->get_row("SELECT id,props_order FROM ".DB_NAME_TPL.".tbl_game_props ORDER BY props_order DESC LIMIT 1","ASSOC");
				break;
		}
		if(empty($switch)) return array('Flag'=>102,'FlagString'=>'已经到顶端或底端');
		$set1 = "UPDATE ".DB_NAME_TPL.".tbl_game_props SET props_order={$switch['props_order']} WHERE id={$order['id']}";
		$set2 = "UPDATE ".DB_NAME_TPL.".tbl_game_props SET props_order={$order['props_order']} WHERE id={$switch['id']}";
		
		if( ! $this->db->query($set1) || !$this->db->query($set2)){
			return array('Flag'=>103,'FlagString'=>'排序失败');
		}else{
			return array('Flag'=>100,'FlagString'=>'成功');
		}
	}
	
	public function game_props_config($cmd){
		if(empty($cmd)) return array("Flag"=>102,"FlagString"=>"参数错误");
		$sql = "SELECT * FROM ".DB_NAME_PROPS.".props_config WHERE `key`='{$cmd}_config'";
		$result = $this->db->get_row($sql,'ASSOC');
		$result['value'] = unserialize($result['value']);
		$result['descr'] = unserialize($result['descr']);
		return array('Flag'=>100,'FlagString'=>'成功','Result'=>$result);
	}
	
	public function gamePropsConfigModify($data){
		if(empty($data['key'])) return array("Flag"=>102,"FlagString"=>"参数错误");
		$value = serialize($data['value']);
		$descr = serialize($data['descr']);
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_PROPS.".props_config WHERE `key`='{$data['key']}_config'";
		if($this->db->get_var($sql) > 0){
			$sql = "UPDATE ".DB_NAME_PROPS.".props_config SET `value`='{$value}',`descr`='{$descr}',`status`='{$data['status']}' WHERE `key`='{$data['key']}_config'";
		}else{
			$sql = "INSERT INTO ".DB_NAME_PROPS.".props_config(`key`,`value`,`descr`,`funduin`,`status`) VALUES('{$data['key']}_config','{$value}','{$descr}','0','{$data['status']}')";
		}
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'配置成功');
		}
		return array('Flag'=>102,'FlagString'=>'配置失败');
	}
	
	public function gameMoneyBindConfig($cmd){
		if(empty($cmd)) return array("Flag"=>102,"FlagString"=>"参数错误");
		$sql = "SELECT funduin FROM ".DB_NAME_PROPS.".props_config WHERE `key`='{$cmd}_config'";
		$funduin = $this->db->get_var($sql);
		return array('Flag'=>100,'FlagString'=>'成功','Result'=>$funduin);
	}
	
	public function gameMoneyBindConfigSet($cmd,$funduin){
		if(empty($cmd)) return array("Flag"=>102,"FlagString"=>"参数错误");
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_PROPS.".props_config WHERE `key`='{$cmd}_config'";
		if($this->db->get_var($sql) > 0){
			$sql = "UPDATE ".DB_NAME_PROPS.".props_config SET funduin='{$funduin}' WHERE `key`='{$cmd}_config'";
			if($this->db->query($sql))
				return array('Flag'=>100,'FlagString'=>'设置成功');
		}
		return array('Flag'=>102,'FlagString'=>'设置失败');
	}
	
	public function gamePropsInfoByCmd($cmd){
		if(empty($cmd)) return array("Flag"=>102,"FlagString"=>"参数错误");
		$sql = "SELECT * FROM ".DB_NAME_TPL.".tbl_game_props WHERE cmd='{$cmd}' AND props_status=1";
		$props_arr = $this->db->get_row($sql,'ASSOC');
		$result['Cmd'] = $props_arr['cmd'];
		$result['CmdPath'] = $props_arr['cmd_path'];
		$result['ParentType'] = $props_arr['parent_id'];
		$result['PropsName'] = $props_arr['props_name'];
		$result['PropsMoney'] = $props_arr['props_money'];
		$result['PropsSize'] = $props_arr['props_size'];
		$result['PropsIco'] = $props_arr['props_ico'];
		return array('Flag'=>100,'FlagString'=>'成功','Result'=>$result);
	}
	
}
?>