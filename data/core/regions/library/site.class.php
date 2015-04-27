<?php
/**
 * 后台地域分站类
 * @author pgp
 * @copyright aodiansoft.com
 * @version $Id$
 */
class site extends Regions
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
	public function siteList($array){
		$where = ' WHERE TRUE';
		if(isset($array['province']) && $array['province']>0)
			$where .= ' AND province="'.$array['province'].'"';
		if(isset($array['city']) && $array['city']>0)
			$where .= ' AND city="'.$array['city'].'"';
		if(isset($array['ishot']) && $array['ishot']>-1)
			$where .= ' AND ishot="'.$array['ishot'].'"';
		if(isset($array['status']) && $array['status']>-1)
			$where .= ' AND status="'.$array['status'].'"';
		$total = $this->db->get_var('SELECT COUNT(1) FROM '.DB_NAME_REGION.'.tbl_site '.$where);
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = 'SELECT id,province,city,area,uptime,ishot,status FROM '.DB_NAME_REGION.'.tbl_site '.$where.' ORDER BY id DESC LIMIT '.$page_arr['limit'];
			$list = $this->db->get_results($sql,'ASSOC');
			foreach($list as $k=>$v){
				$pNames = $this->getProvinceName($v['province']);
				$cNames = $this->getCityName($v['city']);
				$aNames = $this->getAreaName($v['area']);
				$list[$k]['provinceName'] = $pNames['provinceName'];
				$list[$k]['cityName'] = $cNames['cityName'];
				$list[$k]['areaName'] = $aNames['cityName'];
			}
			$list['page'] = $page_arr['page'];
		}
		return array('Flag'=>100,'FlagString'=>'区域分站','Result'=>$list,'Region'=>$this->getOpenCity());
	}
	
	public function getInfo($id){
		$sql = 'SELECT * FROM '.DB_NAME_REGION.'.tbl_site WHERE id="'.$id.'"';
		$info = $this->db->get_row($sql,'ASSOC');
		if($info){
			$info['pic_url'] = urldecode($info['pic_url']);
			return array('Flag'=>100,'FlagString'=>'分站详情','Info'=>$info);
		}
		return array('Flag'=>102,'FlagString'=>'获取分站详情失败');
	}
	
	public function siteAdd($array){
		//参数检测
		if($array['province']==-1 || $array['city']==-1 || $array['domain']=='')
			return array('Flag'=>101,'FlagString'=>'参数错误');
		$region_id = ($array['area']==-1) ? $array['city'] : $array['area'];
		$cat_pic_json = json_encode(array(
			'header_url' => array('cat'=>$array['header_category'],'pic'=>$array['header_pic']),
			'footer_url' => array('cat'=>$array['footer_category'],'pic'=>$array['footer_pic']),
			'item_url' => array('cat'=>$array['part_bg_category'],'pic'=>$array['part_bg_pic']),
			'item_icon' => array('cat'=>$array['part_icon_category'],'pic'=>$array['part_icon_pic']),
			'rooms_bg_url' => array('cat'=>$array['default_room_category'],'pic'=>$array['default_room_pic']),
			'rooms_load_url' => array('cat'=>$array['room_load_bg_category'],'pic'=>$array['room_load_bg_pic']),
			'video_url' => array('cat'=>$array['room_video_bg_category'],'pic'=>$array['room_video_bg_pic'])
		));
		$array['city_name'] = str_replace('市','',$array['city_name']);
		
		//检测domain是否存在
		$sql = 'SELECT COUNT(1) FROM '.DB_NAME_REGION.'.tbl_site WHERE domain="'.$array['domain'].'"';
		if($this->db->get_var($sql) > 0)
			return array('Flag'=>103,'FlagString'=>'存在相同域名');
		//添加
		$sql = 'INSERT INTO '.DB_NAME_REGION.'.tbl_site(`province`,`city`,`area`,`region_id`,`city_name`,`domain`,`pic_url`,`cat_pic_json`,`uptime`,`ishot`,`status`) VALUES("'.$array['province'].'","'.$array['city'].'","'.$array['area'].'","'.$region_id.'","'.$array['city_name'].'","'.$array['domain'].'","'.$array['pic_url'].'","'.addslashes($cat_pic_json).'","'.time().'","'.$array['ishot'].'","'.$array['status'].'")';
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'添加成功');
		}
		return array('Flag'=>102,'FlagString'=>'添加失败');
		
	}
	
	public function siteUpdate($array,$id){
		//参数检测
		if($array['province']==-1 || $array['city']==-1 || $array['domain']=='')
			return array('Flag'=>101,'FlagString'=>'参数错误');
		$region_id = ($array['area']==-1) ? $array['city'] : $array['area'];
		$cat_pic_json = json_encode(array(
			"header_url" => array('cat'=>$array['header_category'],'pic'=>$array['header_pic']),
			"footer_url" => array('cat'=>$array['footer_category'],'pic'=>$array['footer_pic']),
			"item_url" => array('cat'=>$array['part_bg_category'],'pic'=>$array['part_bg_pic']),
			"item_icon" => array('cat'=>$array['part_icon_category'],'pic'=>$array['part_icon_pic']),
			"rooms_bg_url" => array('cat'=>$array['default_room_category'],'pic'=>$array['default_room_pic']),
			"rooms_load_url" => array('cat'=>$array['room_load_bg_category'],'pic'=>$array['room_load_bg_pic']),
			"video_url" => array('cat'=>$array['room_video_bg_category'],'pic'=>$array['room_video_bg_pic'])
		));
		$array['city_name'] = str_replace('市','',$array['city_name']);
		
		//检测domain是否存在
		$sql = 'SELECT COUNT(1) FROM '.DB_NAME_REGION.'.tbl_site WHERE domain="'.$array['domain'].'" AND id!="'.$id.'"';
		if($this->db->get_var($sql) > 0)
			return array('Flag'=>103,'FlagString'=>'存在相同域名');
		$sql = 'update '.DB_NAME_REGION.'.tbl_site SET `province`="'.$array['province'].'",`city`="'.$array['city'].'",`area`="'.$array['area'].'",`region_id`="'.$region_id.'",`city_name`="'.$array['city_name'].'",`domain`="'.$array['domain'].'",`pic_url`="'.$array['pic_url'].'",`cat_pic_json`="'.addslashes($cat_pic_json).'",`ishot`="'.$array['ishot'].'",`status`="'.$array['status'].'" WHERE `id`="'.$id.'"';
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'修改成功');
		}
		return array('Flag'=>102,'FlagString'=>'修改失败');
	}
	
	//热门
	public function setHot($id){
		$sql = 'UPDATE '.DB_NAME_REGION.'.tbl_site SET ishot="1" WHERE id="'.$id.'"';	
		if($this->db->query($sql))
			return array('Flag'=>100,'FlagString'=>'设置成功');
		return array('Flag'=>102,'FlagString'=>'设置失败');
	}
	
	public function getNameByRegion($region_id){
		$region_id = intval($region_id);
		if($region_id < 0) return array('Flag'=>101,'FlagString'=>'region参数错误');
		$sql = "SELECT city_name FROM ".DB_NAME_REGION.".tbl_site WHERE region_id={$region_id}";
		$siteName = $this->db->get_var($sql);
		if(!empty($siteName)){
			return array('Flag'=>100,'FlagString'=>'站点名称','SiteName'=>$siteName);
		}
		return array('Flag'=>102,'FlagString'=>'获取站点名称失败');
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
