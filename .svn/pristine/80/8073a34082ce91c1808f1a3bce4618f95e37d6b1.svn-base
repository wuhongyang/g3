<?php
/**
* 地域接口(实现地域分站)
* @author dl
* @date 2012-4-21
*/
class Regions
{
	public function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	public function getOpenCity($status=''){
		$tbl_site = DB_NAME_REGION.'.tbl_site';
		$tbl_province = DB_NAME_REGION.'.tbl_province';
		$tbl_city = DB_NAME_REGION.'.tbl_city';
		$tbl_area = DB_NAME_REGION.'.tbl_area';
		if(intval($status) == 1){
			$status_condition = ' AND s.status=1';
		}
		$sql = "SELECT p.province_id,p.province_name,c.city_id,c.city_name 
				FROM {$tbl_site} AS s,{$tbl_province} AS p,{$tbl_city} AS c 
				WHERE s.province = p.province_id AND s.city = c.city_id".$status_condition;
		$results = $this->db->get_results($sql,'ASSOC');
		$provinces = $citys = $areas = array(); //省市区
		foreach($results as $val){
			$province_id = $val['province_id'];
			$province_name = $val['province_name'];
			unset($val['province_id'],$val['province_name']);
			$provinces[$province_id] = array('province_id'=>$province_id,'province_name'=>$province_name);
			$citys[$province_id][] = $val; 
		}
		//$provinces = array_values($provinces);
		$sql = "SELECT a.area_id,a.area_name,a.city_id FROM {$tbl_site} AS s,{$tbl_area} AS a WHERE s.region_id = a.area_id".$status_condition;
		$results = $this->db->get_results($sql,'ASSOC');
		foreach($results as $area){
			$city_id = $area['city_id'];
			unset($area['city_id']);
			$areas[$city_id][] = $area;
		}
		return array('Flag'=>100,'FlagString'=>'成功','province'=>$provinces,'city'=>$citys,'area'=>$areas);
	}
	
	public function getCity($ip=''){
		if(empty($ip)) $ip = get_ip();
		$ip = ip2long($ip);
		if( ! empty($ip)){
			$sql = "SELECT * FROM ".DB_NAME_REGION.".tbl_ip WHERE ip_start <= {$ip} AND ip_end >= {$ip} LIMIT 1";
			$city = $this->db->get_row($sql,'ASSOC');
		}
		if(empty($city)) $city = array('Flag'=>101,'FlagString'=>'未知城市');
		return array_merge(array('Flag'=>100,'FlagString'=>'成功'),$city);
	}
	/*
	public function getRoomsCase($region_id){
		$region_id = intval($region_id);
		if($region_id > 0){
			return $this->getRoomCategory($region_id);
		}else{
			//得到所有站点
			return $this->getAllSite();
		}
	}*/
	
	//得到所有站点
	private function getAllSite(){
		$sql = 'SELECT id,region_id,city_name,domain,ishot,uptime FROM '.DB_NAME_REGION.'.tbl_site WHERE region_id>0 AND `status`=1 ORDER BY ishot DESC,region_id ASC';
		$result = $this->db->get_results($sql,'ASSOC');
		$total_user = 0;
		foreach((array)$result as $key=>$val){
			$result[$key]['city_name'] = rtrim($val['city_name'],'市');
			$sql = "SELECT SUM(curuser) FROM ".DB_NAME_REGION.".tbl_rooms_case WHERE region_id={$val['region_id']} AND `status`=1";
			$result[$key]['curuser'] = intval($this->db->get_var($sql));
			$total_user += $result[$key]['curuser'];
		}
		$results['rooms_case'] = $result;
		$results['total_user'] = $total_user;
		return array('Flag'=>100,'FlagString'=>'所有分站','Result'=>$results);
	}
	
	private function getRoomCategory($region_id){
		$sql = "SELECT id,region_id,`name`,curuser FROM ".DB_NAME_REGION.".tbl_rooms_case WHERE region_id={$region_id} AND parent_id=0 AND `status`=1 ORDER BY ordernum DESC";
		$case_array = $this->db->get_results($sql,'ASSOC');
		$curuser = 0;
		foreach ((array)$case_array as $key => $val) {
			$sql = "SELECT id,region_id,`name`,curuser FROM ".DB_NAME_REGION.".tbl_rooms_case WHERE parent_id={$val['id']} AND `status`=1 ORDER BY ordernum DESC";
			$child_menu = $this->db->get_results($sql,'ASSOC');
			$case_array[$key]['child_menu'] = $child_menu;
			foreach($child_menu as $child){
				$case_array[$key]['curuser'] += $child['curuser'];
				$curuser += $child['curuser'];
			}
		}
		$results['rooms_case'] = $case_array;
		$results['total_user'] = $curuser;
		return array('Flag'=>100,'FlagString'=>'成功','Result'=>$results);
	}
	
// 	public function SetCurUser($case_id,$cur_user){
// 		$sql = "UPDATE ".DB_NAME_REGION.".tbl_rooms_case SET curuser = {$cur_user} WHERE id={$case_id}";
// 		if($this->db->query($sql)){
// 			return array('Flag'=>100,'FlagString'=>'成功');
// 		}else{
// 			return array('Flag'=>101,'FlagString'=>'失败');
// 		}
// 	}
	/*
	public function getHotSites(){
		$sql = "SELECT region_id,city_name,domain,ishot FROM ".DB_NAME_REGION.".tbl_site WHERE ishot=1 AND status>0 ORDER BY city ASC";
		$result = $this->db->get_results($sql,'ASSOC');
		if(empty($result)) $result = array();
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$result);
	}*/
	/*
	public function getSiteList(){
		$sites = array(
			'A'=>array(),'B'=>array(),'C'=>array(),'D'=>array(),'E'=>array(),'F'=>array(),'G'=>array(),'H'=>array(),'I'=>array(),
			'J'=>array(),'K'=>array(),'L'=>array(),'M'=>array(),'N'=>array(),'O'=>array(),'P'=>array(),'Q'=>array(),'R'=>array(),
			'S'=>array(),'T'=>array(),'U'=>array(),'V'=>array(),'W'=>array(),'X'=>array(),'Y'=>array(),'Z'=>array());
		$sql = "SELECT region_id,city_name,domain,ishot FROM ".DB_NAME_REGION.".tbl_site WHERE status>0";
		$result = $this->db->get_results($sql,'ASSOC');
		foreach($sites as $key=>$val){
			foreach($result as $site){
				if(strtoupper($site['domain'][0]) == $key){
					$sites[$key][] = $site;
				}
			}
		}
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$sites);
	}*/
	/*
	public function getSite($site){
		//if(empty($doamin)) $doamin = $_SERVER['HTTP_HOST'];
		//$site = explode('.',$doamin);
		//$site = $site[0];
		if(is_numeric($site)){
			$site = intval($site);
			$sql = "SELECT * FROM ".DB_NAME_REGION.".tbl_site WHERE region_id={$site} AND status>0 LIMIT 1";
		}else{
			$sql = "SELECT * FROM ".DB_NAME_REGION.".tbl_site WHERE domain='{$site}' AND status>0 LIMIT 1";
		}
		$result = $this->db->get_row($sql,'ASSOC');
		if(empty($result)){
			$result = array();
		}else{
			$pic_url = json_decode(urldecode($result['pic_url']),true);
			$result['header_url'] = $pic_url['header_url'];
			$result['footer_url'] = $pic_url['footer_url'];
			$result['item_url'] = $pic_url['item_url'];
			$result['item_icon'] = $pic_url['item_icon'];
			$result['rooms_bg_url'] = $pic_url['rooms_bg_url'];
			$result['rooms_load_url'] = $pic_url['rooms_load_url'];
			$result['video_url'] = $pic_url['video_url'];
		}
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$result);
	}
	
	public function positionSite($ip=''){
		$city = $this->getCity($ip);
		if(empty($city['city_id'])){
			$city['city_id'] = 0;
		}
		$sql = "SELECT * FROM ".DB_NAME_REGION.".tbl_site WHERE region_id={$city['city_id']} OR region_id=0 ORDER BY region_id DESC LIMIT 1";
		$result = $this->db->get_row($sql,'ASSOC');
		if(empty($result))
			$result = array();
		else{
			$pic_url = json_decode($result['pic_url'],true);
			$result['header_url'] = $pic_url['header_url'];
			$result['footer_url'] = $pic_url['footer_url'];
			$result['item_url'] = $pic_url['item_url'];
			$result['item_icon'] = $pic_url['item_icon'];
			$result['rooms_bg_url'] = $pic_url['rooms_bg_url'];
			$result['rooms_load_url'] = $pic_url['rooms_load_url'];
			$result['video_url'] = $pic_url['video_url'];
		}
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$result);
	}*/
	
	public function getParentId($sortId){
		$parent_id = $this->db->get_var("select parent_id from ".DB_NAME_REGION.".tbl_rooms_case where id='".$sortId."'");
		return $parent_id;
	}
	
	//多余的要去掉
	public function getAllClassify(){
		$parents = array();
		$result = $this->db->get_results("select * from ".DB_NAME_REGION.".tbl_rooms_case where status='1'",'ASSOC');
		foreach($result as $k=>$v){
			if($v['parent_id'] == '0'){
				$parents[] = $v;
				unset($result[$k]);
			}
		}
		foreach($parents as $k=>$parent){
			foreach($result as $r){
				if($r['parent_id'] == $parent['id']){
					$parents[$k]['child'][] = $r;
				}
			}
		}
		return $parents;
	}
	
	public function getAllProvince(){
		$sql = "SELECT * FROM ".DB_NAME_REGION.".tbl_province";
		$results = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'所有省份','Result'=>$results);
	}
	
	public function getCities($provinceId){
		if(!is_numeric($provinceId)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_REGION.".tbl_city WHERE province_id='".$provinceId."'";
		$results = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'城市','Result'=>$results);
	}
	
	public function getProvinceName($provinceId){
		if(!is_numeric($provinceId))
			return array('Flag'=>101,'FlagString'=>'参数错误');
		$sql = "SELECT province_name FROM ".DB_NAME_REGION.".tbl_province WHERE province_id='".$provinceId."'";
		$provinceName = $this->db->get_var($sql);
		return array('Flag'=>100,'FlagString'=>'省份名称','provinceName'=>$provinceName);
	}
	
	public function getCityName($cityId){
		if(!is_numeric($cityId))
			return array('Flag'=>101,'FlagString'=>'参数错误');
		$sql = "SELECT city_name FROM ".DB_NAME_REGION.".tbl_city WHERE city_id='".$cityId."'";
		$cityName = $this->db->get_var($sql);
		return array('Flag'=>100,'FlagString'=>'城市名称','cityName'=>$cityName);
	}
	
	public function getAreaName($areaId){
		if(!is_numeric($areaId))
			return array('Flag'=>101,'FlagString'=>'参数错误');
		$sql = "SELECT area_name FROM ".DB_NAME_REGION.".tbl_area WHERE area_id='".$areaId."'";
		$areaName = $this->db->get_var($sql);
		return array('Flag'=>100,'FlagString'=>'地区名称','cityName'=>$areaName);
	}
	
// 	public function getSites(){
// 		$sql = "SELECT id,region_id,city_name,domain,ishot,uptime FROM ".DB_NAME_REGION.".tbl_site WHERE `status`=1 ORDER BY region_id ASC";
// 		$result = $this->db->get_results($sql,'ASSOC');
// 		return array('Flag'=>100,'Result'=>(array)$result);
// 	}
}