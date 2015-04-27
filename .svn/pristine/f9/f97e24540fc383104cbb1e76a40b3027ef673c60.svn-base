<?php

/**
 *   微博管理
 *   文件: vdmanage.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class weibo extends dlhelper
{
	protected $_open_ubb = true;
	
	//连接数据库
	public function __construct() {
		$this->db = domain::main()->GroupDBConn("mysql");
		parent::__construct($this->db);
	}

	//添加微博
	function addWeibo($tuid,$tuser,$tpost){
		if((empty($tpost['tbody']) && empty($tpost['source'])) || ( ! is_numeric($tuid))){
			return array('Flag'=>101,'FlagString'=>'没有填写任何内容！');
		}
		
		//上传图片
		if(file_exists($_FILES['pic']['tmp_name'])){
			$bytes = file_get_contents($_FILES['pic']['tmp_name']);
			$index = md5($bytes);
			$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
			$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
			$rst = array('Flag'=>$query['rst'],'File'=>$index);
			if($rst['Flag'] == 100){
				$tpost['pic'] = $rst['File'];
			}else{
				return array('Flag'=>101,'FlagString'=>'发布失败，图片上传错误！');
			}
		}
		
		$tbodylen = iconv_strlen($tpost['tbody'],'utf-8');
		if($tbodylen > 120){
			$tbodylen -= 120;
			return array('Flag'=>101,'FlagString'=>"发布失败，发布内容最多120字当前已超{$tbodylen}字！");
		}
		$addarr = array('tuid'=>$tuid,'tuser'=>$tuser,'tbody'=>'');
		foreach($tpost as $key=>$val){
			if(empty($val)){
				unset($tpost[$key]);
			}
		}

		//如果是转播的为转播记录+1
		if(isset($tpost['source']) && is_numeric($tpost['source'])){
			$addarr['source'] = intval($tpost['source']);
			$this->db->query("UPDATE ".DB_NAME_WEIBO.".`infocenter` SET `counts`=`counts`+1 WHERE `tid`={$addarr['source']}");
		}

		$addarr = array_merge($addarr,$tpost);
		$addarr['posttime'] = time();
		$addarr['tuser'] = addslashes(htmlspecialchars($addarr['tuser'],ENT_QUOTES));
		if($this->insert(DB_NAME_WEIBO.'.infocenter',$addarr)){
			return array('Flag'=>100,'FlagString'=>'发布成功');
		}else{
			return array('Flag'=>102,'FlagString'=>'发布失败');
		}
		
	}

	//验证是否是我发表的微博
	function isMyWeibo($uid,$tid){
		$uid = intval($uid);
		$tid = intval($tid);
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_WEIBO.".`infocenter` WHERE tid={$tid} AND tuid={$uid}";
		if(intval($this->db->get_var($sql)) > 0){
			return array('Flag'=>100,'FlagString'=>'ok');
		}else{
			return array('Flag'=>102,'FlagString'=>'fail');
		}
	}

	//删除一条微博
	function deleteWeibo($uid,$tid){
		$tid = intval($tid);
		$uid = intval($uid);
		$sql = "UPDATE ".DB_NAME_WEIBO.".`infocenter` SET `flag`=0 WHERE `tid`={$tid} AND `tuid`={$uid}";
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'删除成功');
		}else{
			return array('Flag'=>102,'FlagString'=>'删除失败');
		}
	}

	//获取用户的微博消息
	function getWeiboByUser($uin){
		$tlist = $this->findAllPage(DB_NAME_WEIBO.'.infocenter',"flag>0 AND tuid={$uin}",'`posttime` DESC');
		foreach($tlist as $key=>$val){
			$tlist[$key]['tbody'] = $this->ubb($tlist[$key]['tbody']);
			if(is_numeric($val['source'])){
				$sql = "SELECT * FROM ".DB_NAME_WEIBO.".`infocenter` WHERE `tid`={$val['source']} LIMIT 1";
				$source = $this->db->get_row($sql,'ASSOC');
				$source['tbody'] = $this->ubb($source['tbody']);
				$tlist[$key]['source'] = $source;
			}
		}
		$page = $this->getPage();
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$tlist,'Page'=>$page);
	}

	//获取最新发表的微博
	function getWeiboNews(){
		//$page = (intval($page) - 1) * $num;
		$tlist = $this->findAllPage(DB_NAME_WEIBO.'.infocenter',"flag>0",'`posttime` DESC');
		foreach($tlist as $key=>$val){
			$tlist[$key]['tbody'] = $this->ubb($tlist[$key]['tbody']);
			if(is_numeric($val['source'])){
				$sql = "SELECT * FROM ".DB_NAME_WEIBO.".`infocenter` WHERE `tid`={$val['source']} LIMIT 1"; //转播的
				$source = $this->db->get_row($sql,'ASSOC');
				$source['tbody'] = $this->ubb($source['tbody']);
				$tlist[$key]['source'] = $source;
			}
		}
		$page = $this->getPage();
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$tlist,'Page'=>$page);
	}

	//获取朋友发表的微博
	function getWeiboByFriends($uin){
		$dbname = DB_NAME_WEIBO;
		$sql="SELECT following FROM ".$dbname.".follow WHERE follower=$uin";
		$followList=$this->db->get_results($sql,'ASSOC');
		if(empty($followList)){
			$tlist = $this->findAllPage("$dbname.infocenter","tuid=$uin AND flag=1",'posttime DESC');
		}
		else{
			$followUins=array($uin);
			foreach($followList as $val){
				$followUins[]=$val['following'];
			}
			$followUins=implode(',',$followUins);
			$tlist = $this->findAllPage("$dbname.infocenter","tuid IN (".$followUins.") AND flag=1",'posttime DESC');
		}
		
		foreach($tlist as $key=>$val){
			$tlist[$key]['tbody'] = $this->ubb($tlist[$key]['tbody']);
			if(is_numeric($val['source'])){
				$sql = "SELECT * FROM ".DB_NAME_WEIBO.".`infocenter` WHERE `tid`={$val['source']} LIMIT 1";
				$source = $this->db->get_row($sql,'ASSOC');
				$source['tbody'] = $this->ubb($source['tbody']);
				$tlist[$key]['source'] = $source;
			}
		}
		$page = $this->getPage();
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$tlist,'Page'=>$page);
	}

	//转播我的微博
	function getRelayMyWeibo($uin){
		$uin = intval($uin);
		$tlist = $this->findAllPage(DB_NAME_WEIBO.'.`infocenter`',"source_uid={$uin} AND flag>0",'posttime DESC');
		foreach($tlist as $key=>$val){
			$tlist[$key]['tbody'] = $this->ubb($tlist[$key]['tbody']);
			if(is_numeric($val['source'])){
				$sql = "SELECT * FROM ".DB_NAME_WEIBO.".`infocenter` WHERE `tid`={$val['source']} LIMIT 1";
				$source = $this->db->get_row($sql,'ASSOC');
				$source['tbody'] = $this->ubb($source['tbody']);
				$tlist[$key]['source'] = $source;
			}
		}
		$page = $this->getPage();
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$tlist,'Page'=>$page);
	}
	
	//所有转播评论
	function getWeiboComments($tid){
		$tid = intval($tid);
		$lists =  $this->findAllPage(DB_NAME_WEIBO.'.`infocenter`', "`source`={$tid}",'posttime DESC');
		foreach($lists as $k=>$val){
			$lists[$k]['tbody'] = $this->ubb($val['tbody']);
		}
		$page = $this->getPage();
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$lists,'Page'=>$page);
	}
	
	function getWeibo($tid){
		$tid = intval($tid);
		$weibo = $this->db->get_row("SELECT * FROM ".DB_NAME_WEIBO.".`infocenter` WHERE tid={$tid} AND flag > 0 LIMIT 1");
		$weibo['tbody'] = $this->ubb($weibo['tbody']);
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$weibo);
	}

	//统计用户发表的信息数
	function countWeibo($uin){
		$sql = "SELECT count(*) FROM ".DB_NAME_WEIBO.".`infocenter` WHERE flag > 0 AND tuid={$uin}";
		return $num = intval($this->db->get_var($sql));
		//return array('Flag'=>100,'FlagString'=>'ok','Result'=>$num);
	}

	protected function ubb($content){
		$content = htmlspecialchars($content);
		if( ! $this->_open_ubb) return $content;
		$content = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_+.~#?&//=]+)', '<a href="\1" target="_blank">\1</a>', $content); 
		$em = '/themes/g3/service/images/smileys/';
		$content = preg_replace('|\(:(\d*)\)|','<img src="'.$em.'\1.gif" />',$content);
		return $content; 
	}
	
}