<?php
/**
 * 房间方案类
 * @author 
 * @copyright aodiansoft.com
 * @version $Id$
 */
class UiPackage
{

	protected $db = null;
	
	function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	public function __destruct() {
		unset($this->db);
	}
	
	public function getOpenCity(){
		$open_citys = parent::getOpenCity();
		return $open_citys;
	}
	
	//列表查看
	public function GetNewPhiz($param){
		$roomid = $param['ChannelId'];
		$roomdb = DB_NAME_NEW_ROOMS;
		
		$module_id = domain::main()->GroupKeyVal($param['GroupId'],'module_id');
		$ui_info = $this->db->get_var('SELECT cate_id FROM '.DB_NAME_TPL.'.tpl_config_cate WHERE tpl_id = '.$module_id.' AND  `type`=1 ','ASSOC');
		$expres_arr = json_decode($ui_info,true);
		
		foreach($expres_arr as $key=>$value){
			$row = $this->db->get_row('SELECT cate_name,cate_id FROM '.DB_NAME_REGION.'.tbl_expressiontype WHERE cate_id ='.$value.'  AND `status` = 1',"ASSOC");
			if(!empty($row)){
				$result[$key]['CateName'] = $row['cate_name'];
				$result[$key]['CateId'] = $row['cate_id'];
				$list = $this->db->get_results("SELECT img_name,img_path FROM ".DB_NAME_REGION.".tbl_expression WHERE cate_id = ".$row['cate_id']." ORDER BY id ASC","ASSOC");
				foreach($list as $kk=>$val){
					$list[$kk]['img_path'] = cdn_url(PIC_API_PATH."/p/{$val['img_path']}/0/0.jpg");
				}
				$result[$key]['List'] =$list;
			}
		}
		return array('Flag'=>100,'FlagString'=>'成功','PhizList'=>$result);
	}
	
}
