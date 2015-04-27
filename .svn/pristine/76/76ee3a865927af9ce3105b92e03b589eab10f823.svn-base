<?php
/**
 * 后台互动游戏操作类
 * @author sdq
 * @copyright aodiansoft.com
 * @version $Id$
 */
class interact_manage extends Regions
{

	protected $db = null;
	private $region;
	
	function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	public function __destruct() {
		unset($this->db);
	}
	
	public function getOPenCity(){
		$open_citys = parent::getOpenCity();
		return $open_citys;
	}
	
	//游戏排序，置顶
	function interact_order($id,$type){
		$result = false;
		switch($type){
			case 'up':
				$result = $this->db->query("UPDATE ".DB_NAME_REGION.".tbl_interact SET interact_order=interact_order+1 WHERE id='{$id}'");
				break;
			case 'down':
				$result = $this->db->query("UPDATE ".DB_NAME_REGION.".tbl_interact SET interact_order=interact_order-1 WHERE id='{$id}'");
				break;
			case 'top':
				$max_order = $this->db->get_var("SELECT max(interact_order) FROM ".DB_NAME_REGION.".tbl_interact");
				$max_order += 1;
				$result = $this->db->query("UPDATE ".DB_NAME_REGION.".tbl_interact SET interact_order={$max_order} WHERE id='{$id}'");
				break;
		}
		if($result){
			return array("Flag"=>100,"FlagString"=>"操作成功");
		}else{
			return array("Flag"=>101,"FlagString"=>"操作失败");
		}
	}
	//添加游戏
	public function interact_add($json){
		$cmd = $json['Cmd'];
		$cmd_path = $json['CmdPath'];
		$big_case_id = $json['BigCaseId'];
		$case_id = $json['CaseId'];
		$parent_id = $json['ParentId'];
		$interact_name = urldecode($json['InteractName']);
		$interact_status = $json['InteractStatus'];
		$room_span = $json['RoomSpan'];
		$interact_cat = $json['InteractCat'];
		$interact_pic = $json['InteractPic'];
		$status_cat = $json['StatusCat'];
		$status_pic = $json['StatusPic'];
		if(!empty($big_case_id) && !empty($cmd_path) && !empty($case_id) && !empty($parent_id) && !empty($interact_name)){
			$sql = 'INSERT INTO '.DB_NAME_REGION.'.tbl_interact(cmd,cmd_path,big_case_id,case_id,parent_id,interact_name,interact_cat,interact_pic,status_cat,status_pic,interact_status,room_span,uptime) VALUES("'.$cmd.'","'.$cmd_path.'","'.$big_case_id.'","'.$case_id.'","'.$parent_id.'","'.$interact_name.'","'.$interact_cat.'","'.$interact_pic.'","'.$status_cat.'","'.$status_pic.'","'.$interact_status.'","'.$room_span.'","'.time().'");';
			if($this->db->query($sql)) {
				$result = array("Flag"=>100,"FlagString"=>"添加成功");
			} else {
				$result = array("Flag"=>101,"FlagString"=>"添加失败");
			}
		}else{
			$result = array("Flag"=>102,"FlagString"=>"参数错误");
		}
		return $result;
	}
	//修改游戏
	public function interact_update($json) {
		$id = $json['Id'];
		$cmd = $json['Cmd'];
		$cmd_path = $json['CmdPath'];
		$big_case_id = $json['BigCaseId'];
		$case_id = $json['CaseId'];
		$parent_id = $json['ParentId'];
		$interact_name = urldecode($json['InteractName']);
		$interact_status = $json['InteractStatus'];
		$robot = $json['Robot'];
		$room_span = $json['RoomSpan'];
		$interact_cat = $json['InteractCat'];
		$interact_pic = $json['InteractPic'];
		$status_cat = $json['StatusCat'];
		$status_pic = $json['StatusPic'];
		$category = $json['Category'];
		$category_id = $json['Category_id'];
		
		if(!empty($id) && !empty($big_case_id) && !empty($cmd_path) && !empty($case_id) && !empty($parent_id) && !empty($interact_name)) {
			$sql = 'UPDATE '.DB_NAME_REGION.'.tbl_interact SET `cmd`="'.$cmd.'",`cmd_path`="'.$cmd_path.'",`big_case_id`="'.$big_case_id.'",`case_id`="'.$case_id.'",`parent_id`="'.$parent_id.'",`interact_name`="'.$interact_name.'",`interact_cat`="'.$interact_cat.'",`interact_pic`="'.$interact_pic.'",`status_cat`="'.$status_cat.'",`status_pic`="'.$status_pic.'",`interact_status`="'.$interact_status.'",`robot`="'.$robot.'",`room_span`="'.$room_span.'",`uptime`="'.time().'",category ='.$category.',category_id ='.$category_id.' where id="'.$id.'";';
			if($this->db->query($sql)) {
				$result = array("Flag"=>100,"FlagString"=>"修改成功");
			} else {
				$result = array("Flag"=>101,"FlagString"=>"修改失败");
			}
		} else {
			$result = array("Flag"=>102,"FlagString"=>"参数错误");
		}
		return $result;
	}
	
	//游戏列表
	public function interact_list($data,$id='') {
        if(isset($data['idin'])){
            $where .= " AND i.id IN({$data['idin']})";
            $order = " ORDER BY INSTR('{$data['idin']}',i.id)";
        }else{
            $order = " ORDER BY i.interact_order DESC";
        }
		if(!empty($data['big_case_id'])){
			$where .= ' AND i.big_case_id="'.$data['big_case_id'].'"';
		}
		if(!empty($data['case_id'])){
			$where .= ' AND i.case_id="'.$data['case_id'].'"';
		}
		if(!empty($data['parent_id'])){
			$where .= ' AND i.parent_id="'.$data['parent_id'].'"';
		}
		if($data['province']!=-1 && !empty($data['province'])){
			$where .= ' AND i.province_id="'.$data['province'].'"';
		}
		if($data['city']!=-1 && !empty($data['city'])){
			$where .= ' AND i.city_id="'.$data['city'].'"';
		}
		if($data['area']!=-1 && !empty($data['area'])){
			$where .= ' AND i.area_id="'.$data['area'].'"';
		}
		if(is_numeric($data['region_id'])){
			$where .= ' AND (i.region_id='.$data['region_id'].' OR i.region_id=0)';
		}/*else{
			if($data['IsBack']!==true)
				$where .= ' AND i.region_id=0';
		}*/
		if(isset($data['interact_status']) && intval($data['interact_status']) != -1){
			$where .= ' AND i.interact_status="'.intval($data['interact_status']).'"';
		}
		if(isset($data['robot'])){
			$where .= ' AND i.robot="'.$data['robot'].'"';
		}
		if(!empty($data['keyword'])){
			$where .= " AND i.interact_name LIKE '".urldecode($data['keyword'])."%'";
		}
		if(!empty($id)){
			$where .= " AND i.id='".$id."'";
		}
		if(!empty($where)){
			$where = ' WHERE '.ltrim($where,' AND ');
		}
		$total = $this->db->get_var('SELECT COUNT(i.id) FROM '.DB_NAME_REGION.'.tbl_interact as i LEFT JOIN '.DB_NAME_SYSTEM_CONFIG.'.pic_manager AS m ON i.interact_pic=m.id'.$where.';');
		if($total > 0) {
			//$page_arr = $this->showPage($total);
			$sql = 'SELECT i.*,m.img_path AS interact_img FROM '.DB_NAME_REGION.'.tbl_interact AS i LEFT JOIN '.DB_NAME_SYSTEM_CONFIG.'.pic_manager AS m ON i.interact_pic=m.id'.$where.$order;
			$list = $this->db->get_results($sql,'ASSOC');
			foreach($list as $k=>$v){
				if(!empty($list[$k]['interact_img']))
					$list[$k]['interact_img'] = cdn_url(PIC_API_PATH.'/p/'.$v['interact_img'].'/0/0.jpg');
				$sql = 'SELECT img_path FROM '.DB_NAME_SYSTEM_CONFIG.'.pic_manager WHERE id="'.$v['status_pic'].'"';
				$img_path = $this->db->get_var($sql);
				$list[$k]['status_img'] = '';
				if(!empty($img_path))
					$list[$k]['status_img'] = cdn_url(PIC_API_PATH.'/p/'.$img_path.'/0/0.jpg');
			}
		}
		return empty($id) ? array('Flag'=>100,'FlagString'=>'成功','list'=>$list,'total'=>$total) : array('Flag'=>100,'FlagString'=>'成功','list'=>$list[0],'total'=>$total);
	}
	
	public function interactConfig($name,$id){
		if($id > 0){
			$where = ' AND i.id ='.$id;
		}
		if(!empty($name)){
			$where .= ' AND i.interact_name ="'.$name.'"';
		}
		$total = $this->db->get_var('SELECT COUNT(*) FROM '.DB_NAME_REGION.'.`tbl_interact` i WHERE 1 '.$where);
		if($total > 0) {
			$sql = 'SELECT i.id,i.interact_name,i.category,i.category_id,i.region_id,i.interact_status,i.province_id,i.city_id,i.area_id,i.cmd FROM '.DB_NAME_REGION.'.`tbl_interact` i WHERE 1'.$where.';';
			$result = $this->db->get_results($sql,"ASSOC");
			foreach($result as $key=>$value){
				$result[$key]['value'] = $this->db->get_var('SELECT g.value FROM '.DB_NAME_FLASH_GAME.'.`games_config` g WHERE g.key = "'.$value['cmd'].'_config"');
			}
		}
		return array('Flag'=>100,'FlagString'=>'成功','total'=>$total,'Result'=>$result,'region'=>$this->getOPenCity());
	}
	
	public function getInteractGropuConfig($interact,$group){
		$interact = (int)$interact;
		$group = (int)$group;
		$sql = "SELECT * FROM ".DB_NAME_REGION.".tbl_interact_group WHERE interact={$interact} AND groupid={$group} LIMIT 1";
		$config = $this->db->get_row($sql,'ASSOC');
		if(empty($config)){
			$config = array('interact'=>$interact,'groupid'=>$group,'robot'=>0);
		}
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$config);
	}
	
	public function setInteractGropuConfig($interact,$group,$robot){
		$interact = (int)$interact;
		$group = (int)$group;
		$robot = (int)$robot;
		$sql = "REPLACE INTO ".DB_NAME_REGION.".tbl_interact_group(interact,groupid,robot)VALUES({$interact},{$group},{$robot})";
		if(!$this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'操作失败');
		}
		return array('Flag'=>100,'FlagString'=>'操作成功');
	}
	
	//游戏配置列表
	public function interactConfigList($cmd){
		if(empty($cmd)) return array('Flag'=>101,'FlagString'=>'参数错误');
		$sql = 'SELECT `value`,`descr` FROM '.DB_NAME_FLASH_GAME.'.games_config WHERE `key`="'.$cmd.'_config'.'"';
		$result = $this->db->get_row($sql,'ASSOC');
		$result['value'] = (array)unserialize($result['value']);
		$result['descr'] = (array)unserialize($result['descr']);
		return array('Flag'=>100,'FlagString'=>'成功','Result'=>$result);
	}
	
	//保存游戏配置
	public function interactConfigSave($key,$value,$descr){
		if(empty($key) || empty($value)) return array('Flag'=>101,'FlagString'=>'参数错误');
		$value = serialize($value);
		$descr = serialize($descr);
		$sql = 'SELECT COUNT(1) FROM '.DB_NAME_FLASH_GAME.'.games_config WHERE `key`="'.$key.'_config'.'"';
		if($this->db->get_var($sql) > 0){
			$sql = "UPDATE ".DB_NAME_FLASH_GAME.".games_config SET `value`='{$value}',`descr`='{$descr}' WHERE `key`='{$key}_config'";
		}else{
			$sql = "INSERT INTO ".DB_NAME_FLASH_GAME.".games_config(`key`,`value`,`descr`) VALUES('{$key}_config','{$value}','{$descr}')";
		}
		if($this->db->query($sql))
			return array('Flag'=>100,'FlagString'=>'游戏配置修改成功');
		return array('Flag'=>102,'FlagString'=>'游戏配置修改失败');
	}

	//删除游戏
	public function interact_del($id) {
		if(!empty($id)) {
			$sql = 'DELETE FROM '.DB_NAME_REGION.'.tbl_interact WHERE id="'.$id.'"';
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
	
	public function getInfoByCmd($cmd){
		$sql = 'SELECT * FROM '.DB_NAME_REGION.'.tbl_interact WHERE cmd="'.$cmd.'"';
		$info = $this->db->get_row($sql,'ASSOC');
		return array('Flag'=>100,'FlagString'=>'成功','Info'=>$info);
	}
	
	public function getGameInfo($parent){
		$sql = "SELECT * FROM ".DB_NAME_REGION.".tbl_interact WHERE parent_id={$parent} ORDER BY interact_status DESC LIMIT 1";
		$info = $this->db->get_row($sql,'ASSOC');
		if(empty($info)){
			return array('Flag'=>101,'FlagString'=>'游戏不存在');
		}else{
			return array('Flag'=>100,'FlagString'=>'成功','Info'=>$info);
		}
	}
	
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
