<?php
class interact_api
{

	public function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	public function getFrozenList($cmd,$start,$end){
		$dl = new dlhelper($this->db);
		$result = $dl->findAllPage(DB_NAME_FLASH_GAME.".{$cmd}_user_bank","uptime > {$start} AND uptime < {$end}",'uptime DESC');
		$page = $dl->getPage();
		return array('Flag'=>100,'Result'=>$result,'Page'=>$page);
	}
	
	public function freeFrozen($cmd,$start,$end){
		//得到游戏名称
		$info = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetInfoByCmd','Cmd'=>$cmd)));
		$info = $info['Info'];
		if(empty($info)) return array('Flag'=>101,'FlagString'=>'游戏不存在');
		$userlist = $this->getFrozenList($cmd,$start,$end);
		$error = array();
		foreach($userlist['Result'] AS $list){
			$param = array(
				'param'=>array('BigCaseId'=>$info['big_case_id'],'CaseId'=>$info['case_id'],'ParentId'=>$info['parent_id'],'ChildId'=>104,'ChannelId'=>$list['roomid'],'MoneyWeight'=>0,'Uin'=>$list['uin'],'TargetUin'=>$list['uin'],'Desc'=>'后台解冻','Client'=>'Web Admin'),
				'extparam'=>array("Tag"=>"BackMoney","Gameid"=>$list['gameid'])
			);
			//判断机器人
			if($list['uin']>=GUEST_UIN_START && $list['uin']<=GUEST_UIN_END){
				$param['param']['ChildId'] = 106;
			}
			$rst = request($param);
			if($rst['Flag'] != 100){
				$error[] = $list['uin'];
			}
		}
		return array('Flag'=>100,'FlagString'=>'操作完成','Error'=>$error);
	}
	
	//活动推荐房间配置
	public function saveActivityRooms($param){
		$gameid = $param['Gameid'];
		if($gameid <= 0) return array('Flag'=>101,'FlagString'=>'游戏ID错误');
		$rooms = json_encode((array)$param['Rooms']);
		$sql = "REPLACE INTO ".DB_NAME_REGION.".tbl_interact_rooms(gameid,rooms)VALUES({$gameid},'{$rooms}')";
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'保存成功！');
		}else{
			return array('Flag'=>100,'FlagString'=>'保存失败！');
		}
	}

	public function getActivityRooms($param){
		$gameid = $param['Gameid'];
		if($gameid <= 0) return array('Flag'=>101,'FlagString'=>'游戏ID错误');
		$sql = "SELECT rooms FROM ".DB_NAME_REGION.".tbl_interact_rooms WHERE gameid={$gameid}";
		$rooms = $this->db->get_row($sql,'ASSOC');
		$rooms = json_decode($rooms['rooms']);
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$rooms);
	}
}
