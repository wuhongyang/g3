<?php
class domain{

    private static $storage = null;
    private static $instance = false;
    private static $key = null;
    private static $db = null;

    private $last_group = array();
	private static $last_group_id = null;

    private function __construct(){
    }

    private function __clone(){
    }

    public static function main(){
        if(!self::$instance instanceof self){
            self::$storage = cache::connect(config('cache','memcache'));
			self::$db  = db::connect(config('database','default'));
            self::$instance = new self();
        }
        return self :: $instance;
    }

	public function SetGroupId($param = ''){
		$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
        $__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);
		self::$last_group_id = $param['GroupId']>0 ? $param['GroupId'] : (isset($_GET['GroupId']) ? $_GET['GroupId'] : ($__ADMIN_CURGROUP['groupid']>0 ? $__ADMIN_CURGROUP['groupid'] : 0));
	}
	
	public function GroupData($param = ''){
		if(self::$last_group_id < 1){
			$this->SetGroupId($param);
		}
		self::$key = !empty($_COOKIE['GroupData']) ? $_COOKIE['GroupData'] : (self::$last_group_id > 0 ? strtoupper(md5(self::$last_group_id)) : '');
		$domain = $_GET['domain'];
		$host = $_SERVER['HTTP_HOST'];
		$host_array = explode(".",$host);
		$GroupData = $this->get_storage(self::$key);
		$group = json_decode($GroupData,true);
		$this->last_group = !empty(self::$key) ? $group : '';
		
		if($group['groupid'] == self::$last_group_id && self::$last_group_id >0){//5711238.test.vvku.com
			return $group;
		}
		if($host == $group['Domain'] && $domain !== 'default' && ($host_array[0] !== 'www' && $host_array[1] !== 'vvku')){//domain != www.vvku.com
			return $group;
		}
		
		if($host_array[0] >0 && $host_array[2] == 'vvku' && $host_array[0] == $group['groupid']){//5711238.test.vvku.com/a.php
			return $group;
		}

		if(($host_array[0] >0 && $host_array[2] == 'vvku') || ($host_array[0] == 'www' && $host_array[1] !== 'vvku' && self::$last_group_id <1) || ($host_array[1] !== 'vvku' && self::$last_group_id <1 && in_array($host_array[2],array('com','cn'))) ){
			return $this->foot_info($host_array);
		}
	}

	public function GroupDBConn($type = 'mysql',$group_id=0,$dbname='kexoo_im'){
		if(in_array($type,array('mysql','mongo'))){
			if(self::$last_group_id < 1 && empty(self::$key)){
				$this->SetGroupId();
			}
			$group_id = $group_id > 0 ? $group_id : self::$last_group_id;
			if($group_id > 0 || !empty(self::$key)){
				if(empty(self::$key)) self::$key = strtoupper(md5($group_id));
				$this->last_group = json_decode($this->get_storage(self::$key),true);
				if($this->last_group['groupid'] <= 0){
					$this->last_group = $this->foot_info(array($group_id),false);
				}
			}
			if($this->last_group['groupid']<= 0){
				return false;
			}
			$config = $this->group_dbinfo($this->last_group['EXT'],$type,$dbname);
			if(!empty($config)){
				return db::connect($config,$type);
			}else{
				return false;
			}
		}
		return false;
	}
	
	/*
		返回指定字段值
	*/
	public function GroupKeyVal($group_id,$get_key=''){
		if($group_id > 0 ){
			$k_key = strtoupper(md5($group_id));
			$this->last_group = json_decode($this->get_storage($k_key),true);
			return !empty($get_key) ? $this->last_group[$get_key] : $this->last_group;
		}
		return array('Flag'=>100,'FlagString'=>'参数错误');
	}
    
    public function GroupExtra($group_id){
        $sql = "SELECT ext2 FROM ".DB_NAME_GROUP.".`footer` WHERE group_id = ".$group_id;
        $ext = self::$db->get_var($sql);
        return array("Flag"=>100, "Data"=>json_decode($ext, true));
    }
	
	protected function group_dbinfo($group_ext,$type,$dbname){
		$group_ext = json_decode($group_ext,true);
		if($type=='mongo' && !empty($group_ext['mongoDB_HOST']['value']) && !empty($group_ext['mongoDB_NAME']['value']) && !empty($group_ext['mongoDB_PASS']['value']) && !empty($group_ext['mongoDB_PORT']['value'])){
			return array(
				'dbhost'	=> rawurldecode($group_ext['mongoDB_HOST']['value']),
				'dbuser'	=> rawurldecode($group_ext['mongoDB_NAME']['value']),
				'dbpw'		=> rawurldecode($group_ext['mongoDB_PASS']['value']),
				'dbport'	=> rawurldecode($group_ext['mongoDB_PORT']['value']),
				'dbname'	=> $dbname,
				'dbcharset' => 'utf8',
				'debug'		=> false
			);
		}elseif(!empty($group_ext['kkyooDB_HOST']['value']) && !empty($group_ext['kkyooDB_NAME']['value']) && !empty($group_ext['kkyooDB_PASS']['value']) && !empty($group_ext['kkyooDB_PORT']['value'])){
			return array(
				'dbhost'	=> rawurldecode($group_ext['kkyooDB_HOST']['value']),
				'dbuser'	=> rawurldecode($group_ext['kkyooDB_NAME']['value']),
				'dbpw'		=> rawurldecode($group_ext['kkyooDB_PASS']['value']),
				'dbport'	=> rawurldecode($group_ext['kkyooDB_PORT']['value']),
				'dbname'	=> $dbname,
				'dbcharset' => 'utf8',
				'debug'		=> false,
				'pconnect'	=> 0,
			);
		}else{
			return false;
		}
	}

	protected function group_info($group_id){
		/*
		$sql = "SELECT groupid,name,module_id,currency_unit,games,role_order_type,logo,notice,client_version,room_ui FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid={$group_id} AND is_use=1 LIMIT 1";
		$group_info = self::$db->get_row($sql,'ASSOC');
		*/
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupInfo','Id'=>$group_id),
			'param'=>array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101,'Desc'=>'获取站信息')
		);
		$group_info = httpPOST('api/rooms/rooms_api.php',$param);
		if($group_info['Flag'] != 100){
			header("Location:/404.html");
			exit;
		}
		return $group_info['Result'];
	}

	protected function foot_info($domain,$is_cookie=true){
		if($domain[0] > 0){
			$group_id = $domain[0];
		}else{
			$group_domain = $domain[0].'.'.$domain[1].'.'.$domain[2];
		}
		if($group_id>0){
            $where="group_id={$group_id}";
        }
        if(!empty($group_domain)){
            $where="domain='{$group_domain}'";
        }
        $sql="SELECT group_id,template,icp_info,domain,ktv_template,ext FROM ".DB_NAME_GROUP.".footer WHERE {$where}";
		$group = self::$db->get_row($sql,'ASSOC');
		if(empty($group)){
			header("Location:/404.html");
			exit;
		}
		/*
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupFoot'),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点首页底部信息')
		);
		$group =httpPOST('api/rooms/rooms_api.php',$param);
		*/
		if($group['group_id'] == $this->last_group['groupid']){
			return $this->last_group;
		}
		$group_info = $this->group_info($group['group_id']);
		$group_info['currency_unit'] = empty($group_info['currency_unit']) ? '金币' : $group_info['currency_unit'];
		
		$group_info['Template'] = $group['template'];
		$group_info['Icpinfo'] = $group['icp_info'];
		$group_info['Domain'] = $group['domain'];
		$group_info['KtvTemplate'] = $group['ktv_template'];
		$group_info['VoucherUnit'] = $group_info['currency_unit'] ;//货币单位
		$group_info['EXT'] = $group['ext'];
		if(!$this->set_storage(json_encode($group_info))){
			header("Location:/404.html");
			exit;
		}
		self::$key = strtoupper(md5($group['group_id']));
		self::$last_group_id  = $group['group_id'];
		if($is_cookie){
			@setcookie('GroupData',self::$key,time()+86400,'/',$_SERVER['HTTP_HOST']);
		}
		return $group_info;
	}

	//存储站信息
	protected function set_storage($info,$time=604800){
		$result = self::$storage->set(self::$key,$info,$time);
		if($result){
			return array('Flag'=>100,'FlagString'=>'ok');
		}else{
			return array('Flag'=>101,'FlagString'=>'fail');
		}
	}

	//获取站信息
	protected function get_storage($token=''){
		if(empty($token)){
			return false;
		}
		return self::$storage->get($token);
	}

	//删除站信息
	protected function del_storage($k_key){
		$result = self::$storage->delete(array($k_key));
		if($result){
			return array('Flag'=>100,'FlagString'=>'ok');
		}else{
			return array('Flag'=>101,'FlagString'=>'fail');
		}
	}
	
	public function set_group_info($domain,$clear=true){
		$k_key = strtoupper(md5($domain[0]));
		$this->del_storage($k_key);
		return $this->foot_info($domain,false);
	}
}