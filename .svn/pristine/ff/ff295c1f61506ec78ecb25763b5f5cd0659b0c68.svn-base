<?php
class Interfaces{
	protected $db = null;

	public function __construct() {
		$this->db = db::connect(config('database','default'));
	}

	public function __destruct() {
		unset ($this->db);
	}
	
	private function getInterfaces($flag){
		$sql = 'SELECT `id`,`path`,`name` FROM '.DB_NAME_BEHAVIOR.'.interface WHERE `flag`="'.$flag.'"';
		return $this->db->get_results($sql,'ASSOC');
	}
	
	//得到业务接口
	public function getBusinessInterface(){
		$bi = $this->getInterfaces(1);
		return array('Flag'=>100,'FlagString'=>'业务接口','Result'=>$bi);
	}
	
	//读入接口
	public function getReadInterface(){
		$ri = $this->getInterfaces(1);
		return array('Flag'=>100,'FlagString'=>'读入接口','Result'=>$ri);
	}
	
	//写入接口
	public function getWriteInterface(){
		$wi = $this->getInterfaces(2);
		return array('Flag'=>100,'FlagString'=>'写入接口','Result'=>$wi);
	}
}
