<?php
class issue {

    private $db = null;
  
	public function __construct(){
        $this->db = db::connect(config('database','default'));
    }

    public function __destruct(){
        unset($this->db);
    }
	
    public function InitiateTypeList($no_page = false){
    	if($no_page){
    		$sql = "SELECT * FROM `".DB_NAME_ISSUE."`.`initiate_type_config` ORDER BY `order` ASC";
			$data = (array)($this->db->get_results($sql, "ASSOC"));
			return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$data);
    	}else{
    		$sql = "SELECT COUNT(*) FROM `".DB_NAME_ISSUE."`.`initiate_type_config`";
			$total = $this->db->get_var($sql);
			$page_arr = $this->showpage($total);
			$sql = "SELECT * FROM `".DB_NAME_ISSUE."`.`initiate_type_config` ORDER BY `order` ASC LIMIT ".$page_arr['limit'];
			$data = (array)($this->db->get_results($sql, "ASSOC"));
			return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$data,'Page'=>$page_arr['page']);
    	}
    }
    
	public function InitiateTypeInfo($id){
    	$sql = "SELECT * FROM `".DB_NAME_ISSUE."`.`initiate_type_config` WHERE `id`='".$id."'";
		$data = (array)($this->db->get_row($sql));
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$data);
    }
    
	public function InitiateTypeSave($arr){
		$name = addslashes(trim($arr['name']));
		$desc = addslashes(trim($arr['[desc']));
		$order = intval($arr['order']);
		$status = intval($arr['status']);
		if($arr['id']){
			$id	  = $arr['id'];
			if(!($id && $name)){
				return array('Flag'=>101,'FlagString'=>'参数错误');
			}
			$sql = "SELECT COUNT(*) FROM `".DB_NAME_ISSUE."`.`initiate_type_config` WHERE id = ".$id;
			$count = $this->db->get_var($sql);
			if($count != 1){
				return array('Flag'=>103,'FlagString'=>'无此内容');
			}
    		$sql = "SELECT COUNT(*) FROM `".DB_NAME_ISSUE."`.`initiate_type_config` WHERE `name` = '".$name."' AND `id`!='".$id."'";
			$exist = $this->db->get_var($sql);
			if($exist){
				return array('Flag'=>102,'FlagString'=>'已存在分类名称');
			}
			$sql = "UPDATE `".DB_NAME_ISSUE."`.`initiate_type_config` SET `name`='".$name."',`desc`='".$desc."',`order`='".$order."',`status`='".$status."' WHERE `id`='".$id."'";
			$done = $this->db->query($sql);
			if($done)
				return array('Flag'=>100,'FlagString'=>'更新成功');
			else
				return array('Flag'=>102,'FlagString'=>'更新失败');
		}else{
			if(!$name){
				return array('Flag'=>101,'FlagString'=>'不能为空');
			}
			$sql = "SELECT COUNT(*) FROM `".DB_NAME_ISSUE."`.`initiate_type_config` WHERE `name` = '".$name."'";
			$exist = $this->db->get_var($sql);
			if($exist){
				return array('Flag'=>102,'FlagString'=>'已存在分类名称');
			}
			$sql = "INSERT INTO `".DB_NAME_ISSUE."`.`initiate_type_config`(`id`,`name`,`desc`,`create_time`,`status`,`order`)
					 VALUES ( NULL,'".$name."','".$desc."','".time()."', '".$status."', '".$order."')";
			$done = $this->db->query($sql);
			if($done)
				return array('Flag'=>100,'FlagString'=>'添加成功');
			else
				return array('Flag'=>102,'FlagString'=>'添加失败');
			}
	}
	
    public function InitiateTypeDel($id){
    	if(!$id) return array('Flag'=>101,'FlagString'=>'参数错误');
    	$sql = "DELETE FROM `".DB_NAME_ISSUE."`.`initiate_type_config` WHERE `id`='".$id."'";
		$done = $this->db->query($sql);
		if($done)
			return array('Flag'=>100,'FlagString'=>'删除成功');
		else
			return array('Flag'=>102,'FlagString'=>'删除失败');
    }
    
	public function LevelList($search){
		$string = "";
		$no_page = $search['no_page']?true:false;
		unset($search['no_page']);
		foreach($search as $key => $val){
			if($val)
				$string .= " and `".$key."`='".$val."'";
		}
    	$sql = "SELECT COUNT(*) FROM `".DB_NAME_ISSUE."`.`level_config`";
		$total = $this->db->get_var($sql);
		if($no_page){
			$sql = "SELECT * FROM `".DB_NAME_ISSUE."`.`level_config` WHERE 1".$string." ORDER BY `order` ASC";
			$data = (array)($this->db->get_results($sql, "ASSOC"));
		}else{
			$sql = "SELECT COUNT(*) FROM `".DB_NAME_ISSUE."`.`level_config` WHERE 1".$string;
			$total = $this->db->get_var($sql);
			$page_arr = $this->showpage($total);
			$sql = "SELECT * FROM `".DB_NAME_ISSUE."`.`level_config` WHERE 1".$string." ORDER BY `order` ASC LIMIT ".$page_arr['limit'];
			$data = (array)($this->db->get_results($sql, "ASSOC"));
		}
		if($search['level'] = 2){
			foreach ($data as $key=>$val){
				$sql = "SELECT name FROM ".DB_NAME_ISSUE.".level_config WHERE p_id=".$val['p_id'];
				$data[$key]['one_name'] = $this->db->get_var($sql);
			}
		}
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$data,'Page'=>$page_arr['page']);
	}
    
	public function LevelInfo($id){
		$sql = "SELECT * FROM `".DB_NAME_ISSUE."`.`level_config` WHERE `id`='".$id."'";
		$data = (array)($this->db->get_row($sql));
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$data);
    }
    
	public function LevelSave($arr){
		$level = intval($arr['level']);
		$p_id = intval($arr['p_id']);
		$name = addslashes(trim($arr['name']));
		$desc = addslashes(trim($arr['desc']));
		$order = intval($arr['order']);
		$status = intval($arr['status']);
    	if($arr['id']){
			$id	  = $arr['id'];
			if(!($id && $name)){
				return array('Flag'=>101,'FlagString'=>'参数错误');
			}
			$sql = "SELECT COUNT(*) FROM `".DB_NAME_ISSUE."`.`level_config` WHERE id = ".$id;
			$count = $this->db->get_var($sql);
			if($count != 1){
				return array('Flag'=>103,'FlagString'=>'无此内容');
			}
    		$sql = "SELECT COUNT(*) FROM `".DB_NAME_ISSUE."`.`level_config` WHERE `name` = '".$name."' AND `id`!='".$id."'";
			$exist = $this->db->get_var($sql);
			if($exist){
				return array('Flag'=>102,'FlagString'=>'已存在分类名称');
			}
			$sql = "UPDATE `".DB_NAME_ISSUE."`.`level_config` SET `level`='".$level."',`p_id`='".$p_id."',`name`='".$name."',`desc`='".$desc."',`order`='".$order."',`status`='".$status."' WHERE `id`='".$id."'";
			$done = $this->db->query($sql);
			if($done)
				return array('Flag'=>100,'FlagString'=>'更新成功');
			else
				return array('Flag'=>102,'FlagString'=>'更新失败');
		}else{
			if(!$name){
				return array('Flag'=>101,'FlagString'=>'不能为空');
			}
			$sql = "SELECT COUNT(*) FROM `".DB_NAME_ISSUE."`.`level_config` WHERE `name` = '".$name."'";
			$exist = $this->db->get_var($sql);
			if($exist){
				return array('Flag'=>102,'FlagString'=>'已存在分类名称');
			}
			$sql = "INSERT INTO `".DB_NAME_ISSUE."`.`level_config`(`p_id`,`level`,`id`,`name`,`desc`,`create_time`,`status`,`order`)
					 VALUES ( '".$p_id."','".$level."', NULL,'".$name."','".$desc."','".time()."', '".$status."', '".$order."')";
			$done = $this->db->query($sql);
			if($done)
				return array('Flag'=>100,'FlagString'=>'添加成功');
			else
				return array('Flag'=>102,'FlagString'=>'添加失败');
		}
	}
	
	public function LevelDel($id){
    	$sql = "DELETE FROM `".DB_NAME_ISSUE."`.`level_config` WHERE `id`='".$id."' OR `p_id`='".$id."'";
		$done = $this->db->query($sql);
		if($done)
			return array('Flag'=>100,'FlagString'=>'删除成功');
		else
			return array('Flag'=>102,'FlagString'=>'删除失败');
    }
    
	public function Collection($search){
		$sql = "SELECT * FROM ".DB_NAME_ISSUE.".procedure_content WHERE 1";
		if($search['area_name']) $sql .= " AND area_name='".$search['area_name']."'";
		if($search['level_id']) $sql .= " AND level_id='".$search['level_id']."'";
		if($search['bg_date']) $sql .= " AND add_time>='".$search['bg_date']."'";
		if($search['ed_date']) $sql .= " AND add_time<='".$search['ed_date']."'";
   		$list = $this->db->get_results($sql);
   		foreach ($list as $key=>$val){
   			if($val['level_id']){
   				$sql = "SELECT name FROM ".DB_NAME_ISSUE.".level_config WHERE id=".$val['level_id'];
				$list[$key]['level_name'] = $this->db->get_var($sql);
   			}
   		}
		$temp = array();
		switch ($search['type']){
			case 'week':
				foreach($list as $val){
					if(!$val['level_id']) continue;
					$time_field = $this->get_time_by_week(date("Y", $val['add_time']), date("W", $val['add_time']));
					$week = date("Y", $val['add_time'])."年".date("W", $val['add_time'])."周(".$time_field.")";
					$temp[$week][$val['area_name']][$val['level_name']]  += 1;
				}
				$list = array();
				$level_arr = array();
				foreach ($temp as $week=>$val){
					foreach ($val as $area=>$val1){
						$arr = array();
						$arr['date'] = $week;
						$arr['area_name'] = $area;
						foreach($val1 as $level=>$val2){
							if(!in_array($level, $level_arr))$level_arr[] = $level;
							$arr[$level] = $val2;
						}
						$list[] = $arr;
					}
				}
				break;
			case 'month':
				foreach($list as $val){
					if(!$val['level_id']) continue;
					$month = date("Y-m", $val['add_time']);
					$temp[$month][$val['area_name']][$val['level_name']]  += 1;
				}
				$list = array();
				$level_arr = array();
				foreach ($temp as $month=>$val){
					foreach ($val as $area=>$val1){
						$arr = array();
						$arr['date'] = $month;
						$arr['area_name'] = $area;
						foreach($val1 as $level=>$val2){
							if(!in_array($level, $level_arr))$level_arr[] = $level;
							$arr[$level] = $val2;
						}
						$list[] = $arr;
					}
				}
				break;
			case 'year':
				foreach($list as $val){
					if(!$val['level_id']) continue;
					$year = date("Y", $val['add_time'])."年";
					$temp[$year][$val['area_name']][$val['level_name']]  += 1;
				}
				$list = array();
				$level_arr = array();
				foreach ($temp as $year=>$val){
					foreach ($val as $area=>$val1){
						$arr = array();
						$arr['date'] = $year;
						$arr['area_name'] = $area;
						foreach($val1 as $level=>$val2){
							if(!in_array($level, $level_arr))$level_arr[] = $level;
							$arr[$level] = $val2;
						}
						$list[] = $arr;
					}
				}
				break;
			case 'all':
				foreach($list as $val){
					if(!$val['level_id']) continue;
					$temp["截至目前"][$val['area_name']][$val['level_name']]  += 1;
				}
				$list = array();
				$level_arr = array();
				foreach ($temp as $year=>$val){
					foreach ($val as $area=>$val1){
						$arr = array();
						$arr['date'] = $year;
						$arr['area_name'] = $area;
						foreach($val1 as $level=>$val2){
							if(!in_array($level, $level_arr))$level_arr[] = $level;
							$arr[$level] = $val2;
						}
						$list[] = $arr;
					}
				}
				break;
			default:
				foreach($list as $val){
					if(!$val['level_id']) continue;
					$date = date("Y-m-d", $val['add_time']);
					$temp[$date][$val['area_name']][$val['level_name']]  += 1;
				}
				$list = array();
				$level_arr = array();
				foreach ($temp as $date=>$val){
					foreach ($val as $area=>$val1){
						$arr = array();
						$arr['date'] = $date;
						$arr['area_name'] = $area;
						foreach($val1 as $level=>$val2){
							if(!in_array($level, $level_arr))$level_arr[] = $level;
							$arr[$level] = $val2;
						}
						$list[] = $arr;
					}
				}
				break;
		}
		return array('Flag'=>100,'FlagString'=>'查询成功','level_arr'=>$level_arr, "list"=>$list);
    }
    
	public function IssueList($search){
		$str = "";
		if($search['status']) $str .= " and status in(".join(",", $search['status']).")";
		unset($search['status']);
		if($search['bg_date']) $str .= " and add_time>='".$search['bg_date']."'";
		unset($search['bg_date']);
		if($search['ed_date']) $str .= " and add_time<='".$search['ed_date']."'";
		unset($search['ed_date']);
		foreach($search as $key => $val){
			$str .= " and `".$key."`='".$val."'";
		}
    	$sql = "SELECT COUNT(*) FROM `".DB_NAME_ISSUE."`.`procedure_content`";
		$total = $this->db->get_var($sql);
		$page_arr = $this->showpage($total);
		$sql = "SELECT * FROM ".DB_NAME_ISSUE.".`procedure_content` WHERE 1 ".$str." ORDER BY `post_time` DESC LIMIT {$page_arr['limit']}";
		$data = (array)($this->db->get_results($sql, "ASSOC"));
		foreach ($data as $key=>$val){
			$sql = "SELECT name FROM ".DB_NAME_ISSUE.".initiate_type_config WHERE id=".$val['initiate_type_id'];
			$data[$key]['initiate_type_name'] = $this->db->get_var($sql);
			$data[$key]['title'] = $this->cut($val['title'], 20);
			$data[$key]['title1'] = $val['title'];
			$data[$key]['till_now'] = $this->format_time(time() - $val['add_time']);
			if($val['level_id']){
				$sql = "SELECT name FROM ".DB_NAME_ISSUE.".level_config WHERE id=".$val['level_id'];
				$data[$key]['level_name'] = $this->db->get_var($sql);
			}else{
				$data[$key]['level_name'] = '-';
			}
		}
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$data,'Page'=>$page_arr['page']);
    	
    }
    
    public function IssueAdd($data){
    	$area_id = addslashes(trim($data['area_id']));
    	$area_name = addslashes(trim($data['area_name']));
    	$response_user = addslashes(trim($data['response_user']));
    	$response_nick = addslashes(trim($data['response_nick']));
    	$response_email = addslashes(trim($data['response_email']));
    	$response_phone = addslashes(trim($data['response_phone']));
    	$response_time = strtotime(($data['response_time']));
    	$initiate_type_id = intval($data['initiate_type_id']);
    	$title = addslashes(trim($data['title']));
    	$desc = $this->translater2($data['desc']);
		$img = $data['img'];
		$img_ext = $data['img_ext'];
		$record = $data['record'];
		$status = 1;
    	
    	$sql = "INSERT INTO `".DB_NAME_ISSUE."`.`procedure_content`(`id`,`area_id`,`area_name`,`response_user`,`response_nick`,`response_email`,`response_phone`,`response_time`,`initiate_type_id`,`title`,`desc`,`add_time`,`post_time`,`record`,`status`,`img`,`img_ext`)
				VALUES ( NULL,'".$area_id."','".$area_name."','".$response_user."','".$response_nick."','".$response_email."','".$response_phone."','".$response_time."','".$initiate_type_id."','".$title."', '".$desc."', '".time()."', '".time()."', '".$record."', '1', '".$img."','".$img_ext."')";
    	$done = $this->db->query($sql);
		if($done)
			return array('Flag'=>100,'FlagString'=>'添加成功');
		else
			return array('Flag'=>102,'FlagString'=>'添加失败');
    }
    
	public function IssueEdit($data){
    	$id = $data['id'];
    	unset($data['id']);
    	if($id <0 ) return array('Flag'=>101,'FlagString'=>'参数错误');
		$string = "UPDATE ".DB_NAME_ISSUE.".procedure_content SET ";
		foreach($data as $key => $val){
			$string .= "`".$key."`='".$val."',";
		}
		$string = substr($string, 0, -1)." WHERE id=".$id;
		$done = $this->db->query($string);
		if($done)
			return array('Flag'=>100,'FlagString'=>'更新成功');
		else
			return array('Flag'=>102,'FlagString'=>'更新失败');
    }
    
	public function IssueInfo($id){
    	$sql = "SELECT * FROM `".DB_NAME_ISSUE."`.`procedure_content` WHERE `id`='".$id."'";
		$data = (array)($this->db->get_row($sql));
		$sql = "SELECT name FROM ".DB_NAME_ISSUE.".initiate_type_config WHERE id=".$data['initiate_type_id'];
		$data['initiate_type_name'] = $this->db->get_var($sql);
		$data['desc'] = $this->translater1($data['desc']);
		if($data['level_id']){
			$sql = "SELECT name FROM ".DB_NAME_ISSUE.".level_config WHERE id=".$data['level_id'];
			$data['level_name'] = $this->db->get_var($sql);
		}
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$data);
    }
	
    function translater2($value){
		$value = str_ireplace(array("\r\n"), array(""), $value);//去除kindeditor自动加的一个换行符
		$value = str_ireplace(array("<", ">", " ", "\"", "'", "\\", ":"), array("&lt;", "&gt;", "&nbsp;", "&quot;", "&singleQuto", "&backslash", "&colon;"), $value);
		return $value;
	}
	
	function translater1($value){
		$value = str_ireplace(array("&lt;", "&gt;", "&nbsp;", "&quot;", "&singleQuto", "&backslash", "&colon;"), array("<", ">", " ", "\"", "'", "\\", ":"),  $value);
		return $value;
	}
	
	/**
	 * 字符串截取
	 */
	function cut($Str, $Length) {//$Str为截取字符串，$Length为需要截取的长度
		global $s;
		$i = 0;
		$l = 0;
		$ll = strlen($Str);
		$s = $Str;
		$f = true;
		while ($i <= $ll) {
			if (ord($Str{$i}) < 0x80) {
				$l++; $i++;
			} else if (ord($Str{$i}) < 0xe0) {
				$l++; $i += 2;
			} else if (ord($Str{$i}) < 0xf0) {
				$l += 2; $i += 3;
			} else if (ord($Str{$i}) < 0xf8) {
				$l += 1; $i += 4;
			} else if (ord($Str{$i}) < 0xfc) {
				$l += 1; $i += 5;
			} else if (ord($Str{$i}) < 0xfe) {
				$l += 1; $i += 6;
			}
			if (($l >= $Length - 1) && $f) {
				$s = substr($Str, 0, $i);
	            $f = false;
			}
			if (($l > $Length) && ($i < $ll)) {
				$s = $s . '...'; break; //如果进行了截取，字符串末尾加省略符号“...”
			}
		}
		return $s;
	}
	
	function format_time($time){
		$day = floor($time/86400);
		$hour = floor(($time-$day*86400)/3600);
		$minute = floor(($time-$day*86400-$hour*3600)/60);
		$second = $time-$day*86400-$hour*3600-$minute*60;
		return $day."天".$hour."小时".$minute."分".$second."秒";
	}

	function get_after_level($p_id){
    	if(!$p_id) {
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_ISSUE.".level_config WHERE status=1 AND p_id=".$p_id;
		$list = $this->db->get_results($sql);
		$str = "";
		if($list){
			$str = "&nbsp;&nbsp;<select class='level_id'><option value=''>--请选择--</option>";
			foreach ($list as $val){
				$str .= "<option value='".$val['id']."'>".$val['name']."</option>";
			}
			$str .= "</select>";
		}
		return array('Flag'=>100,'FlagString'=>'获取成功', 'Data'=>$str);
    }
    
 	function get_before_level($id){
    	// 1.拼接id所在层的下一层 2.取出id所在层 3.拼接id所在层前的所有层级（递归）
		$str = array();
		$sql = "SELECT * FROM ".DB_NAME_ISSUE.".level_config WHERE id=".$id;
		$info = $this->db->get_row($sql);
		//1
		$str0 = "";
		$sql = "SELECT * FROM ".DB_NAME_ISSUE.".level_config WHERE status=1 AND p_id=".$id;
		$list = $this->db->get_results($sql);
		if($list){
			$str0 .= "<select class='level_id'><option value=''>--请选择--</option>";
			foreach ($list as $val){
				$str0 .= "<option value='".$val['id']."'>".$val['name']."</option>";
			}
			$str0 .= "</select>";
			$str[] = $str0;
		}
		//2
		$str0 = "";
		$sql = "SELECT * FROM ".DB_NAME_ISSUE.".level_config WHERE status=1 AND p_id=".$info['p_id'];
		$list = $this->db->get_results($sql);
		$str0 .= "<select class='level_id'><option value=''>--请选择--</option>";
		foreach ($list as $val){
			if($val['id'] == $info['id']) $str0 .= "<option value='".$val['id']."' selected>".$val['name']."</option>";
			else $str0 .= "<option value='".$val['id']."'>".$val['name']."</option>";
		}
		$str0 .= "</select>";
		$str[]  = $str0;
		
		//3
		for($i=$info['level']; $i--; $i>=1){
			$str0 = "";
			$list = $this->db->get_results("SELECT * FROM ".DB_NAME_ISSUE.".level_config WHERE status=1 AND level=".$i);
			if($list){
				$str0 .= "<select class='level_id'><option value=''>--请选择--</option>";
				foreach ($list as $val){
					if($val['id'] == $info['p_id']) {
						$str0 .= "<option value='".$val['id']."' selected>".$val['name']."</option>";
						//$info = $this->db->get_row("SELECT * FROM ".DB_NAME_ISSUE.".level_config WHERE id=".$val['id']);
					}
					else $str0 .= "<option value='".$val['id']."'>".$val['name']."</option>";
				}
				$str0 .= "</select>";
			}
			$str[] = $str0;
			
		}
		$str = array_reverse($str);
		$str1 = "<input type='hidden' name='level_id' value='".$id."'>";
		$str1 .= join("&nbsp;&nbsp;", $str);
		return $str1;
    }
    
	function get_time_by_week($year, $week){
		$first_w = date("w", strtotime($year."-01-01"));
		switch ($first_w){
			case 1:
				if($week == 1){
					$begin_day = $year."-01-01";
					$end_day = $year."-01-07";
				}else{
					$basic = ($week-2)*7;
					$begin_day = date("Y-m-d", strtotime("+".$basic." days", strtotime($year."-01-08")));
					$end_day = date("Y-m-d", strtotime("+6 days", strtotime($begin_day)));
				}
				break;
			case 2:
				if($week == 1){
					$begin_day = $year."-01-01";
					$end_day = $year."-01-06";
				}else{
					$basic = ($week-2)*7;
					$begin_day = date("Y-m-d", strtotime("+".$basic." days", strtotime($year."-01-07")));
					$end_day = date("Y-m-d", strtotime("+6 days", strtotime($begin_day)));
				}
				break;
			case 3:
				if($week == 1){
					$begin_day = $year."-01-01";
					$end_day = $year."-01-05";
				}else{
					$basic = ($week-2)*7;
					$begin_day = date("Y-m-d", strtotime("+".$basic." days", strtotime($year."-01-06")));
					$end_day = date("Y-m-d", strtotime("+6 days", strtotime($begin_day)));
				}
				break;
			case 4:
				if($week == 1){
					$begin_day = $year."-01-01";
					$end_day = $year."-01-04";
				}else{
					$basic = ($week-2)*7;
					$begin_day = date("Y-m-d", strtotime("+".$basic." days", strtotime($year."-01-05")));
					$end_day = date("Y-m-d", strtotime("+6 days", strtotime($begin_day)));
				}
				break;
			case 5:
				if($week == 1){
					$begin_day = $year."-01-01";
					$end_day = $year."-01-03";
				}else{
					$basic = ($week-2)*7;
					$begin_day = date("Y-m-d", strtotime("+".$basic." days", strtotime($year."-01-04")));
					$end_day = date("Y-m-d", strtotime("+6 days", strtotime($begin_day)));
				}
				break;
			case 6:
				if($week == 1){
					$begin_day = $year."-01-01";
					$end_day = $year."-01-02";
				}else{
					$basic = ($week-2)*7;
					$begin_day = date("Y-m-d", strtotime("+".$basic." days", strtotime($year."-01-03")));
					$end_day = date("Y-m-d", strtotime("+6 days", strtotime($begin_day)));
				}
				break;
			case 0:
				if($week == 1){
					$begin_day = $year."-01-02";
					$end_day = $year."-01-08";
				}else{
					$basic = ($week-2)*7;
					$begin_day = date("Y-m-d", strtotime("+".$basic." days", strtotime($year."-01-09")));
					$end_day = date("Y-m-d", strtotime("+6 days", strtotime($begin_day)));
				}
				break;
		}
		if(date("Y", strtotime($end_day)) > $year)
			$end_day = ($year+1)."-12-31";
		return date("m", strtotime($begin_day)).".".date("d", strtotime($begin_day))."-".date("m", strtotime($end_day)).".".date("d", strtotime($end_day));
	}
    
    private function showPage($total, $perpage = 15) {
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
?>