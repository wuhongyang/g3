<?php
/**
 * 后台分站方言类
 * @author pgp
 * @copyright aodiansoft.com
 * @version $Id$
 */
include_once 'interfaces.class.php';
class ParamConfig extends Interfaces {
	
	private function getRuleDefine($tpl_id){
		$rules = httpPOST('api/admin/template_api.php',array('extparam'=>array('Tag'=>'GetRuleDefine', 'TplId'=>$tpl_id)));
		return $rules;
	}
	
	public function lists($data){
		$where = ' WHERE TRUE';
        if($data['tpl_id']){
			$where .= ' AND `tpl_id` = "'.$data['tpl_id'].'"';
		}
		if(isset($data['name']) && $data['name']!=''){
			$where .= ' AND `name` LIKE "'.$data['name'].'%"';
		}
		if(isset($data['status']) && $data['status']!=-1){
			$where .= ' AND `status`="'.$data['status'].'"';
		}
		//$total = $this->db->get_var('SELECT COUNT(1) FROM '.DB_NAME_BEHAVIOR.'.business_param_config '.$where);
		//if($total > 0){
			//$page_arr = $this->showPage($total);
			$sql = 'SELECT `id`,`name`,`desc`,`rule_id`,`status` FROM '.DB_NAME_TPL.'.business_param_config '.$where.' ORDER BY `status` ASC';
			$list = $this->db->get_results($sql,'ASSOC');
		//}
		return array('Flag'=>100,'FlagString'=>'业务参数配置列表','Result'=>$list,'Page'=>'');
	}
	
	public function getInfo($id,$tpl_id){
		if($id){
			$sql = 'SELECT * FROM '.DB_NAME_TPL.'.business_param_config WHERE id="'.$id.'"';
			$info = $this->db->get_row($sql,'ASSOC');
		}
		return array('Flag'=>100,'FlagString'=>'业务参数配置详情','Info'=>$info,'Rule'=>$this->getRuleDefine($tpl_id),'ReadInterface'=>$this->getReadInterface(),'WriteInterface'=>$this->getWriteInterface());
	}
	
	public function add($data){
		if($data['name'] == '') return array('Flag'=>101,'FlagString'=>'参数错误');
		
		//检查rule_id是否存在
		//$sql = 'SELECT COUNT(1) FROM '.DB_NAME_BEHAVIOR.'.business_param_config WHERE rule_id="'.$data['rule'].'"';
		//if($this->db->get_var($sql) > 0)  return array('Flag'=>103,'FlagString'=>'已经存在的业务规则');
		$sql = 'INSERT INTO '.DB_NAME_TPL.'.business_param_config(`name`,`desc`,`rule_id`,`read_interface`,`period`,`style`,`property`,`integration`,`status`,`tpl_id`) VALUES("'.$data['name'].'","'.$data['desc'].'","'.$data['rule'].'","'.$data['read_interface'].'","'.$data['period'].'","'.$data['style'].'","'.$data['property'].'","'.addslashes($data['integration']).'","'.$data['status'].'","'.$data['tpl_id'].'")';
		$result = $this->db->query($sql);
		if($result)
			return array('Flag'=>100,'FlagString'=>'添加成功');
		return array('Flag'=>102,'FlagString'=>'添加失败');
	}
	
	public function update($data,$id){
		if($data['name'] == '') return array('Flag'=>101,'FlagString'=>'参数错误');
		
		//检查rule_id是否存在
		//$sql = 'SELECT COUNT(1) FROM '.DB_NAME_BEHAVIOR.'.business_param_config WHERE rule_id="'.$data['rule'].'" AND id!="'.$id.'"';
		//if($this->db->get_var($sql) > 0)  return array('Flag'=>103,'FlagString'=>'已经存在的业务规则');
		$sql = 'UPDATE '.DB_NAME_TPL.'.business_param_config SET `name`="'.$data['name'].'",`desc`="'.$data['desc'].'",`rule_id`="'.$data['rule'].'",`read_interface`="'.$data['read_interface'].'",`write_interface`="'.$data['write_interface'].'",`period`="'.$data['period'].'",`style`="'.$data['style'].'",`property`="'.$data['property'].'",`integration`="'.addslashes($data['integration']).'",`status`="'.$data['status'].'" WHERE id="'.$id.'"';
		$result = $this->db->query($sql);
		if($result)
			return array('Flag'=>100,'FlagString'=>'修改成功');
		return array('Flag'=>102,'FlagString'=>'修改失败');
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
