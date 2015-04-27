<?php
 
class Rank{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= domain::main()->GroupDBConn();
		$this->platform_db = db::connect(config('database','default'));
	}

	public function RankList($groupId){
		$groupId = intval($groupId);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT id,group_id,index_rank,rank FROM ".DB_NAME_GROUP.".`rank_config` WHERE `group_id`={$groupId}";
		$list = $this->db->get_row($sql,ASSOC);

		$allList = $this->allRankList();
		return array('Flag'=>100,'FlagString'=>'获取排行榜设置列表','List'=>$list,'All'=>$allList);
	}

	public function rankIndexSave($groupId,$rank){
		$groupId = intval($groupId);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		/*if(empty($rank)){
			$rankInfo = array();
		}else{
			$rank = array_unique($rank);
			foreach ($rank as $key => $val) {
				if($val > 0){
					$info = $this->rankInfo($val);
					if(!empty($info)){
						$rankInfo[] = $info;
					}
				}
			}
			$rankInfo = $rank;
		}*/
		$rankInfo = $rank?$rank:array();
		$rankInfo = serialize($rankInfo);
		
		$id = $this->groupExist($groupId);
		if($id > 0){
			$sql = "UPDATE ".DB_NAME_GROUP.".rank_config SET index_rank='{$rankInfo}' WHERE id={$id}";
		}else{
			$sql = "INSERT INTO ".DB_NAME_GROUP.".rank_config(`group_id`,`index_rank`) VALUES({$groupId},'{$rankInfo}')";
		}
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'保存主页排行榜设置失败');
		}
		return array('Flag'=>100,'FlagString'=>'保存主页排行榜设置成功');
	}

	public function rankSave($groupId,$rank){
		$groupId = intval($groupId);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		/*if(empty($rank)){
			$rankInfo = array();
		}else{
			$rank = array_unique($rank);
			foreach ($rank as $key => $val) {
				if($val > 0){
					$info = $this->rankInfo($val);
					if(!empty($info)){
						$rankInfo[] = $info;
					}
				}
			}
			$rankInfo = $rank;
		}*/
		$rankInfo = $rank?$rank:array();
		$rankInfo = serialize($rankInfo);
		
		$id = $this->groupExist($groupId);
		if($id > 0){
			$sql = "UPDATE ".DB_NAME_GROUP.".rank_config SET rank='{$rankInfo}' WHERE id={$id}";
		}else{
			$sql = "INSERT INTO ".DB_NAME_GROUP.".rank_config(`group_id`,`rank`) VALUES({$groupId},'{$rankInfo}')";
		}
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'保存排行榜设置失败');
		}
		return array('Flag'=>100,'FlagString'=>'保存排行榜设置成功');
	}

	private function groupExist($groupId){
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".rank_config WHERE group_id={$groupId}";
		$id = $this->db->get_var($sql);
		return $id;
	}

	private function rankInfo($id){
		$sql = "SELECT `id`,`name`,`rule` FROM ".DB_NAME_GROUP.".rank_setting WHERE id={$id} AND `status`=1";
		return $this->platform_db->get_row($sql,ASSOC);
	}

	private function allRankList(){
		$sql = "SELECT id,name,rule FROM ".DB_NAME_GROUP.".rank_setting WHERE `status`=1";
		return $this->platform_db->get_results($sql,ASSOC);
	}
}
