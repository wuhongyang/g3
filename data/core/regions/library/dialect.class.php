<?php
/**
 * 后台分站方言类
 * @author pgp
 * @copyright aodiansoft.com
 * @version $Id$
 */
class dialect extends Regions
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
	public function getDialectList($array){
		$where = ' WHERE TRUE';
		if(isset($array['province']) && $array['province']>0)
			$where .= ' AND province="'.$array['province'].'"';
		if(isset($array['city']) && $array['city']>0)
			$where .= ' AND city="'.$array['city'].'"';
		if(isset($array['status']) && $array['status']>-1)
			$where .= ' AND status="'.$array['status'].'"';
		$total = $this->db->get_var('SELECT COUNT(1) FROM '.DB_NAME_REGION.'.tbl_dialect '.$where);
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = 'SELECT * FROM '.DB_NAME_REGION.'.tbl_dialect '.$where.' ORDER BY id DESC LIMIT '.$page_arr['limit'];
			$list = $this->db->get_results($sql,'ASSOC');
			foreach($list as $k=>$v){
				$pNames = $this->getProvinceName($v['province']);
				$cNames = $this->getCityName($v['city']);
				$list[$k]['provinceName'] = $pNames['provinceName'];
				$list[$k]['cityName'] = $cNames['cityName'];
			}
			$list['page'] = $page_arr['page'];
		}
		return array('Flag'=>100,'FlagString'=>'分站方言','Result'=>$list,'Region'=>$this->getOpenCity());
	}
	
	public function getDialectInfo($id){
		if($id > 0){
			$sql = 'SELECT id,province,city,area,region_id,contents,status FROM '.DB_NAME_REGION.'.tbl_dialect WHERE id="'.$id.'"';
			$info = $this->db->get_row($sql,'ASSOC');
		}
		return array('Flag'=>100,'FlagString'=>'分站方言详情','Info'=>$info,'Region'=>$this->getOpenCity());	
	}
	
	//方言添加
	public function dialectAdd($data){
		if($data['province']==-1 || $data['city']==-1 || $data['content']==-1)
			return array('Flag'=>101,'FlagString'=>'参数错误');
		$region_id = ($data['area']==-1) ? $data['city'] : $data['area'];
		$sql = 'INSERT INTO '.DB_NAME_REGION.'.tbl_dialect(`province`,`city`,`area`,`region_id`,`contents`,`uptime`,`status`) VALUES("'.$data['province'].'","'.$data['city'].'","'.$data['area'].'","'.$region_id.'","'.$data['contents'].'","'.time().'","'.$data['status'].'")';
		if($this->db->query($sql))
			return array('Flag'=>100,'FlagString'=>'添加成功');
		return array('Flag'=>102,'FlagString'=>'添加失败');
	}
	
	public function dialectUpdate($data,$id){
		if($data['province']==-1 || $data['city']==-1 || $data['content']==-1 || $id<=0)
			return array('Flag'=>101,'FlagString'=>'参数错误');
		$region_id = ($data['area']==-1) ? $data['city'] : $data['area'];
		$sql = 'UPDATE '.DB_NAME_REGION.'.tbl_dialect SET province="'.$data['province'].'",city="'.$data['city'].'",area="'.$data['area'].'",region_id="'.$region_id.'",contents="'.$data['contents'].'",status="'.$data['status'].'" WHERE id="'.$id.'"';
		if($this->db->query($sql))
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
