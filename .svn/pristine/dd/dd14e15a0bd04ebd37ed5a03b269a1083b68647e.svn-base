<?php

class GroupStyle{
	//数据库指针
	protected $db = null;
	
	public function __construct(){
		$this->db=db::connect(config('database','default'));
	}
	
	//站风格列表
	public function styleList($group_id){
		$where = $group_id>0? "group_id={$group_id}" : "1";
		//得到数量用于分页
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_GROUP.".style WHERE {$where};";
		$total = $this->db->get_var($sql);
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = "SELECT * FROM ".DB_NAME_GROUP.".style WHERE {$where};";
			$result = $this->db->get_results($sql,'ASSOC');
			$style_name_arr=array();
			foreach($result as $key=>$val){
				$sql="SELECT name FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid=".$val['group_id'];
				$result[$key]['group_name']=$this->db->get_var($sql);
				if(isset($style_name_arr[$val['style_id']])){
					$result[$key]['style_name']=$style_name_arr[$val['style_id']];
				}
				else{
					$sql="SELECT name FROM ".DB_NAME_GROUP.".style_setting WHERE id='{$val['style_id']}'";
					$style_name=$this->db->get_var($sql);
					$result[$key]['style_name']=$style_name;
					$style_name_arr[$val['cat_id']]=$style_name;
				}
				$result[$key]['uptime']=date('Y-m-d H:i:s',$val['uptime']);
			}
		}
		return array('Flag'=>100,'FlagString'=>"ok",'Result'=>$result,'Page'=>$page_arr['page']);
	}
	
	//站风格保存
	public function styleSave($post){
		$group_id=$post['group_id'];
		$style_id=intval($post['style_id']);
		if($style_id<=0){
			return array('Flag'=>101,'FlagString'=>'请选择风格');
		}
		
		if($group_id > 0){
			$sql = "SELECT groupid FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid=$group_id";	
			$id = $this->db->get_var($sql);
			if($id <= 0){
				return array('Flag'=>101,'FlagString'=>'不存在该站');
			}
		}
		
		$style_info = $this->styleSettingList($style_id);
		$style_info = $style_info['Result'][0];
		if(empty($style_info)){
			return array('Flag'=>101,'FlagString'=>'不存在该风格');
		}
		
		$sql = "REPLACE INTO ".DB_NAME_GROUP.".style (`group_id`,`bg_status`,`bg_tile`,`bg_align`,`style_id`,`uptime`)VALUES('".$group_id."','".$style_info['bg_status']."','".$style_info['bg_tile']."','".$style_info['bg_align']."','".$style_id."','".time()."');";
		if( ! $this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>"操作失败");
		}
		return array('Flag'=>100,'FlagString'=>"操作成功");
	}

	//风格模板列表
	public function styleSettingList($id){
		$where = $id>0? "id={$id}" : "1";
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".style_setting WHERE {$where};";
		$result = $this->db->get_results($sql,'ASSOC');
		$cat_name_arr=array();
		foreach($result as $key=>$val){
			if($val['cat_id']>0){
				if(isset($cat_name_arr[$val['cat_id']])){
					$result[$key]['cat_name']=$cat_name_arr[$val['cat_id']];
				}
				else{
					$sql="SELECT name FROM ".DB_NAME_GROUP.".style_category WHERE id='{$val['cat_id']}'";
					$cat_name=$this->db->get_var($sql);
					$result[$key]['cat_name']=$cat_name;
					$cat_name_arr[$val['cat_id']]=$cat_name;
				}
			}
			else{
				$result[$key]['cat_name']='未指定';
			}
			$result[$key]['color_style']=unserialize($val['color_style']);
			$result[$key]['uptime']=date('Y-m-d H:i:s',$val['uptime']);
		}
		return array('Flag'=>100,'FlagString'=>"ok",'Result'=>$result);
	}
	
	//风格模板保存
	public function styleSettingSave($post){
		if($post['name']==''){
			return array('Flag'=>101,'FlagString'=>'风格名称不能为空');
		}
		if($post['main_text']==''){
			return array('Flag'=>101,'FlagString'=>'主文字不能为空');
		}
		if($post['minor_text']==''){
			return array('Flag'=>101,'FlagString'=>'次文字不能为空');
		}
		if($post['border']==''){
			return array('Flag'=>101,'FlagString'=>'边框不能为空');
		}
		if($post['bottom_text']==''){
			return array('Flag'=>101,'FlagString'=>'底框文字不能为空');
		}
		if($post['page_bg']==''){
			return array('Flag'=>101,'FlagString'=>'页面背景不能为空');
		}
		if($post['content_bg']==''){
			return array('Flag'=>101,'FlagString'=>'内容背景不能为空');
		}
		if(intval($post['bg_img'])>0){
			if(!in_array($post['bg_status'],array(0,1))){
				return array('Flag'=>101,'FlagString'=>'请选择大背景图是否使用');
			}
			if(!in_array($post['bg_tile'],array(0,1))){
				return array('Flag'=>101,'FlagString'=>'请选择大背景图显示方式');
			}
			if(!in_array($post['bg_align'],array(1,2,3))){
				return array('Flag'=>101,'FlagString'=>'请选择大背景图对齐方式');
			}
		}
		if(intval($post['banner'])>0){
			if(!in_array($post['banner_status'],array(0,1))){
				return array('Flag'=>101,'FlagString'=>'请选择顶部通栏是否使用');
			}
		}
		if(intval($post['thumb'])<=0){
			return array('Flag'=>101,'FlagString'=>'请选择风格缩略图');
		}
		$color_style=array(
			'main_text'=>$post['main_text'],
			'minor_text'=>$post['minor_text'],
			'border'=>$post['border'],
			'bottom_text'=>$post['bottom_text'],
			'page_bg'=>$post['page_bg'],
			'content_bg'=>$post['content_bg']
		);
		$color_style=serialize($color_style);
		
		$where = '';
		if($post['id'] > 0){
			$where = " AND id!={$post['id']}";
		}
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".style_setting WHERE name='{$post['name']}' {$where}";
		$id = $this->db->get_var($sql);
		if($id > 0){
			return array('Flag'=>102,'FlagString'=>'该模板名称已存在');
		}
		$sql = "REPLACE INTO ".DB_NAME_GROUP.".style_setting (`id`,`name`,`cat_id`,`color_style`,`bg_img`,`bg_status`,`bg_tile`,`bg_align`,`banner`,`banner_status`,`thumb`,`uptime`)VALUES('{$post['id']}','".addslashes($post['name'])."','".intval($post['cat_id'])."','".addslashes($color_style)."','".intval($post['bg_img'])."','".intval($post['bg_status'])."','".intval($post['bg_tile'])."','".intval($post['bg_align'])."','".intval($post['banner'])."','".intval($post['banner_status'])."','".intval($post['thumb'])."','".time()."');";
		$type = $post['id'] > 0 ? '修改' : '添加';
		if( ! $this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>"{$type}失败");
		}
		return array('Flag'=>100,'FlagString'=>"{$type}成功");
	}
	
	//模板分类列表
	public function styleCategoryList(){
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".style_category";
		$result = $this->db->get_results($sql,'ASSOC');
		return array('Flag'=>100,'FlagString'=>"ok",'Result'=>$result);
	}
	
	//模板分类保存
	public function styleCategorySave($post){
		$name = addslashes($post['name']);
		if($name == ''){
			return array('Flag'=>101,'FlagString'=>'分类名称不能为空');
		}
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".style_category WHERE name='$name'";
		$id = $this->db->get_var($sql);
		if($id > 0){
			return array('Flag'=>102,'FlagString'=>'该分类名称已存在');
		}
		$sql = "INSERT INTO ".DB_NAME_GROUP.".style_category (name) VALUES ('$name')";
		if( ! $this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>"添加失败");
		}
		$id=$this->db->insert_id();
		return array('Flag'=>100,'FlagString'=>"添加成功",'Id'=>$id);
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
