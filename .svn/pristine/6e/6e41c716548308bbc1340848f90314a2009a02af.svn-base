<?php
/**
 * 后台分站方言类
 * @author pgp
 * @copyright aodiansoft.com
 * @version $Id$
 */
include_once 'interfaces.class.php';
class RuleDefine extends Interfaces{

	protected $db = null;
	
	function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	public function __destruct() {
		unset($this->db);
	}

	
	public function getRuleDefine($tpl_id){
	   if($tpl_id){
	       $where = " AND tpl_id = ".$tpl_id;
	   }
		$sql = 'SELECT `id`,`name`,`user_id_type`,`business_id_type`,`extended_parameters` FROM '.DB_NAME_TPL.'.business_rule WHERE `status`="1"'.$where;
        $result = $this->db->get_results($sql,'ASSOC');
		if($result)
			return array('Flag'=>100,'FlagString'=>'业务规则','Result'=>$result);
		return array('Flag'=>102,'FlagString'=>'业务规则获取失败');
	}
	
	public function lists($data, $page=false){
		$where = ' WHERE TRUE';
		if(isset($data['name']) && $data['name']!=''){
			$where .= ' AND `name` LIKE "'.$data['name'].'%"';
		}
		if(isset($data['status']) && $data['status']!=-1){
			$where .= ' AND `status`="'.$data['status'].'"';
		}
		if($data['scope'] > 0){
			$where .= ' AND `scope`='.intval($data['scope']);
		}
        if($data['tpl_id']){
            $where .= ' AND `tpl_id` = '.$data['tpl_id'];
        }
		if(!$page){
			$sql = 'SELECT `id`,`name`,`desc`,`rule`,`status`,uptime FROM '.DB_NAME_TPL.'.business_rule '.$where.' ORDER BY status DESC';
			$list = $this->db->get_results($sql,'ASSOC');
			return array('Flag'=>100,'FlagString'=>'业务规则定义列表','Result'=>$list,'Interface'=>$this->getBusinessInterface(),'Page'=>'');
		}else{
			$sql = 'SELECT COUNT(*) FROM '.DB_NAME_TPL.'.business_rule '.$where;
			$total = $this->db->get_var($sql);
			$page_arr = $this->showPage($total);
			$sql = 'SELECT `id`,`name`,`desc`,`rule`,`status`,uptime FROM '.DB_NAME_TPL.'.business_rule '.$where.' ORDER BY status DESC LIMIT '.$page_arr['limit'];
			$list = $this->db->get_results($sql,'ASSOC');
			return array('Flag'=>100,'FlagString'=>'业务规则定义列表','Result'=>$list,'Interface'=>$this->getBusinessInterface(),'Page'=>$page_arr['page']);
		}
	}
	
	public function getInfo($id){
		if($id){
			$sql = 'SELECT * FROM '.DB_NAME_TPL.'.business_rule WHERE id="'.$id.'"';
			$info = $this->db->get_row($sql,'ASSOC');
		}
		return array('Flag'=>100,'FlagString'=>'业务规则定义详情','Info'=>$info,'Interface'=>$this->getBusinessInterface());
	}
	
	public function add($data){
		if($data['name'] == '') return array('Flag'=>101,'FlagString'=>'参数错误');
// 		$data['user_id_type'] = intval($data['user_id_type']);
// 		$data['business_id_type'] = intval($data['business_id_type']);
// 		$data['extended_parameters'] = intval($data['extended_parameters']);
// 		if(empty($data['user_id_type']) && empty($data['business_id_type']) && empty($data['extended_parameters'])){
// 			return array('Flag'=>101,'FlagString'=>'请至少选择一个数据主键规则');
// 		}
		
		$keys_str = $this->to_key_str($data['keys']);
		if($data['bigcase_id'] > 0 && $data['case_id'] > 0 && $data['parent_id'] > 0  && $data['child_id'] > 0 ){
			$cb_subject = array('bigcase_id'=>$data['bigcase_id'],'case_id'=>$data['case_id'],'parent_id'=>$data['parent_id'],'child_id'=>$data['child_id']);
		}
		$sql = 'INSERT INTO '.DB_NAME_TPL.'.business_rule(`name`,`desc`,`user_id_type`,`business_id_type`,`extended_parameters`,`sort_type`,`sort_key`,`scope`,`rule_type`,`period`,`interval_type`,`extend`,`rule`,`status`,`uptime`,`cb_subject`,`tpl_id`) VALUES("'.$data['name'].'","'.$data['desc'].'",\''.$keys_str.'\',\''.json_encode($data['keys']).'\',"0","'.$data['sort_type'].'","'.$data['sort_key'].'","'.$data['scope'].'","'.$data['rule_type'].'",'.intval($data['period']).','.intval($data['interval_type']).',"'.addslashes($data['extend']).'","'.addslashes($data['rule']).'","'.$data['status'].'","'.time().'",\''.json_encode($cb_subject).'\',"'.$data['tpl_id'].'")';
        $result = $this->db->query($sql);
		if($result)
			return array('Flag'=>100,'FlagString'=>'添加成功');
		return array('Flag'=>102,'FlagString'=>'添加失败');
	}
	
	public function update($data,$id){
		if($data['name'] == '') return array('Flag'=>101,'FlagString'=>'参数错误');
// 		$data['user_id_type'] = intval($data['user_id_type']);
// 		$data['business_id_type'] = intval($data['business_id_type']);
// 		$data['extended_parameters'] = intval($data['extended_parameters']);
// 		if(empty($data['user_id_type']) && empty($data['business_id_type']) && empty($data['extended_parameters'])){
// 			return array('Flag'=>101,'FlagString'=>'请至少选择一个数据主键规则');
// 		}
		$keys_str = $this->to_key_str($data['keys']);
		if($data['bigcase_id'] > 0 && $data['case_id'] > 0 && $data['parent_id'] > 0  && $data['child_id'] > 0 ){
		 $cb_subject = array('bigcase_id'=>$data['bigcase_id'],'case_id'=>$data['case_id'],'parent_id'=>$data['parent_id'],'child_id'=>$data['child_id']);
		}
		//$sql = 'UPDATE '.DB_NAME_BEHAVIOR.'.business_rule SET `name`="'.$data['name'].'",`desc`="'.$data['desc'].'",`user_id_type`="'.$data['user_id_type'].'",`business_id_type`="'.$data['business_id_type'].'",`extended_parameters`="'.$data['extended_parameters'].'",`scope`="'.$data['scope'].'",`rule_type`="'.$data['rule_type'].'",`period`='.intval($data['period']).',`interval_type`='.intval($data['interval_type']).',`extend`="'.addslashes($data['extend']).'",`rule`="'.addslashes($data['rule']).'",`status`="'.$data['status'].'" WHERE id="'.$id.'"';
		$sql = 'UPDATE '.DB_NAME_TPL.'.business_rule SET `name`="'.$data['name'].'",`desc`="'.$data['desc'].'",`user_id_type`=\''.$keys_str.'\',`business_id_type`=\''.json_encode($data['keys']).'\',`cb_subject`=\''.json_encode($cb_subject).'\',`sort_type`="'.$data['sort_type'].'",`sort_key`="'.$data['sort_key'].'",`scope`="'.$data['scope'].'",`rule_type`="'.$data['rule_type'].'",`period`='.intval($data['period']).',`interval_type`='.intval($data['interval_type']).',`extend`="'.addslashes($data['extend']).'",`rule`="'.addslashes($data['rule']).'",`status`="'.$data['status'].'" WHERE id="'.$id.'"';
		$result = $this->db->query($sql);
		if($result)
			return array('Flag'=>100,'FlagString'=>'修改成功');
		return array('Flag'=>102,'FlagString'=>'修改失败');
	}
	
	private function to_key_str($post_keys){
		$ids = join(",", $post_keys);
		$sql = "SELECT `id`,`engname`,`type`, `extra` FROM ".DB_NAME_BEHAVIOR.".business_key WHERE id IN (".$ids.")";
		$res = $this->db->get_results($sql, "ASSOC");
		$id_value = array();
		foreach($res as $one){
			if($one['type'] == "RoleId"){
				$arr = json_decode($one['extra'], true);
				$id_value[$one['id']] = array($one['engname'], $arr['role'], $arr['user_type']);
			}else{
				$id_value[$one['id']] = array($one['engname']);
			}
		}
		$keys = array();
		foreach($post_keys as $name=>$id){
			if($id_value[$id]){
				$keys[$name] = $id_value[$id];
			}
		}
		return $keys_str = json_encode($keys);
	}
	
	//分页
	private function showPage($total, $perpage = 20) {
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
