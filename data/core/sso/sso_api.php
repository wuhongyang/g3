<?php
require_once 'library/new_sso.class.php';
$json = $_POST['extparam'];
$param = $_POST['param'];

$data_group_id = $param['GroupId'];
$sso = new new_sso($data_group_id);

switch ($json['Tag']) {
	case 'GetLogin': //获取登录信息
		$token = $param['SessionKey'];
		$group_id = $param['GroupId'];
		$reset = $json['Reset'];
		echo json_encode($sso->getLogin($token,$group_id,$reset));
		break;
    case 'OpenidLogin': //登录验证
		echo json_encode($sso->openidLogin($json['Userinfo']));
		break;
	case 'QqBind':
		echo json_encode($sso->qqBind($json['Data']));
		break;
	case 'UserLogin': //登录验证
		$User		= $param['Uin'];
		$PassWord	= $param['SessionKey'];
        $groupid    = intval($param['GroupId']);
		$remember   = intval($json['Remember']);
        $isUin      = $json['IsUin'];
        $denyList   = $json['DenyList'];
		echo json_encode($sso->userLogin($User,$PassWord,$remember,$groupid,$isUin,$denyList));
		break;
	case 'GetUserInfo' : //获取绑定信息
		$Uid = $json['Uid'];
		echo json_encode($sso->getUserInfo($Uid));
		break;
	case 'GetUserBasicForUin' : //根据uin获得基本资料
		echo json_encode($sso->getUserBasicForUin($json['Uin']));
		break;
	case 'UinExist' : // 用户是否存在
		echo json_encode($sso->uinExist($json['Uin']));
		break;
	case 'SetStorage' : // 更新memcache
		$Userinfo = $json['Userinfo'];
		echo json_encode($sso->set_storage($Userinfo));
		break;
	case 'BindUser' : // 绑定帐号
		echo json_encode($sso->bindUser($json['User'],$json['Bind'],$json['Uid'],$json['Openid'],$json['Platform']));
		break;
	case 'ResetPassword' : // 重置密码
		$User = $json['User'];
		$OldPass = $json['OldPass'];
		$Pass = $json['Pass'];
        $IsUin = $json['IsUin'];
		echo json_encode($sso->resetPassword($User,$OldPass,$Pass,$IsUin));
		break;
	case 'EditPassword' : // 修改密码
		$Uid = $json['Uid'];
		$Pass = $json['Pass'];
		echo json_encode($sso->editPassword($Uid,$Pass));
		break;
	case 'RegPassport' : //注册通行证
		$User	= $json['User'];
		$Pass	= $json['Pass'];
		$Nick	= $json['Nick'];
		$gender = $json['Gender'];
		$platform	= $json['Platform'];
		$uid	= $json['Uid'];
        $age    = $json['Age'];
        $province = $json['Province'];
        $city = $json['City'];
        $area = $json['Area'];
		echo json_encode($sso->regPassport($User,$Pass,$platform,$age,$Nick,$gender,$uid,$province,$city,$area));
		break;
	case 'OpenidReg':
		/*$user	= $json['User'];
		$pass	= $json['Pass'];
		$platform	= $json['Platform'];
		$openid = $json['OpenId'];
		$picurl = $json['PicUrl']?$json['PicUrl']:'';*/
		echo json_encode($sso->openidReg($json['Data']));
		break;
	case 'RegisterUin' : // 注册用户ID(公司后台开设账号)
		$Uin		= $json['Uin'];
		$Nick		= $json['Nick'];
		$GroupId	= $json['GroupId']?$json['GroupId']:0;
		echo json_encode($sso->registerUin($Uin,$GroupId,$Nick,'123456'));
		break;
	case 'EditNick':
		echo json_encode($sso->editNick($param,$json));
		break;
	case 'GetLastUin' : // 获取最后的用户ID
		echo json_encode($sso->getLastuin());
		break;
	case 'UserLogOut' : // 用户退出
		echo json_encode($sso->userLogout());
		break;
	case 'CountUser': //获取用户数量
		echo json_encode($sso->countUser());
		break;
	case 'GetUser': //获取用户信息
		$user = $json['UserName'];
		$uid = $json['Uid'];
		$status = isset($json['Status'])? $json['Status'] : true;
		$group_id = isset($json['GroupId'])? $json['GroupId'] : '';
		echo json_encode($sso->getUser($user,$uid,$status,$group_id));
		break;
	case 'SendCode':
		echo json_encode($sso->sendCode($json['Phone'],$json['Module']));
		break;
	case 'BindPhone':
		echo json_encode($sso->bindPhone($json['Email'],$json['Phone'],$json['BindCode'],$json['Uid'],$json['Openid']));
		break;
	case 'PassPortModify':
		echo json_encode($sso->passPortModify($json['Uid'],$json['Fields']));
		break;
	case 'EditUserAdvanced' : //修改用户高级信息
		echo json_encode($sso->editUserAdvanced($json['Uin'],$json['GroupId'],$json['AdvancedInfo']));
		break;
	case 'GetUserAdvanced' : //查询用户高级信息
		echo json_encode($sso->getUserAdvanced($json['Uin'],$json['GroupId']));
		break;
	case 'GetInfoByIdcard':
		echo json_encode($sso->getInfoByIdcard($json['Idcard'],$json['IsNeedGroup']));
		break;
	case 'EditPassport':
		echo json_encode($sso->editPassport($json['Uin'],$json['Data']));
		break;
    case 'SearchUserByUin':
        echo json_encode($sso->searchUserByUin($param['TargetUin']));
        break;
    case 'SearchUserByInfo':
        echo json_encode($sso->searchUserByInfo($json['Email'], $json['Nick']));
        break;
    case 'HasPwdProtection':
        echo json_encode($sso->hasPwdProtection($param['Uin']));
        break;
    case 'AddPwdProtection':
        echo json_encode($sso->addPwdProtection($param['Uin'], $json['Question'], $json['Answer']));
        break;
    case 'EditPwdProtection':
        echo json_encode($sso->editPwdProtection($param['Uin'], $json['Question'], $json['Answer'], $json['OldAnswer']));
        break;
    case 'GetQuestion':
        echo json_encode($sso->getQuestion($param['Uin']));
        break;  
    case 'ResetPwdByQuestion':
        echo json_encode($sso->resetPwdByQuestion($param['Uin'], $json['Answer']));
        break; 
    case 'GetNickArray':
        echo json_encode($sso->getNickArray($json['Uins']));
        break;
	default:
		echo '{"Tag":"109","FlagString":"接口不存在"}';
		break;
}