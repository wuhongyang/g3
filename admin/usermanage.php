<?php
require_once '../library/global.fun.php';
$module = $_GET['module'];
$link_array = getLevellink(10002, 10017, 10028, 101);

switch($module) {

	case 'usermanage':	//查找用户
		//当前使用站
        $__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
		$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);
        
		
        if(!$_GET['data_group_id']){
            $_GET['data_group_id'] = $__ADMIN_CURGROUP['groupid'];
        }
        $mypost = array();
		if(!empty($_GET)){
			unset($_GET['module']);
			if(!isset($_GET['vip'])) $_GET['vip'] = -1;
			$mypost = $_GET;
		}
		$param = array(
			'extparam' => array(
				'Tag'    => 'ShowUserMessage', 
				'mypost' => $mypost,
                'GroupId'=> $_GET['data_group_id'],
			),
			'param'    => array(
				'BigCaseId'		=> '10002',
				'CaseId'    	=> '10017',
				'ParentId'  	=> '10028', 
				'ChildId'   	=> '101',
				'Uin' 	    	=> '',
				'SessionKey'	=> '',
				'ChannelId' 	=> 0,
				'TargetUin' 	=> '',
				'Client'    	=> 'WEB ADMIN',
				'DoingWeight'   => 1,
				'MoneyWeight'	=> 1,
				'Desc'			=> '用户列表查看',
			)
		);
		$result = request($param);
		$lists = $result['lists'];
		$page = $result['page'];
		$tem = 'usermanage/usermanage.html';
		break;
	case 'detail':
		//获取相关参数
		$uin = intval($_GET['uin']);
		//$bid = intval($_GET['bid']);
		if(!$uin){
			alertMsg("非法获取uin", "usermanage.php?module=usermanage");
		}
		/*if(!$bid){
			alertMsg("非法获取bid", "usermanage.php?module=usermanage");
		}*/
		//发起请求
		$mypost = array('uin'=>$uin);
		$param = array(
				'extparam' => array(
						'Tag'    => 'GetUserDetail',
						'mypost' => $mypost,
                        'GroupId'=> $_GET['data_group_id'],
				),
				'param'    => array(
						'BigCaseId'		=> '10002',
						'CaseId'    	=> '10017',
						'ParentId'  	=> '10028',
						'ChildId'   	=> '102',
						'Uin' 	    	=> '',
						'SessionKey'	=> '',
						'ChannelId' 	=> 0,
						'TargetUin' 	=> '',
						'Client'    	=> 'WEB ADMIN',
						'DoingWeight'   => 1,
						'MoneyWeight'	=> 1,
						'Desc'			=> '查看详情'
				)
		);
		//获取uin对应的具体信息处理
		$result = request($param);
		if($result['Flag'] != 100){
			alertMsg($result['FlagString'], "usermanage.php?module=usermanage");
		}
		if( $result['info']['gender'] == '1')
			$result['info']['sex'] = '男';
		else if( $result['info']['gender'] == '2' )
			$result['info']['sex'] = '女';
		else
			$result['info']['sex'] = '未知';
		
		$info = $result['info'];
		$tem = 'usermanage/userdetail.html';
		break;
}
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template($tem, $tpl);
