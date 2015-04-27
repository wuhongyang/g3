<?php
class Commodity{

	protected $db;
	
	function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	public function lists($search){
	    if($search['tpl_id']){
	       $where = " AND cd.tpl_id = ".$search['tpl_id'];
	    }
		$total = $this->db->get_var('SELECT COUNT(1) FROM '.DB_NAME_TPL.'.commodity as cd WHERE 1 '.$where);
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = 'SELECT cd.*,cg.name AS category_name FROM '.DB_NAME_TPL.'.commodity AS cd LEFT JOIN '.DB_NAME_SHOP.'.`category` AS cg ON cd.`category` = cg.`id` WHERE 1 '.$where.' ORDER BY cd.uptime ASC LIMIT '.$page_arr['limit'];
            $list = $this->db->get_results($sql,'ASSOC');
		}
		return array('Flag'=>100,'FlagString'=>'商品类别列表','List'=>$list,'Page'=>$page_arr['page']);
	}
	
	public function info($id){
		$id = intval($id);
		$sql = "SELECT * FROM ".DB_NAME_TPL.".commodity WHERE id={$id}";
		$info = $this->db->get_row($sql, ASSOC);
		if(empty($info)){
			return array('Flag'=>101,'FlagString'=>'获取商品详情失败');
		}
		return array('Flag'=>100,'FlagString'=>'获取商品详情成功','Info'=>$info);
	}

	public function getByCategory($category){
		$category = intval($category);
		$sql = "SELECT * FROM ".DB_NAME_TPL.".commodity WHERE `category`={$category} AND `status`=1";
		$list = $this->db->get_results($sql, ASSOC);
		return array('Flag'=>100,'FlagString'=>'获取类别商品','Commodity'=>$list);
	}

	public function add($data){
		$data = $this->filterParam($data);
		$rst = $this->checkParam($data);
		if($rst['Flag'] != 100){
			return $rst;
		}
		$time = time();
		$sql = "INSERT INTO ".DB_NAME_TPL.".commodity(`bigcase_id`,`case_id`,`parent_id`,`name`,`desc`,`category`,`role_id`,`scope`,`type`,`price`,`expire`,`image`,`image_md5`,`flash`,`flash_md5`,`room_image`,`room_image_md5`,`tip`,`is_gift`,`status`,`uptime`,`tpl_id`) VALUES({$data['bigcase_id']},{$data['case_id']},{$data['parent_id']},'{$data['name']}','{$data['desc']}',{$data['category']},{$data['role_id']},{$data['scope']},{$data['type']},{$data['price']},{$data['expire']},'{$data['image']}','{$data['image_md5']}','{$data['flash']}','{$data['flash_md5']}','{$data['room_image']}','{$data['room_image_md5']}','{$data['tip']}',{$data['is_gift']},{$data['status']},{$time},{$data['tpl_id']})";
		if(!$this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'添加商品失败');
		}
		return array('Flag'=>100,'FlagString'=>'添加商品成功');
	}

	public function edit($id,$data){
		$id = intval($id);
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$data = $this->filterParam($data);
		$sql = "UPDATE ".DB_NAME_TPL.".commodity SET `bigcase_id`={$data['bigcase_id']},`case_id`={$data['case_id']},`parent_id`={$data['parent_id']},`name`='{$data['name']}',`desc`='{$data['desc']}',`category`={$data['category']},`role_id`={$data['role_id']},`scope`={$data['scope']},`type`={$data['type']},`price`={$data['price']},`expire`={$data['expire']},`image`='{$data['image']}',`image_md5`='{$data['image_md5']}',`flash`='{$data['flash']}',`flash_md5`='{$data['flash_md5']}',`room_image`='{$data['room_image']}',`room_image_md5`='{$data['room_image_md5']}',`tip`='{$data['tip']}',`is_gift`={$data['is_gift']},`status`={$data['status']} WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'编辑商品失败');
		}
		return array('Flag'=>100,'FlagString'=>'编辑商品成功');
	}

	private function filterParam($data){
		$data['bigcase_id'] = intval($data['bigcase_id']);
		$data['case_id'] = intval($data['case_id']);
		$data['parent_id'] = intval($data['parent_id']);
		$data['name'] = addslashes(htmlspecialchars(trim($data['name'])));
		$data['desc'] = addslashes(htmlspecialchars(trim($data['desc'])));
		$data['category'] = intval($data['category']);
		$data['role_id'] = intval($data['role_id']);
		$data['scope'] = intval($data['scope']);
		$data['type'] = intval($data['type']);
		$data['price'] = intval($data['price']);
		$data['expire'] = intval($data['expire']);
		$data['image_md5'] = '';
        $data['tpl_id'] = intval($data['tpl_id']);
		if($data['pic'] > 0){
			$data['image'] = json_encode(array($data['pic_category'],$data['pic']));
			$pic = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1,'id'=>$data['pic']))));
			$data['image_md5'] = $pic['lists'][0]['img_path'];
		}
		$data['flash_md5'] = '';
		if($data['swf'] > 0){
			$data['flash'] = json_encode(array($data['swf_category'],$data['swf']));
			$pic = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1,'id'=>$data['swf']))));
			$data['flash_md5'] = $pic['lists'][0]['img_path'];
		}
		$data['room_image_md5'] = '';
		if($data['room_image'] > 0){
			$room_image = $data['room_image'];
			$data['room_image'] = json_encode(array($data['room_image_category'],$data['room_image']));
			$pic = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1,'id'=>$room_image))));
			$data['room_image_md5'] = $pic['lists'][0]['img_path'];
		}
		$data['tip'] = addslashes(htmlspecialchars(trim($data['tip'])));
		$data['gift'] = array();
		if($data['is_gift'] == 1){
			foreach ($data['cates'] as $key => $val) {
				$data['gift'][$key] = array('category'=>$val,'commodity'=>$data['commodities'][$key]);
			}
		}else{
			$data['is_gift'] = 0;
		}
		$data['gift'] = json_encode($data['gift']);
		$data['status'] = intval($data['status']);
		return $data;
	}

	private function checkParam($data){
		if($data['bigcase_id'] < 1){
			return array('Flag'=>101,'FlagString'=>'请选择科目');
		}
		if($data['case_id'] < 1){
			return array('Flag'=>101,'FlagString'=>'请选择科目');
		}
		if($data['parent_id'] < 1){
			return array('Flag'=>101,'FlagString'=>'请选择科目');
		}
		if(empty($data['name'])){
			return array('Flag'=>101,'FlagString'=>'请填写商品名称');
		}
		if(empty($data['desc'])){
			return array('Flag'=>101,'FlagString'=>'请填写商品描述');
		}
		if($data['category'] < 1){
			return array('Flag'=>101,'FlagString'=>'请选择商品分类');
		}
		if(!in_array($data['scope'], array(1,2))){
			return array('Flag'=>101,'FlagString'=>'请选择商品作用域');
		}
		return array('Flag'=>100);
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
