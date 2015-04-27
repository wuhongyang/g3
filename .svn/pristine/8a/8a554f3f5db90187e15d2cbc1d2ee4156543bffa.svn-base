<?php
class Statistics{

	public function __construct($data_group_id) {
		$data_group_id = intval($data_group_id);
		if($data_group_id > 0){
			$this->mongodb = domain::main()->GroupDBConn('mongo', $data_group_id);
		}else{
			$this->mongodb = $this->mongodb = db::connect(config('mongodb','ktv'),'mongo');
		}
		$this->platform_db = db::connect(config('database','default'));
	}

	public function __destruct() {
		unset ($this->platform_db);
		unset ($this->mongodb);
	}
	
	//分站在线人数汇总
	public function roomsUserTotal($region_id) {
		$query_condition = array();
		if($region_id > 0){
			$query_condition['region_id'] = (int)$region_id;
		}
		$table_name = DB_NAME_NEW_ROOMS.'.tbl_rooms_usertotal';
        $page_arr = $this->showPage($total,20);
        list($offset,$rows) = explode(',',$page_arr['limit']);
        $result = $this->mongodb->get_results(
            $table_name,
            $query_condition,
            array(
                'sort'=>array('createtime'=>-1),
                'limit'=>array('offset'=>$offset,'rows'=>$rows)
            )
        );
		foreach($result as $key=>$value){
            $sql = "SELECT name FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid={$value['region_id']}";
			$result[$key]['siteName'] = $this->platform_db->get_var($sql);
		}
        $page_arr = $this->showPage(count($result),20);
		return array('Flag'=>100,'FlagString'=>'总在线人数汇总','Result'=>$result,'Page'=>$page_arr['page']);
	}
	
	//分站在线人数明细
	public function roomsUserHistory($data){
		$query_condition = array();
		if($data['RegionId'] > 0){
			$query_condition['region_id'] = (int)$data['RegionId'];
		}
		if(!empty($data['s_time']) && !empty($data['e_time'])){
			$query_condition['createtime'] = array('$gte'=>intval(strtotime($data['s_time'])),'$lte'=>intval(strtotime($data['e_time'])));
		}
		$table_name = DB_NAME_NEW_ROOMS.'.tbl_rooms_usertotal_history';
        $page_arr = $this->showPage($total,20);
        list($offset,$rows) = explode(',',$page_arr['limit']);
        $result = $this->mongodb->get_results(
            $table_name,
            $query_condition,
            array(
                'sort'=>array('createtime'=>-1),
                'limit'=>array('offset'=>$offset,'rows'=>$rows)
            )
        );
		foreach($result as $key=>$value){
            $sql = "SELECT name FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid={$value['region_id']}";
			$result[$key]['siteName'] = $this->platform_db->get_var($sql);
		}
        $page_arr = $this->showPage(count($result),20);
		return array('Flag'=>100,'FlagString'=>'分站在线人数明细','Result'=>$result,'Page'=>$page_arr['page']);
	}
	
	//获取总在线人数汇总
	public function allUserTotal(){
		$table_name = DB_NAME_NEW_ROOMS.'.tbl_total_usertotal';
        $page_arr = $this->showPage($total,20);
        list($offset,$rows) = explode(',',$page_arr['limit']);
        $result = $this->mongodb->get_results(
            $table_name,
            array(),
            array(
                'sort'=>array('createtime'=>-1),
                'limit'=>array('offset'=>$offset,'rows'=>$rows)
            )
        );
        $page_arr = $this->showPage(count($result),20);
		return array('Flag'=>100,'FlagString'=>'总在线人数汇总','Result'=>$result,'Page'=>$page_arr['page']);
	}
	
	//获取总在线人数明细
	public function allUserHistory($data){
		$query_condition = array();
		if(!empty($data['s_time']) && !empty($data['e_time'])){
			$query_condition['createtime'] = array('$gte'=>intval(strtotime($data['s_time'])),'$lte'=>intval(strtotime($data['e_time'])));
		}
		$table_name = DB_NAME_NEW_ROOMS.'.tbl_total_userhistory';
        $page_arr = $this->showPage($total,20);
        list($offset,$rows) = explode(',',$page_arr['limit']);
        $result = $this->mongodb->get_results(
            $table_name,
            $query_condition,
            array(
                'sort'=>array('createtime'=>-1),
                'limit'=>array('offset'=>$offset,'rows'=>$rows)
            )
        );
        $page_arr = $this->showPage(count($result),20);
		return array('Flag'=>100,'FlagString'=>'总在线人数明细','Result'=>$result,'Page'=>$page_arr['page']);
	}
	
	//房间在线人数汇总
	public function roomsCountDay($data){
		$query_condition = array();
		$region_id = $data['region_id'];
		if($region_id > 0){
			$query_condition['region_id'] = (int)$region_id;
		}
		if($data['roomid'] > 0){
			$query_condition['roomid'] = (int)$data['roomid'];
		}
		if(!empty($data['s_time']) && !empty($data['e_time'])){
			$query_condition['createtime'] = array('$gte'=>intval(strtotime($data['s_time'])),'$lte'=>intval(strtotime($data['e_time'])));
		}
		require_once(dirname(__FILE__).'/room.class.php');
		$room = new Room();
		$table_name = DB_NAME_NEW_ROOMS.'.tbl_roomsuser_total';
        $page_arr = $this->showPage($total,20);
        list($offset,$rows) = explode(',',$page_arr['limit']);
        $result = $this->mongodb->get_results(
            $table_name,
            $query_condition,
            array(
                'sort'=>array('createtime'=>-1),
                'limit'=>array('offset'=>$offset,'rows'=>$rows)
            )
        );
		foreach($result as $key=>$value){
            $sql = "SELECT name FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid={$value['region_id']}";
			$result[$key]['siteName'] = $this->platform_db->get_var($sql);
			$roomInfo = $room->getRoomInfo($value['roomid']);
			if($roomInfo['Flag'] == 100){
				$result[$key]['name'] = $roomInfo['Info']['name'];
			}
		}
        $page_arr = $this->showPage(count($result),20);
		return array('Flag'=>100,'FlagString'=>'房间在线人数汇总','Result'=>$result,'Page'=>$page_arr['page']);
	}
	
	public function roomsUserInfo($data) {
		$query_condition = array();
		$region_id = $data['region_id'];
		if($region_id > 0){
			$query_condition['region_id'] = (int)$region_id;
		}
		if($data['roomid'] > 0){
			$query_condition['roomid'] = (int)$data['roomid'];
		}
		if(!empty($data['s_time']) && !empty($data['e_time'])){
			$query_condition['createtime'] = array('$gte'=>intval(strtotime($data['s_time'])),'$lte'=>intval(strtotime($data['e_time'])));
		}
		require_once(dirname(__FILE__).'/room.class.php');
		$room = new Room();
		$table_name = DB_NAME_NEW_ROOMS.'.tbl_roomsuser_history';
        $page_arr = $this->showPage($total,20);
        list($offset,$rows) = explode(',',$page_arr['limit']);
        $result = $this->mongodb->get_results(
            $table_name,
            $query_condition,
            array(
                'sort'=>array('createtime'=>-1),
                'limit'=>array('offset'=>$offset,'rows'=>$rows)
            )
        );
		foreach($result as $key=>$value){
            $sql = "SELECT name FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid={$value['region_id']}";
			$result[$key]['siteName'] = $this->platform_db->get_var($sql);
			$roomInfo = $room->getRoomInfo($value['roomid']);
			if($roomInfo['Flag'] == 100){
				$result[$key]['name'] = $roomInfo['Info']['name'];
			}
		}
        $page_arr = $this->showPage(count($result),20);
		return array('Flag'=>100,'FlagString'=>'房间在线人数明细','Result'=>$result,'Page'=>$page_arr['page']);
	}
	
	private function showPage($total, $perpage = 10) {
		require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/library/page.class.php');
        $page = new extpage(array (
            'total' => $total,
            'perpage' => $perpage
        ));
        $pageArr['page'] = $page->simple_page($total);
        $pageArr['limit'] = $page->simple_limit();
        unset ($page);
        return $pageArr;
	}
}
