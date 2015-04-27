<?php
/**
 * 后台地域分站类别类
 * @author pgp
 * @copyright aodiansoft.com
 * @version $Id$
 */
class site_category extends Regions
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
	public function siteCategoryCaseList($array){
		$where = ' WHERE parent_id=0';
		if(isset($array['province']) && $array['province']>0)
			$where .= ' AND province="'.$array['province'].'"';
		if(isset($array['city']) && $array['city']>0)
			$where .= ' AND city="'.$array['city'].'"';
		if(isset($array['status']) && $array['status']>-1)
			$where .= ' AND status="'.$array['status'].'"';
		$total = $this->db->get_var('SELECT COUNT(1) FROM '.DB_NAME_REGION.'.tbl_rooms_case '.$where);
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = 'SELECT id,province,city,area,name,uptime,status FROM '.DB_NAME_REGION.'.tbl_rooms_case '.$where.' ORDER BY ordernum ASC LIMIT '.$page_arr['limit'];
			$list = $this->db->get_results($sql,'ASSOC');
			foreach($list as $k=>$v){
				$pNames = $this->getProvinceName($v['province']);
				$cNames = $this->getCityName($v['city']);
				//$aNames = $this->getAreaName($v['area']);
				$list[$k]['provinceName'] = $pNames['provinceName'];
				$list[$k]['cityName'] = $cNames['cityName'];
				//$list[$k]['areaName'] = $aNames['cityName'];
			}
			$list['page'] = $page_arr['page'];
		}
		return array('Flag'=>100,'FlagString'=>'分站类别','Result'=>$list,'Region'=>$this->getOpenCity());
	}
	
	//得到分类详情
	public function getInfo($id){
		if($id > 0){
			$sql = 'SELECT id,province,city,area,region_id,name,status FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE parent_id=0 AND id="'.$id.'"';
			$info = $this->db->get_row($sql);
			$pNames = $this->getProvinceName($info['province']);
			$cNames = $this->getCityName($info['city']);
			$info['provinceName'] = $pNames['provinceName'];
			$info['cityName'] = $cNames['cityName'];
		}
		return array('Flag'=>100,'FlagString'=>'一级分类详情','Info'=>$info,'Region'=>$this->getOpenCity());
	}
	
	//一级分类修改
	public function saveCaseUpdate($data,$id){
		if($data['province']==-1 || $data['city']==-1 || $data['name']=='')
			return array('Flag'=>101,'FlagString'=>'参数错误');
		$region_id = ($data['area']==-1) ? $data['city'] : $data['area'];
		$sql = 'UPDATE '.DB_NAME_REGION.'.tbl_rooms_case SET `province`="'.$data['province'].'",`city`="'.$data['city'].'",`area`="'.$data['area'].'",region_id="'.$region_id.'",`name`="'.$data['name'].'",`status`="'.$data['status'].'" WHERE id="'.$id.'" AND parent_id=0';
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'修改成功');
		}
		return array('Flag'=>102,'FlagString'=>'修改失败');
	}
	
	//一级分类添加
	public function saveCaseAdd($data){
		//检测参数合法性
		if($data['province']==-1 || $data['city']==-1 || $data['name']=='')
			return array('Flag'=>101,'FlagString'=>'参数错误');
		//检测房间分类名称是否存在
		$sql = 'SELECT COUNT(1) FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE parent_id="0" AND `name`="'.$data['name'].'"';
		if($this->db->get_var($sql) > 0)
			return array('Flag'=>103,'FlagString'=>'存在相同的房间分类名称');
		$region_id = ($data['area']==-1) ? $data['city'] : $data['area'];
		$sql = 'INSERT INTO '.DB_NAME_REGION.'.tbl_rooms_case(`province`,`city`,`area`,`region_id`,`parent_id`,`name`,`curuser`,`ordernum`,`status`,`uptime`) VALUES("'.$data['province'].'","'.$data['city'].'","'.$data['area'].'","'.$region_id.'","0","'.$data['name'].'","0","0","'.$data['status'].'","'.time().'")';
		$result = $this->db->query($sql);
		$ordernum = $this->db->insert_id();
		$sql = 'UPDATE '.DB_NAME_REGION.'.tbl_rooms_case SET ordernum="'.$ordernum.'" WHERE id="'.$ordernum.'"';
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'添加成功');
		}
		return array('Flag'=>102,'FlagString'=>'添加失败');
	}
	
	//查看下级分类
	public function showSubSiteCategory($id){
		$sql = 'SELECT name,province,city,region_id FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE id="'.$id.'"';
		$cates = $this->db->get_row($sql,'ASSOC');
		$pNames = $this->getProvinceName($cates['province']);
		$cNames = $this->getCityName($cates['city']);
		$cates['provinceName'] = $pNames['provinceName'];
		$cates['cityName'] = $cNames['cityName'];
		$sql = 'SELECT COUNT(1) FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE parent_id="'.$id.'"';
		$total = $this->db->get_var($sql);
		if($total){
			$page_arr = $this->showPage($total);
			$sql = 'SELECT id,name,uptime,status,parent_id FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE parent_id="'.$id.'" ORDER BY ordernum ASC LIMIT '.$page_arr['limit'];
			$list = $this->db->get_results($sql,'ASSOC');
			$list['page'] = $page_arr['page'];
		}
		return array('Flag'=>100,'FlagString'=>'查看下级分类','SubCategory'=>$list,'Cates'=>$cates);
	}
	
	//排序号互换
	private function swap($orignal,$swap){
		$sql = 'UPDATE '.DB_NAME_REGION.'.tbl_rooms_case SET ordernum="'.$swap['ordernum'].'" WHERE id="'.$orignal['id'].'"';
		$this->db->query($sql);
		$sql = 'UPDATE '.DB_NAME_REGION.'.tbl_rooms_case SET ordernum="'.$orignal['ordernum'].'" WHERE id="'.$swap['id'].'"';
		return $this->db->query($sql);
	}
	
	//当前排序号
	private function getCurrentOrderNum($id){
		$sql = 'SELECT ordernum FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE id="'.$id.'"';
		return $this->db->get_var($sql);
	}
	
	//一级分类上移
	public function caseUp($id){
		if($id <= 0)
			return array('Flag'=>101,'FlagString'=>'参数错误');
		//当前排序号
		$current_num = $this->getCurrentOrderNum($id);
		//找到小于当前排序号的最大排序号
		$sql = 'SELECT id,ordernum FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE ordernum<"'.$current_num.'" AND parent_id=0 ORDER BY ordernum DESC LIMIT 1';
		$res = $this->db->get_row($sql,'ASSOC');
		if(!$res)
			return array('Flag'=>103,'FlagString'=>'已经置顶');
		
		//排序号互换
		$result = $this->swap(array('id'=>$id,'ordernum'=>$current_num),array('id'=>$res['id'],'ordernum'=>$res['ordernum']));
		if($result)
			return array('Flag'=>100,'FlagString'=>'上移成功');
		return array('Flag'=>102,'FlagString'=>'上移失败');
	}
	
	//一级分类下移
	public function caseDown($id){
		if($id <= 0)
			return array('Flag'=>101,'FlagString'=>'参数错误');
		//当前排序号
		$current_num = $this->getCurrentOrderNum($id);
		//找到小于当前排序号的最小排序号
		$sql = 'SELECT id,ordernum FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE ordernum>"'.$current_num.'" AND parent_id=0 ORDER BY ordernum ASC LIMIT 1';
		$res = $this->db->get_row($sql,'ASSOC');
		if(!$res)
			return array('Flag'=>103,'FlagString'=>'已经置底');
		//排序号互换
		$result = $this->swap(array('id'=>$id,'ordernum'=>$current_num),array('id'=>$res['id'],'ordernum'=>$res['ordernum']));
		
		if($result)
			return array('Flag'=>100,'FlagString'=>'下移成功');
		return array('Flag'=>102,'FlagString'=>'下移失败');
	}
	
	//一级分类置顶
	public function caseTop($id){
		if($id <= 0)
			return array('Flag'=>101,'FlagString'=>'参数错误');
		//当前排序号
		$current_num = $this->getCurrentOrderNum($id);
		//取到最小的排序号
		$sql = 'SELECT id,ordernum FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE ordernum<"'.$current_num.'" AND parent_id=0 ORDER BY ordernum ASC LIMIT 1';
		$res = $this->db->get_row($sql,'ASSOC');
		if(!$res)
			return array('Flag'=>103,'FlagString'=>'已经置顶');
		//排序号互换
		$result = $this->swap(array('id'=>$id,'ordernum'=>$current_num),array('id'=>$res['id'],'ordernum'=>$res['ordernum']));
		if($result)
			return array('Flag'=>100,'FlagString'=>'置顶成功');
		return array('Flag'=>102,'FlagString'=>'置顶失败');
	}
	
	//二级分类详情
	public function getSubCategoryInfo($caseId,$id){
		if($caseId <= 0)
			return array('Flag'=>101,'FlagString'=>'参数错误');
		//得到一给分类名称
		$caseInfo = $this->getInfo($caseId);
		if($id > 0)
			$info = $this->db->get_row('SELECT id,name,status FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE parent_id="'.$caseId.'" AND id="'.$id.'"');
		if($caseInfo)
			return array('Flag'=>100,'FlagString'=>'分类详情','CaseInfo'=>$caseInfo['Info'],'Info'=>$info);
		return array('Flag'=>102,'FlagString'=>'分类详情');
	}
	
	public function subCategoryUpdate($data,$id){
		if($id<=0 || $data['name']=='')
			return array('Flag'=>101,'FlagString'=>'参数错误');
		//检测名称是否存在
		$sql = 'SELECT COUNT(1) FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE parent_id="'.$data['parent_id'].'" AND name="'.$data['name'].'" AND id!="'.$id.'"';
		if($this->db->get_var($sql) > 0)
			return array('Flag'=>103,'FlagString'=>'存在相同名称');
		$sql = 'UPDATE '.DB_NAME_REGION.'.tbl_rooms_case SET name="'.$data['name'].'",`region_id`="'.$data['region_id'].'",status="'.$data['status'].'" WHERE parent_id="'.$data['parent_id'].'" AND id="'.$id.'"';
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'二级分类修改成功');
		}
		return array('Flag'=>102,'FlagString'=>'二级分类修改失败');
	}
	
	public function subCategoryAdd($data){
		if($data['name'] == '')
			return array('Flag'=>101,'FlagString'=>'参数错误');
		//检测名称是否存在
		$sql = 'SELECT COUNT(1) FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE parent_id="'.$data['parent_id'].'" AND name="'.$data['name'].'"';
		if($this->db->get_var($sql) > 0)
			return array('Flag'=>103,'FlagString'=>'存在相同名称');
		$sql = 'INSERT INTO '.DB_NAME_REGION.'.tbl_rooms_case(`province`,`city`,`area`,`region_id`,`parent_id`,`curuser`,`name`,`status`,`uptime`) VALUES("0","0","0","'.$data['region_id'].'","'.$data['parent_id'].'","0","'.$data['name'].'","'.$data['status'].'","'.time().'")';
		$result = $this->db->query($sql);
		$ordernum = $this->db->insert_id();
		$sql = 'UPDATE '.DB_NAME_REGION.'.tbl_rooms_case SET ordernum="'.$ordernum.'" WHERE id="'.$ordernum.'"';
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'二级分类添加成功');
		}
		return array('Flag'=>102,'FlagString'=>'二级分类添加失败');
	}
	
	//二级分类上移
	public function subCategoryUp($id,$parent_id){
		if($id <= 0)
			return array('Flag'=>101,'FlagString'=>'参数错误');
		//当前排序号
		$current_num = $this->getCurrentOrderNum($id);
		//找到小于当前排序号的最大排序号
		$sql = 'SELECT id,ordernum FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE ordernum<"'.$current_num.'" AND parent_id="'.$parent_id.'" ORDER BY ordernum DESC LIMIT 1';
		$res = $this->db->get_row($sql,'ASSOC');
		if(!$res)
			return array('Flag'=>103,'FlagString'=>'已经置顶');
		//排序号互换
		$result = $this->swap(array('id'=>$id,'ordernum'=>$current_num),array('id'=>$res['id'],'ordernum'=>$res['ordernum']));
		if($result)
			return array('Flag'=>100,'FlagString'=>'上移成功');
		return array('Flag'=>102,'FlagString'=>'上移失败');
	}
	
	//二级分类下移
	public function subCategoryDown($id,$parent_id){
		if($id <= 0)
			return array('Flag'=>101,'FlagString'=>'参数错误');
		//当前排序号
		$current_num = $this->getCurrentOrderNum($id);
		//找到小于当前排序号的最小排序号
		$sql = 'SELECT id,ordernum FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE ordernum>"'.$current_num.'" AND parent_id="'.$parent_id.'" ORDER BY ordernum ASC LIMIT 1';
		$res = $this->db->get_row($sql,'ASSOC');
		if(!$res)
			return array('Flag'=>103,'FlagString'=>'已经置底');
		//排序号互换
		$result = $this->swap(array('id'=>$id,'ordernum'=>$current_num),array('id'=>$res['id'],'ordernum'=>$res['ordernum']));
		if($result)
			return array('Flag'=>100,'FlagString'=>'下移成功');
		return array('Flag'=>102,'FlagString'=>'下移失败');
	}
	
	//二级分类置顶
	public function subCategoryTop($id,$parent_id){
		if($id <= 0)
			return array('Flag'=>101,'FlagString'=>'参数错误');
		//当前排序号
		$current_num = $this->getCurrentOrderNum($id);
		//取到最小的排序号
		$sql = 'SELECT id,ordernum FROM '.DB_NAME_REGION.'.tbl_rooms_case WHERE ordernum<"'.$current_num.'" AND parent_id="'.$parent_id.'" ORDER BY ordernum ASC LIMIT 1';
		$res = $this->db->get_row($sql,'ASSOC');
		if(!$res)
			return array('Flag'=>103,'FlagString'=>'已经置顶');
		//排序号互换
		$result = $this->swap(array('id'=>$id,'ordernum'=>$current_num),array('id'=>$res['id'],'ordernum'=>$res['ordernum']));
		if($result)
			return array('Flag'=>100,'FlagString'=>'置顶成功');
		return array('Flag'=>102,'FlagString'=>'置顶失败');
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
