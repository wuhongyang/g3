<?php

/**
 *   代理账户操作接口
 *   文件: proxy.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class proxy
{
	//数据库指针
	protected $db = null;
	private static $instance;
	
	//获取单例对象
    function instance(){
        if(!is_object(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	//构造函数
	public function __construct() {
        $this->db = domain::main()->GroupDBConn();
	}
	
	public function proxyAdd($info){
		if($info['Uin'] > 0 && $info['Uin'] == $info['ConfirmUin']){
			//$uininfo = $this->getChannelUser($info['Uin'],8);
			//if($uininfo['Flag'] !=100 || $uininfo['Data']['flag'] != 1) return array('Flag'=>102,'FlagString'=>'用户不存在或非启用室主');
			$uininfo = $this->getChannelUser($info['Uin'],16);
			if($uininfo['Flag'] ==100)return array('Flag'=>103,'FlagString'=>'用户已经是代理');
			$data = array('type'=>16,'uid'=>$info['Uin'],'name'=>'代理','descr'=>'代理','pact_id'=>'0','bank_id'=>'0','room_id'=>0,'status'=>1,'partner_id'=>100);
			return $this->channelAdd($data);
		}else{
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
	}
	
	private function getChannelUser($uin,$type){
		if($uin > 0){
			$sql = 'SELECT * FROM '.DB_NAME_PARTNER.'.channel_user WHERE uid = '.$uin.' AND `type` = '.$type;
			$row = $this->db->get_row($sql,'ASSOC');
			return empty($row) ? array('Flag'=>101,'FlagString'=>'不存在') : array('Flag'=>100,'FlagString'=>'存在','Data'=>$row);
		}
	}
	
	private function channelAdd($data){
		//检测参数
		if($data['type']==-1 || $data['uid']=='' || $data['name']=='' || $data['descr']=='' || $data['pact_id']=='' || $data['bank_id']=='')
			return array('Flag'=>101,'FlagString'=>'参数错误');
		//$sql = "SELECT s.uid,u.channel_id FROM ".DB_NAME_IM.".sso_user_relate s LEFT JOIN ".DB_NAME_IM.".username u USING(uid) WHERE s.uin={$data['uid']} AND s.is_use=1";
		//$row = $this->db->get_row($sql);
		//if($row['channel_id'] > 0 && $row['channel_id'] != $data['uid']){
		//	return array('Flag'=>103,'FlagString'=>'该通行证下已有其他用户ID绑定渠道');
		//}
		$this->db->start_transaction();
		$sql = 'INSERT INTO '.DB_NAME_PARTNER.'.channel_user(`partner_id`,`type`,`uid`,`up_uid`,`name`,`descr`,`pact_id`,`bank_name`,`bank_id`,`have_salary`,`salary`,`have_tax`,`tax`,`have_push_money`,`push_money`,`uptime`,`flag`,`room_id`) VALUES("'.$data['partner_id'].'","'.$data['type'].'","'.$data['uid'].'","'.$data['up_uid'].'","'.$data['name'].'","'.$data['descr'].'","'.$data['pact_id'].'","'.$data['bank_name'].'","'.$data['bank_id'].'","'.$data['have_salary'].'",0,"'.$data['have_tax'].'",0,"'.$data['have_push_money'].'",0,"'.time().'","'.$data['status'].'",'.$data['room_id'].')';
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>102,'FlagString'=>'添加失败');
		}

		//$rst = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'PassPortModify','Uid'=>$row['uid'],'Fields'=>array('channel_id'=>$data['uid']))));
		//if($rst['Flag'] != 100){
		//	$this->db->rollback();
		//	return array('Flag'=>102,'FlagString'=>'添加失败');
		//}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'添加成功');
	}
	
	private function checkUser($uin,$type){
		if($uin > 0){
			$sql = 'SELECT * FROM '.DB_NAME_PARTNER.'.channel_user WHERE uid = '.$uin.' AND `type` = '.$type.' AND flag = 1';
			$row = $this->db->get_row($sql,'ASSOC');
			return empty($row) ? array('Flag'=>101,'FlagString'=>'不存在') : array('Flag'=>100,'FlagString'=>'存在','Data'=>$row);
		}
	}
	
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
