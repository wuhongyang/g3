<?php

/**
 * 奥点网络媒体互动用户计费管理平台软件
 * 模块: 印章接口
 * copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */

class stamp
{

	/**
	 * 数据库指针
	 * 
	 */
	protected $db = null;

	/**
	 * 构造函数
	 * 
	 */
	public function __construct() {
		
		$this->db = db::connect(config('database','default'));
	}

	/**
	 * 析构函数
	 *
	 */
	public function __destruct() {
		unset ($this->db);
	}
	
	/**
     * 印章列表
     * 
     * @access public
     * @return json
     */
	public function StampList($param) {
		// $roomid = $param['ChannelId'];
		$module_id = domain::main()->GroupKeyVal($param['GroupId'],'module_id');
		$ui_info = $this->db->get_var('SELECT cate_id FROM '.DB_NAME_TPL.'.tpl_config_cate WHERE tpl_id = '.$module_id.' AND  `type`=2 ','ASSOC');
		$stamp_arr = json_decode($ui_info,true);
		foreach((array)$stamp_arr as $key=>$value){
			$type_id = $value;
			$type_arr = $this->db->get_row('SELECT `name`,parent_id,price FROM '.DB_NAME_REGION.'.tbl_stamptype WHERE parent_id ='.$type_id.'  AND `status` = 1',"ASSOC");
			if(!empty($type_arr)){
				$result[$key]['ParentId'] = $type_arr['parent_id'];
				$result[$key]['TypeName'] = $type_arr['name'];
				$result[$key]['List'] = array();
				$sql = 'SELECT stamp_id,stamp_name,stamp_img_path FROM ' . DB_NAME_REGION . '.tbl_stamp WHERE parent_id="'.$type_id.'";';
				$stamp_arr = $this->db->get_results($sql,'ASSOC');
				foreach((array)$stamp_arr as $stamp_key => $stamp_val) {
					$result[$key]['List'][$stamp_key]['StampID'] = $stamp_val['stamp_id'];
					$result[$key]['List'][$stamp_key]['StampName'] = $stamp_val['stamp_name'];
					$result[$key]['List'][$stamp_key]['StampMoney'] = $type_arr['price'];
					$result[$key]['List'][$stamp_key]['StampExpire'] = 30;
					$result[$key]['List'][$stamp_key]['StampIco'] = cdn_url(PIC_API_PATH."/p/{$stamp_val['stamp_img_path']}/0/0.jpg");
				}
			}
		}
		return array('Flag'=>100,'FlagString'=>'获取印章列表成功','Result'=>$result);
	}
	
	/**
     * 单个印章
     * 
     * @access public
     * @return json
     */
	public function stamp_arr($stamp_id) {
		if(!empty($stamp_id)) {
			$sql = 'SELECT s.stamp_id,s.stamp_name,t.price FROM ' . DB_NAME_REGION . '.tbl_stamp s LEFT JOIN ' . DB_NAME_REGION . '.tbl_stamptype t ON s.parent_id=t.parent_id  WHERE s.stamp_id='.$stamp_id.' AND t.status = "1";';
			$stamp_arr = $this->db->get_row($sql,'ASSOC');
		}
		return $stamp_arr;
	}
}
