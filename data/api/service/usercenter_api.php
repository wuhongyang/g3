<?php
require_once 'library/usercenter.class.php';

$json = $_POST['extparam'];
$param = $_POST['param'];

$user = new usercenter();

switch ($json['Tag']) {
	case 'GetLogin' : //查看用户是否登录
		echo json_encode($user->isLogin());
		break;
	case 'SaveBasic':	//保存基本资料
		echo json_encode($user->saveBasic( $param['Uin'],$json['mypost'] ));
		break;
	case 'ShowBasic':	//查看基本资料
		echo json_encode($user->showBasic( $json['Uin'] ));
		break;
	case 'SaveExtend':	//保存扩展资料
		echo json_encode($user->saveExtend( $json['mypost'] ));
		break;
	case 'ShowExtend':	//查看扩展信息
		echo json_encode($user->ShowExtend( $json['mypost'] ));
		break;
	case 'SaveHead':	//保存修改头像
		echo json_encode($user->saveHead( $json['mypost'] ));
		break;		
	case 'ShowHead':	//查看头像
		echo json_encode($user->showHead( $json['mypost'] ));
		break;
	case 'ShowVbList':	//V宝明细查询
		echo json_encode($user->showVbList( $json['mypost'] ));
		break;
	case 'ShowVdList':	//V豆明细查询
		echo json_encode($user->showVdList( $json['mypost'] ));
		break;
	case 'ShowInteres':	//查看爱好
		echo json_encode($user->showInteres( $json['mypost'] ));
		break;
	case 'AddInteres':	//添加爱好
		echo json_encode($user->addInteres( $json['mypost'] ));
		break;
	case 'SaveConnect' :	//修改个人联系方式
		echo json_encode($user->saveConnect( $json['mypost'] ));
		break;
	case 'UserLogin' :
		echo $user->userlogin($param,$json);
		break;
	case 'OpenidLogin':
		echo $user->openidLogin($json['Data']);
		break;
	case 'UserLogOut' :
		$array = array(
			'extparam' => array(
				'Tag'	=>  'UserLogOut',
			)
		);
		echo httpPOST(SSO_API_PATH, $array,false);
		break;
	case 'GetChannelTax':
		echo json_encode($user->getChannelTax($json['ChannelType'],$json['Uin']));
		break;
}


