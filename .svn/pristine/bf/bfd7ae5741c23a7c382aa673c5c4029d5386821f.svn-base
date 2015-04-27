<?php
class UserCount{

	private $db = null;

	public function __construct() {
		$this->db = db::connect(config('database','default'));
		date_default_timezone_set('Asia/Shanghai');
	}
	
	public function getList($data){
		$start = $data['Start'];
		$end = $data['End'];
		$page = $data['Page']?$data['Page']:1;
		$pageSize = 20;
		$one_day = 24*60*60;
		$between = ($pageSize-1)*$one_day;
		$distance = $between!=0?2*$between:$one_day; 
		$min_date = $max_date = 0;
		
		//算出最小时间和最大时间
		$sql = "SELECT `date` FROM ".DB_NAME_FLASH_GAME.".`player_day` ORDER BY `date` ASC LIMIT 1";
		$min_date = $this->db->get_var($sql);
		
		$sql = "SELECT `date` FROM ".DB_NAME_FLASH_GAME.".`player_day` ORDER BY `date` DESC LIMIT 1";
		$max_date = $this->db->get_var($sql);
		
		//处理时间 算出总页数
		$time = strtotime(date("Ymd"));
		
		if($start && !$end){
			//获得相对早起始时间,算出总页数
			$start = $start>$min_date?$start:$min_date;
			$total_page = ceil(($max_date - $start + $one_day)/($pageSize*$one_day));
			
			$end = $time - ($page-1)*$distance;
			$start = $start > ($end - $between)?$start:($end - $between);
		}elseif(!$start && $end){
			//获得相对晚结束时间，算出总页数
			$end = $end<$max_date?$end:$max_date;
			$total_page = ceil(($end - $min_date + $one_day)/($pageSize*$one_day));
			
			$end -= ($page-1)*$distance;
			$start = $end - $between;
		}elseif(!$start && !$end){
			$end = $time - ($page-1)*$distance;
			$start = $end - $between;
			
			$total_page = ceil(($max_date - $min_date + $one_day)/($pageSize*$one_day));
		}elseif($start && $end){
			//获得相对早相对晚时间
			$start = $start>$min_date?$start:$min_date;
			$end = $end<$max_date?$end:$max_date;
			$start = $start > ($end - $between)?$start:($end - $between);
			
			$total_page = ceil(($end - $start + $one_day)/($pageSize*$one_day));
		}
		if($page > $total_page){
			return array('Flag'=>102,'FlagString'=>'页数超出范围'); 
		}
		$page_arr = $this->showpage($total_page);
		
		//查询
		$sql1 = "SELECT uin,`date`,counter FROM ".DB_NAME_FLASH_GAME.".`player_day` WHERE `date` BETWEEN ".$start." AND ".$end;
		$sql2 = "SELECT ip,`date`,counter FROM ".DB_NAME_FLASH_GAME.".`ip_day` WHERE `date` BETWEEN ".$start." AND ".$end;
		$data_uin = $this->db->get_results($sql1, "ASSOC");
		$data_ip = $this->db->get_results($sql2, "ASSOC");
		
		//要求局数
		$demand = 20;
		$search_date = array();
		
		//计算出每天大于20局的人数 和总人数
		$total_uin = array();
		$meet_uin = array();
		foreach($data_uin as $one){
			array_push($search_date, $one['date']);
			$total_uin[$one['date']]++;
			if($one['counter'] > $demand){
				$meet_uin[$one['date']]++;
			}
		}
		
		//计算出每天大于20局的ip数 和总ip数
		$total_ip = array();
		$meet_ip = array();
		foreach($data_ip as $one){
			array_push($search_date, $one['date']);
			$total_ip[$one['date']]++;
			if($one['counter'] > $demand){
				$meet_ip[$one['date']]++;
			}
		}
		
		//每天的人数和ip数集合
		$data = array();
		$search_date = array_unique($search_date);
		ksort($search_date, SORT_NUMERIC);
		$count = count($search_date);
		$i=0;
		foreach($search_date as $date){
			$data[$i]['TotalUin'] = $total_uin[$date]?$total_uin[$date]:0;
			$data[$i]['MeetUin'] = $meet_uin[$date]?$meet_uin[$date]:0;
			$data[$i]['TotalIp'] = $total_ip[$date]?$total_ip[$date]:0;
			$data[$i]['MeetIp'] = $meet_ip[$date]?$meet_ip[$date]:0;
			$data[$i]['Date'] = date("Y-m-d", $date);
			$i++;
		}
		
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>$data, 'Page'=>$page_arr['page']);
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