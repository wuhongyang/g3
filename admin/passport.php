<?php
require_once '../library/global.fun.php';
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
$module = '';	//访问的模块
$module = $_GET['module'] ? $_GET['module'] : 'listPass';
$link_array = getLevelLink(10002,10004,10006,101);

//当前使用站
$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);

if(!$_GET['data_group_id']){
	$_GET['data_group_id'] = $__ADMIN_CURGROUP['groupid'];
}
switch($module) {
	case 'listPass'   :	//显示通行证
        $mypost = array();
		if( !empty($_GET) ) {
			$mypost = array_map("addslashes", array_map("htmlspecialchars", array_map("trim", $_GET)));
		}
		$param = array(
			'extparam' => array(
				'Tag' 		=> 'listPass' ,
				'mypost'	=> $mypost,
                'GroupId'   => $_GET['data_group_id']
			),
			'param'    => array(
				'BigCaseId'	=> 10002,
				'CaseId'    	=> 10004,
				'ParentId'  	=> 10006, 
				'ChildId'   	=> 101,
				'Desc'			=> '通行证列表'
			)
		);
		$result = request($param);
		$lists = $result['Data'];
		foreach($lists as $k=>$v){
			switch($v['platform']){
				case 1:
					$lists[$k]['platform_name'] = "邮箱";
					break;
				case 2:
					$lists[$k]['platform_name'] = "用户名";
					break;
				case 3:
					$lists[$k]['platform_name'] = "手机";
					break;
				case 4:
					$lists[$k]['platform_name'] = "qq";
					break;
				default:
					$lists[$k]['platform_name'] = "未知";
					break;
			}
			$lists[$k]['uptime_name'] = date("Y-m-d H:i:s", $v['uptime']);
			$lists[$k]['load_time_name'] = date("Y-m-d H:i:s", $v['load_time']);
			$lists[$k]['state_name'] = $v['state']==1?"正常":"冻结";
		}
		$page = $result['Page'];
		include template('passport/passport.html', $tpl);
		break;
	case 'state'   :		//修改账号状态
		$state = $_GET['val'];
		$id = $_GET['id'];
		$param = array(
				'extparam' => array(
						'Tag' => 'setPass',
						'mypost'	=> array(
								'id'    => $id
						),
                        'GroupId'=>$_GET['data_group_id']
				),
				'param'    => array(
						'BigCaseId'	=> 10002,
						'CaseId'    	=> 10004,
						'ParentId'  	=> 10006,
						'ChildId'   	=> 102,
						'Desc'			=> $state==1?'冻结通行证':'解冻通行证'
				)
		);
		$result = request($param);
		if( $result['Flag'] == '100' ) {
			ShowMsg($result['FlagString'], '?module=listPass&data_group_id='.$_GET['data_group_id']);
		} else {
			ShowMsg($result['FlagString'], '?module=listPass');
		}
		break;
	case 'reset'   :		//对账号密码进行重置
		if($_POST) {
			$pass = trim($pass);
			$repass = trim($repass);
			if( $_POST['pass'] == $_POST['repass'] ) {
				//2次密码一致判断
				$param = array(
						'extparam' => array(
								'Tag' => 'editPass',
								'mypost'	=> array(
										'uin'    => $_POST['uin'],
										'pass'	 => md5($_POST['pass']),
                                        'data_group_id' => $_GET['data_group_id']
								),
                                'GroupId'=>$_GET['data_group_id']
						),
						'param'    => array(
								'BigCaseId'	=> 10002,
								'CaseId'    	=> 10004,
								'ParentId'  	=> 10006,
								'ChildId'   	=> 103,
								'Uin' 	    	=> '',
								'SessionKey'	=> '',
								'ChannelId' 	=> '0',
								'TargetUin' 	=> '0',
								'Client'    	=> 'WEB ADMIN',
								'DoingWeight'   => '0',
								'MoneyWeight'	=> '0',
								'Desc'		=> '通行证密码重置'
						)
				);
				$result = request($param);
				if( $result['Flag'] == '100' ) {
					ShowMsg($result['FlagString'], '?module=listPass&data_group_id='.$_GET['data_group_id']);
				} else {
					ShowMsg($result['FlagString']);
				}
			} else {
				ShowMsg('2次密码不一致', -1);
			}
		}
		$id = $_GET['id'];
		$param = array(
			'extparam' => array(
				'Tag' => 'getOnePass',
				'mypost'	=> array(
							'id'    => "$id",
						),
                'GroupId'=>$_GET['data_group_id']
			),
			'param'    => array(
				'BigCaseId'	=> 10002,
				'CaseId'    	=> 10004,
				'ParentId'  	=> 10006, 
				'ChildId'   	=> 102,
				'Desc'			=> '通行证详情'
			)
		);
		$result = request($param);
		$one = $result['Data'];
		include template('passport/passportSetPw.html', $tpl);
		break;
	case 'passInfo':
		if($_POST){
			$mypost = $_POST;
			$mypost = array_map("addslashes", array_map("htmlspecialchars", array_map("trim", $mypost)));
			$param = array(
				'extparam'=>array('Tag'=>'SavePassInfo','Data'=>$mypost,'GroupId'=>$_GET['data_group_id']),
				'param'=>array('BigCaseId'=>10002,'CaseId'=>10004,'ParentId'=>10006,'ChildId'=>102)
			);
			$result = request($param);
			if($result['Flag'] == 100){
				ShowMsg($result['FlagString'], $link_array['101']['url']."&data_group_id=".$_GET['data_group_id']);
			}else{
				ShowMsg($result['FlagString'], -1);
			}
		}
		$param = array(
			'extparam'=>array('Tag'=>'getPassDetail','Id'=>intval($_GET['id']),'GroupId'=>$_GET['data_group_id']),
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10004,'ParentId'=>10006,'ChildId'=>101,'Desc'=>'通行证列表')
		);
		$result = request($param);
		$info 	= $result['Data'];
		$info['specialty']['specialty'] = (array)json_decode($info['specialty']['specialty'],true);
		$info['specialty']['experience'] = (array)json_decode($info['specialty']['experience'],true);
		$specialtys = array('1'=>'唱歌','2'=>'主持','3'=>'表演','4'=>'跳舞','5'=>'模仿秀','-1'=>$info['specialty']['other_specialty']);
		$experiences = array('1'=>'有网络视频主播相关经验','2'=>'有网络视频主持相关经验','3'=>'有线下相关工作经验','-1'=>$info['specialty']['other_experience']);
		$info['specialty']['imgs'] = (array)json_decode($info['specialty']['imgs'],true);
		
		//得到所有省份
		$provinces = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetAllProvince')));
		$provinces = (array)$provinces['Result'];
		unset($provinces[0]);
		include template('passport/info.html', $tpl);
		break;
    case 'adminPassEdit':
        if($_POST) {
			$pass = trim($pass);
			$repass = trim($repass);
			if( $_POST['pass'] != $_POST['repass'] ) {
                ShowMsg('2次密码不一致', -1);
			}
            $user = checkLogin();
            $result = httpPOST(SSO_API_PATH,
                array('extparam'=>
                        array(
                            'Tag'=>'ResetPassword',
                            'User'=>$user['Login'],
                            'OldPass'=>md5($_POST['old_pass']),
                            'Pass'=>md5($_POST['pass'])
                            ),
                      'param'=>
                        array(
                            'GroupId'=>10000
                            )
                     )
                );
            ShowMsg($result['FlagString'], 'passport.php?module=adminPassEdit');
		}

        include template('passport/adminPassEdit.html', $tpl);
        break;
}
