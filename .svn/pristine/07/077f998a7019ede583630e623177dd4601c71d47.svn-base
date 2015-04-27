<?php
include_once('library/common.php');

$module = $_GET['module']?$_GET['module']:"intendreg";

switch ($module) {
	case 'sendcode':
		if($_POST['nouser']){
			$param = array(
				'param' => array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10642,'ChildId'=>101),
				'extparam' => array('Tag'=>'SendCode4Reg', 'Phone'=>$_POST['nouser'])
			);
			$rst = request($param);
		}
		exit(json_encode($rst));
		break;
		
	case 'intendreg'://意向用户创建
		$param = array(
			'param' => array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10642,'ChildId'=>101),
			'extparam' => array('Tag'=>'IntendReg',	'Data'=>$_POST)
		);
		$rst = request($param);
		if($rst['Flag'] != 100){
			exit(json_encode($rst));
		}else{
			$param = array(
				'param' => array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10642,'ChildId'=>101),
				'extparam' => array('Tag'=>'GetPractice', 'GroupId'=>$groupId)
			);
			$rst = request($param);
			//先随机登录一个账号，返回创建成功，并且跳转
			$role_count = count($rst['Data']);
			$role_pos = 0;
			$account_count = count($rst['Data'][$role_pos]['account_details']);
			$account_pos = rand(0, $account_count-1);
			$data = $rst['Data'][$role_pos]['account_details'][$account_pos];
			$result = httpPOST(SSO_API_PATH,array('param'=>array('Uin'=>$data['login'],'SessionKey'=>md5(123456),'GroupId'=>$group_id),'extparam'=>array('Tag'=>'UserLogin',"Ip"=>get_ip())));
			//当前角色名
			$rst['role_name'] = urlencode($rst['Data'][$role_pos]['role_name']);
			$rst['role_pos'] = $role_pos;
			$rst['account_pos'] = $account_pos;
			exit(json_encode($rst));
		}
		break;
		
	case 'changeRole'://模板引导栏切换角色
		//将当前登录的角色id退出，登出新角色id对应的账号
		$param = array(
			'param' => array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10642,'ChildId'=>101),
			'extparam' => array('Tag'=>'GetPractice', 'GroupId'=>$groupId)
		);
		$rst = request($param);
		$role = $rst['Data'];
		$role_name = $_POST['role_name'];
		foreach ($role as $key=>$val){
			if($val['role_name'] == $role_name){
				$result = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'UserLogOut')));
				$role_pos = $key;
				$count = count($val['account_details']);
				$pos = rand(0, $count-1);
				$data = $val['account_details'][$pos];
				$result = httpPOST(SSO_API_PATH,array('param'=>array('Uin'=>$data['login'],'SessionKey'=>md5(123456),'GroupId'=>$group_id),'extparam'=>array('Tag'=>'UserLogin',"Ip"=>get_ip())));
				$rst = array("Flag"=>$result['Flag'], "FlagString"=>$result['FlagString'],
				             "role_pos"=>$role_pos, "account_pos"=>$pos, "role_name"=>$role_name);
				exit(json_encode($rst));
			}
		}
		break;
	default:
		# code...
		break;
}
