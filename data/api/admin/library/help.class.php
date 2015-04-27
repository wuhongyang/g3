<?php
class help
{
	private $db;

	function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	function classify_list($arr){
		$status = isset($arr['Status'])?$arr['Status']:-2;
		$id = $arr['Id'];
		$type = $arr['Type'];
		if((!in_array($status, array(-1, 0, 1)) && !$id)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		if($type){
			$extra =  " AND type = ".$type;
		}
		$sql = "SELECT COUNT(*) FROM `".DB_NAME_HELP."`.`help_classify` WHERE 1 ";
		if($id >0){
			$extra .= " AND id = {$id}";
		}
		if($status != -1){
			$extra .= " AND status = {$status}";
		}
		
		/*
		if($id && $status != ""){
			if($status == -1){
				$sql = "SELECT COUNT(*) FROM `".DB_NAME_HELP."`.`help_classify` WHERE id = '".$id."'";
			}else{
				$sql = "SELECT COUNT(*) FROM `".DB_NAME_HELP."`.`help_classify` WHERE id = '".$id."' AND status= '".$status."'";
			}	
		}elseif($id){
			$sql = "SELECT COUNT(*) FROM `".DB_NAME_HELP."`.`help_classify` WHERE id = '".$id."'";
		}else{
			if($status == -1){
				$sql = "SELECT COUNT(*) FROM `".DB_NAME_HELP."`.`help_classify`";
			}else{
				$sql = "SELECT COUNT(*) FROM `".DB_NAME_HELP."`.`help_classify` WHERE status = '".$status."'";
			}
		}*/
		$total = $this->db->get_var($sql.$extra);
		$result = array();
		if($total > 0 ){
			$sql = "SELECT * FROM `".DB_NAME_HELP."`.`help_classify` WHERE 1 ";
			/*if($id && $status != ""){
				if($status == -1){
					$sql = "SELECT * FROM `".DB_NAME_HELP."`.`help_classify` WHERE id ='".$id."'";
				}else{
					$sql = "SELECT * FROM `".DB_NAME_HELP."`.`help_classify` WHERE id ='".$id."' AND status= '".$status."'";
				}
			}elseif($id){
				$sql = "SELECT * FROM `".DB_NAME_HELP."`.`help_classify` WHERE id ='".$id."'";
			}else{
				if($status == -1){
					$sql = "SELECT * FROM `".DB_NAME_HELP."`.`help_classify`";
				}else{
					$sql = "SELECT * FROM `".DB_NAME_HELP."`.`help_classify` WHERE status = '".$status."'";
				}
			}*/
			$data = $this->db->get_results($sql.$extra, "ASSOC");
			foreach($data as $key=>$one_data){
// 				$one_data["create_time"] = date('Y-m-d H:i:s',$one_data["create_time"]);
				$result[$key] = $one_data;
			}
		}
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$result);
	}
	
	function save_classify($arr){
		if($arr['Id']){
			return $this->update_classify($arr);
		}else{
			return $this->add_classify($arr);
		}
	}
	
	private function add_classify($arr){
		$name = $arr['Name'];
		$status = $arr['Status'];
		$type = $arr['Type'];
		if(!$name){
			return array('Flag'=>101,'FlagString'=>'不能为空');
		}
		if(!$name || !in_array($status, array(0, 1)) || !$type){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT COUNT(*) FROM `".DB_NAME_HELP."`.`help_classify` WHERE `name` = '".$name."'";
		$exist = $this->db->get_var($sql);
		if($exist){
			return array('Flag'=>102,'FlagString'=>'已存在分类名称');
		}
		$sql = "INSERT INTO `".DB_NAME_HELP."`.`help_classify`(`id`,`name`,`create_time`,`status`,`type`)
				 VALUES ( NULL,'".$name."','".time()."', '".$status."', '".$type."')";
		$done = $this->db->query($sql);
		if($done)
			return array('Flag'=>100,'FlagString'=>'添加成功');
		else
			return array('Flag'=>102,'FlagString'=>'添加失败');
	}
	
	private function update_classify($arr){
		$id	  = $arr['Id'];
		$name = $arr['Name'];
		$status = $arr['Status'];
		$type = $arr['Type'];
		if(!($id && $name && in_array($status, array(0, 1)) && $type)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql = "SELECT COUNT(*) FROM `".DB_NAME_HELP."`.`help_classify` WHERE id = ".$id;
		$count = $this->db->get_var($sql);
		if($count != 1){
			return array('Flag'=>103,'FlagString'=>'无此内容');
		}
		$sql = "UPDATE `".DB_NAME_HELP."`.`help_classify` SET `name`='".$name."',`status`='".$status."',`type`='".$type."' WHERE `id`='".$id."'";
		
		$done = $this->db->query($sql);
		if($done)
			return array('Flag'=>100,'FlagString'=>'更新成功');
		else
			return array('Flag'=>102,'FlagString'=>'更新失败');
	}
	
	function del_classify($id){
		$sql = "DELETE FROM `".DB_NAME_HELP."`.`help_classify` WHERE `id`='".$id."'";
		$done = $this->db->query($sql);
		if($done)
			return array('Flag'=>100,'FlagString'=>'删除成功');
		else
			return array('Flag'=>102,'FlagString'=>'删除失败');
	}
	
	function substance_list($arr){
		$classify_id = $arr['ClassifyId'];
		$id = $arr['Id'];
		if($id>0){
			$sql = "SELECT a.*,b.`name` as classify_name FROM `".DB_NAME_HELP."`.`help_substance` a LEFT JOIN `".DB_NAME_HELP."`.`help_classify` b ON a.`classify_id` = b.`id` WHERE a.id = '".$id."'";
			$data = $this->db->get_results($sql, "ASSOC");
			return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$data);
		}else{
			if($classify_id){
				$sql = "SELECT COUNT(*) FROM `".DB_NAME_HELP."`.`help_substance`  WHERE classify_id = '".$classify_id."'";
			}else{
				$sql = "SELECT COUNT(*) FROM `".DB_NAME_HELP."`.`help_substance`";
			}
			
			$total = $this->db->get_var($sql);
			$result = array();
			if($total > 0){
				$page_arr = $this->showpage($total);
				if($classify_id){
					$sql = "SELECT a.`id`,a.`classify_id`,a.`title`,a.`update_time`,b.`name` as classify_name FROM
						`".DB_NAME_HELP."`.`help_substance` a LEFT JOIN `".DB_NAME_HELP."`.`help_classify` b ON a.`classify_id` = b.`id` WHERE a.classify_id = '".$classify_id."' LIMIT ".$page_arr['limit'];
				}else{
					$sql = "SELECT a.`id`,a.`classify_id`,a.`title`,a.`update_time`,b.`name` as classify_name FROM
						`".DB_NAME_HELP."`.`help_substance` a LEFT JOIN `".DB_NAME_HELP."`.`help_classify` b ON a.`classify_id` = b.`id` LIMIT ".$page_arr['limit'];
				}
				$data = $this->db->get_results($sql, "ASSOC");
				foreach($data as $key=>$one_data){
// 					$one_data["update_time"] = date('Y-m-d H:i:s',$one_data["update_time"]);
					$result[$key] = $one_data;
				}
			}
			return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$result,'Page'=>$page_arr['page']);
		}
	}
	

	function save_substance($arr){
		if($arr['Id']){
			return $this->update_substance($arr);
		}else{
			return $this->add_substance($arr);
		}
	}
	
	private function add_substance($arr){
		$content 		= $arr['Content'];
		$classify_id 	= $arr['ClassifyId'];
		$time 			= $arr['Time'];
		$title 			= $arr['Title'];
		$top 			= $arr['Top'];
		if(!($classify_id && $title)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$time = $time?$time:time();
		if(!(strlen($time) == 10 && is_numeric($time))){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "INSERT INTO `".DB_NAME_HELP."`.`help_substance`(`id`,`classify_id`,`title`,`update_time`,`content`,`top`) VALUES ( NULL,'".$classify_id."','".$title."','".$time."','".$content."','".$top."')";
		$done = $this->db->query($sql);
		if($done)
			return array('Flag'=>100,'FlagString'=>'添加成功');
		else
			return array('Flag'=>102,'FlagString'=>'添加失败');
	}
	
	private function update_substance($arr){
		$content 		= $arr['Content'];
		$classify_id 	= $arr['ClassifyId'];
		$title 			= $arr['Title'];
		$id				= $arr['Id'];
		$top			= $arr['Top'];
		if(!$id || !($content || $classify_id || $title)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql = "SELECT COUNT(*) FROM `".DB_NAME_HELP."`.`help_substance` WHERE id = ".$id;
		$count = $this->db->get_var($sql);
		if($count != 1){
			return array('Flag'=>103,'FlagString'=>'无此内容');
		}
		
		$sql = "UPDATE `".DB_NAME_HELP."`.`help_substance` SET ";
		if($content){
			$sql .= "`content`='".$content."',";
		}
		if($title){
			$sql .= "`title`='".$title."',";
		}
		if($classify_id){
			$sql .= "`classify_id`='".$classify_id."',";
		}
		if($top){
			$sql .= "`top`='".$top."',";
		}
		$sql .= "`update_time`='".time()."'";
		$sql .= "WHERE `id`='".$id."'";
		$done = $this->db->query($sql);
		if($done)
			return array('Flag'=>100,'FlagString'=>'更新成功');
		else
			return array('Flag'=>102,'FlagString'=>'更新失败');
	}
	
	function del_substance($id){
		$sql = "DELETE FROM `".DB_NAME_HELP."`.`help_substance` WHERE `id`='".$id."'";
		$done = $this->db->query($sql);
		if($done)
			return array('Flag'=>100,'FlagString'=>'删除成功');
		else
			return array('Flag'=>102,'FlagString'=>'删除失败');
	}
	
	function get_link_list($data){
		$id = $data['Id'];
		if($id){
			$sql = "SELECT * FROM `".DB_NAME_HELP."`.`friendly_link` WHERE id = ".$id;
			$data = $this->db->get_row($sql, "ASSOC");
			return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>(array)$data);
		}
		$sql = "SELECT COUNT(*) FROM `".DB_NAME_HELP."`.`friendly_link` WHERE 1 ";
		$total = $this->db->get_var($sql);
		if($total > 0 ){
			$page_arr = $this->showpage($total);
			$sql = "SELECT * FROM `".DB_NAME_HELP."`.`friendly_link` WHERE 1 ORDER BY `order` ASC LIMIT ".$page_arr['limit'];
			$data = $this->db->get_results($sql, "ASSOC");
			return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>(array)$data,'Page'=>$page_arr['page']);
		}
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>array());
	}
	
	function save_link($data){
		if($data['Id']){
			return $this->update_link($data);
		}else{
			return $this->add_link($data);
		}
	}
	
	function del_link($data){
		$id = $data['Id'];
		if($id){
			$sql = "DELETE FROM `".DB_NAME_HELP."`.`friendly_link` WHERE `id`='".$id."'";
			$done = $this->db->query($sql);
			if($done)
				return array('Flag'=>100,'FlagString'=>'删除成功');
			else
				return array('Flag'=>102,'FlagString'=>'删除失败');
		}else{
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
	}
	
	function add_email($data){
		$config = config('email','advert');//指定发送邮件的配置

		$title = $data['Title'];
		$content = $data['Content'];
		
		if(!($_FILES['user_file']['type'] == 'text/plain' && $title && $content)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//处理上传用户列表的内容
		$uploaddir = __BASE__.'/themes/compile/';
		$uploadfile = $uploaddir.uniqid();
		
		if(!move_uploaded_file($_FILES['user_file']['tmp_name'], $uploadfile)){
			   return array('Flag'=>101,'FlagString'=>'上传文件失败');
		}
		
		$file_users = explode("\n", str_replace("\r\n", "\n", mb_convert_encoding(file_get_contents($uploadfile), 'UTF-8', 'UTF-8,GBK,GB2312,BIG5')));
		unlink($uploadfile);
		$users_list = array();
		$users = array();
		foreach($file_users as $one_user){
			if(!$one_user){
				continue;
			}
			$users_list[] = trim($one_user);
		}
		$users_list = array_unique($users_list);
		if(!$users_list){
			return array('Flag'=>102,'FlagString'=>'文件内容为空');
		}
		foreach($users_list as $key => $value){
			$users[$value] = sendMail($value,$title,$content,$config);
		}
		
		//数据存入
		$sql = "INSERT INTO `".DB_NAME_HELP."`.`help_email`(`id`,`title`,`send_time`,`send_num`) VALUES ( NULL,'".$title."','".time()."','".count($users)."');";
		$this->db->query($sql);
		$sql = "SELECT LAST_INSERT_ID()";
		$email_id = $this->db->get_var($sql);
		$sql = "INSERT INTO `g3_help`.`help_email_content`(`id`,`email_id`,`users`,`content`) VALUES ( NULL,'".$email_id."','".addslashes(json_encode($users))."','".$content."');";
		$this->db->query($sql);
		
		$failed = 0;
		foreach($users as $done){
			if(!$done)
				$failed++;
		}
		
		if($failed){
			return array('Flag'=>100,'FlagString'=>'添加并发送成功, 有'.$failed.'失败');
		}	
		return array('Flag'=>100,'FlagString'=>'添加并发送成功');
	}
	
	function email_list($start, $end){
		$sql = "SELECT COUNT(*) FROM `".DB_NAME_HELP."`.`help_email`";
		$total = $this->db->get_var($sql);
		
		$page_arr = $this->showpage($total);
		if($start != -1 && $end != -1){
			$end += 24*60*60-1;
			$sql = "SELECT * FROM `".DB_NAME_HELP."`.`help_email` WHERE send_time BETWEEN '".$start."' AND '".$end."' ORDER BY `send_time` DESC LIMIT ".$page_arr['limit'];
		}elseif($start != -1 && $end == -1){
			$sql = "SELECT * FROM `".DB_NAME_HELP."`.`help_email` WHERE send_time >= '".$start."' ORDER BY `send_time` DESC LIMIT ".$page_arr['limit'];
		}elseif($start == -1 && $end != -1){
			$end += 24*60*60-1;
			$sql = "SELECT * FROM `".DB_NAME_HELP."`.`help_email` WHERE send_time <= '".$end."' ORDER BY `send_time` DESC LIMIT ".$page_arr['limit'];
		}elseif($start == -1 && $end == -1){
			$sql = "SELECT * FROM `".DB_NAME_HELP."`.`help_email` ORDER BY `send_time` DESC LIMIT ".$page_arr['limit'];
		}		
		
		$data = (array)($this->db->get_results($sql, "ASSOC"));
		foreach($data as $key=>$value){
			$data[$key]['send_time'] = date("Y-m-d", $data[$key]['send_time']);
		}
		
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$data,'Page'=>$page_arr['page']);
	}
	
	function email_detail($id){
		$sql = "SELECT a.`title`, b.`content`, b.`users` FROM `".DB_NAME_HELP."`.`help_email` AS a, `".DB_NAME_HELP."`.`help_email_content` AS b WHERE a.`id` = b.`email_id` AND a.`id` = ".$id;
		$data = $this->db->get_row($sql, 'ASSOC');
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$data);
	}
	
	private function update_link($data){
		$id = $data['Id'];
		$site_name = $data['SiteName'];
		$url = $data['Url'];
		$order = $data['Order'];
		$img_cat_id = $data['ImgCatId'];
		$img_pic_id = $data['ImgPicId'];
		if($id && $site_name && $url && $order && $img_cat_id && $img_pic_id){
			//检测是否存在相同名字或url
			$sql = "SELECT COUNT(*) FROM `g3_help`.`friendly_link` WHERE (site_name = '".$site_name."' OR url = '".$url."') AND id != ".$id;
			$exist = $this->db->get_var($sql);
			if($exist)
				return array('Flag'=>102,'FlagString'=>'存在相同的网站名称或网址!');
			
			$sql = "UPDATE `".DB_NAME_HELP."`.`friendly_link` SET `site_name`='".$site_name."',
					`url`='".$url."',`order`='".$order."',`img_cat_id`='".$img_cat_id."',
					`img_pic_id`='".$img_pic_id."' WHERE `id`='".$id."'";
			$done = $this->db->query($sql);
			if($done)
				return array('Flag'=>100,'FlagString'=>'更新成功');
			else
				return array('Flag'=>102,'FlagString'=>'更新失败');
		}else{
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
	}
	
	private function add_link($data){
		$site_name = $data['SiteName'];
		$url = $data['Url'];
		$order = $data['Order'];
		$img_cat_id = $data['ImgCatId'];
		$img_pic_id = $data['ImgPicId'];
		if($site_name && $url && $order && $img_cat_id && $img_pic_id){
			//检测是否存在相同名字或url
			$sql = "SELECT COUNT(*) FROM `g3_help`.`friendly_link` WHERE site_name = '".$site_name."' OR url = '".$url."'";
			$exist = $this->db->get_var($sql);
			if($exist)
				return array('Flag'=>102,'FlagString'=>'存在相同的网站名称或网址!');
			
			$sql = "INSERT INTO `".DB_NAME_HELP."`.`friendly_link`(`id`,`site_name`,`url`,`order`,`img_cat_id`,`img_pic_id`)
				 VALUES ( NULL,'".$site_name."','".$url."','".$order."','".$img_cat_id."','".$img_pic_id."')";
			$done = $this->db->query($sql);
			if($done)
				return array('Flag'=>100,'FlagString'=>'添加成功');
			else
				return array('Flag'=>102,'FlagString'=>'添加失败');
		}else{
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
	}
	
	private function showPage($total, $perpage = 20) {
		if ($total > 0) {
			require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/library/page.class.php');
			$page = new extpage(array (
					'total' => $total,
					'perpage' => $perpage
			));
			$pageArr['page'] = $page->show();
			$pageArr['limit'] = $page->limit();
			unset ($page);
		}
		return $pageArr;
	}
}