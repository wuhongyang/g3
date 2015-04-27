<?php
class outstation_ad extends Regions
{

	protected $db = null;
	
	function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	/*//获得广告列表
	function getAdList($param){
		$where = '';
		if(isset($param['province']) && $param['province']>0)
			$where .= ' AND province="'.$param['province'].'"';
		if(isset($param['city']) && $param['city']>0)
			$where .= ' AND city="'.$param['city'].'"';
		if(isset($param['status']) && $param['status']>-1)
			$where .= ' AND status="'.$param['status'].'"';
		if(!empty($where))
			$where = ' WHERE '.ltrim($where,' AND ');
		$total = $this->db->get_var('SELECT COUNT(1) FROM '.DB_NAME_REGION.'.tbl_cycleimg '.$where);
		if($total > 0){
			$page_arr = $this->_showPage($total);
			$sql = 'SELECT * FROM '.DB_NAME_REGION.'.tbl_cycleimg '.$where.' ORDER BY id DESC LIMIT '.$page_arr['limit'];
			$list = $this->db->get_results($sql,'ASSOC');
			foreach($list as $k=>$v){
				$pNames = $this->getProvinceName($v['province']);
				$cNames = $this->getCityName($v['city']);
				$list[$k]['provinceName'] = $pNames['provinceName'];
				$list[$k]['cityName'] = $cNames['cityName'];
			}
			$list['page'] = $page_arr['page'];
		}
		return array('Flag'=>100,'FlagString'=>'查询成功','Result'=>$list,'Region'=>$this->getOpenCity());
	}
	
	function getAdInfo($id=null){
		if($id > 0){
			$sql = 'SELECT * FROM '.DB_NAME_REGION.'.tbl_cycleimg WHERE id="'.$id.'"';
			$info = $this->db->get_row($sql,'ASSOC');
		}
		return array(
				'Flag'		 =>100,
				'FlagString'=>'分站轮播编辑',
				'Info'		 =>$info,
			  	'Region'	 =>$this->getOpenCity(),
				'AdImg'	 =>$this->_getAdImg()
			);
	}
	
	//添加，编辑广告
	function adjust($arg){
		$arg['cat_pic_json'] = urldecode($arg['cat_pic_json']);
		$arg['thumb_cat_pic_json'] = urldecode($arg['thumb_cat_pic_json']);
		if($arg['id']){
			$id = $arg['id'];
			unset($arg['id']);
			$update = "";
			foreach($arg as $key => $value){
				$update .= "`$key`='$value',";
			}
			$update = substr($update, 0, -1);
			$sql = "UPDATE ".DB_NAME_REGION.".tbl_cycleimg SET ".$update." WHERE `id`='".$id."'";
			$is_done = $this-> db -> query($sql);
			$FlagString = $is_done?"更新成功":"更新失败";
		}else{
			$cols = "`".(join("`,`", array_keys($arg)))."`";
			$values = "'".(join("','", array_values($arg)))."'";
			$sql = 'INSERT INTO '.DB_NAME_REGION.'.tbl_cycleimg('.$cols.') VALUES('.$values.')';
			$is_done = $this-> db -> query($sql);
			$FlagString = $is_done?"添加成功":"添加失败";
		}
		$Flag = $is_done?100:101;
		return array('Flag'=>$Flag,'FlagString'=>$FlagString);
	}
	
	//前台获得广告
	function getAdImg($region_id){
		$sql = 'SELECT * FROM '.DB_NAME_REGION.'.tbl_cycleimg
				WHERE region_id = '.intval($region_id).' AND status > 0
				ORDER BY weight DESC, uptime DESC LIMIT 0,6';
		$results = $this->db->get_results($sql, 'ASSOC');
		return array('Flag'=>100,'FlagString'=>'获取列表成功','Result'=>$results);
	}*/
	
	//分页
	private function _showPage($total, $perpage = 15) {
		if ($total > 0) {
			$page = new extpage(array (
				'total'  => $total,
				'perpage'=> $perpage
			));
			$page_arr['page'] = $page->show();
			$page_arr['limit'] = $page->limit();
			unset ($page);
		}
		return $page_arr;
	}
	
	private function _getAdImg(){
		$cat = $this->_getCat();
		$pic = $this->_getPic();
		return array("Cat"=>$cat,"Pic"=>$pic);
	}
	
	//得到图片分类
	private function _getCat(){
		return httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'CatList','mypost'=>array('state'=>1))));
	}
	
	//得到图片
	private function _getPic($cat_id=''){
		return httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1,'cat_id'=>$cat_id))));
	}
}