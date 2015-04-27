<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module = isset($_GET['module']) ? $_GET['module'] : 'index';

$login_user = checkLogin();
$login_user['Nick'] = stripcslashes($login_user['Nick']);

$uin = $login_user['Uin'];
$nick = $login_user['Nick'];

$group_id = intval($login_user['GroupId']);

$type = intval($_GET['type']);
if($type!=1 && $group_id < 1){
	alertMsg('无效参数');
}

/*
$isLogin = httpPOST(SSO_API_PATH,array('param'=>array('GroupId'=>$group_id),'extparam'=>array('Tag'=>'GetLogin')));
if($isLogin['Flag'] != '100'){
	showMsg('','/passport/?account&index&url='.urlencode($_SERVER['REQUEST_URI']));
}*/
$openInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$login_user['Uid'])));
$userinfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$uin)));
$userinfo = (array)$userinfo['baseInfo'];

$typename = array(1=>'站长',2=>'艺人',3=>'室主',4=>'代理');
$types = array(1=>8,2=>15,3=>9,4=>16);

//是否拥有该身份
if(getChannelType($uin,0,$types[$type]) > 0){
	alertMsg("您已经是{$typename[$type]}，不能重复申请", '/');
}

//站长
if($type == 1){
	$row = getChannelInfo($uin,0,9);
	if($row['room_id'] > 0){
		alertMsg('您的室主身份在签约状态，不能申请站长','/');
	}
	$row = getChannelInfo($uin,0,15);
	if($row['room_id'] > 0){
		alertMsg('您的艺人身份在签约状态，不能申请站长','/');
	}

	//是否已经提交申请
	$param = array(
		'extparam' => array('Tag'=>'CheckApply','Uin'=>$uin),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10035,'ParentId'=>10135,'ChildId'=>103)
	);
	$rst = request($param);
	if($rst['Flag'] != 100){
		alertMsg($rst['FlagString'],'/');
	}
}elseif($type == 3){//室主
	if(getChannelType($uin,0,8) > 0){
		alertMsg("您已经是站长，不能申请室主",'/');
	}
}


function isLegal(){
	header('Content-type:text/html; charset=utf-8');
	header("HTTP/1.1 404 Not Found");
	require(dirname(dirname(__FILE__)).'/404.html');
	die();
}

switch($module){
	case 'info':	
		if(isset($_POST) && !empty($_POST)){
			$param = array(
				'extparam' => array('Tag'=>'SaveOpenInfo','Info'=>$_POST),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10035,'ParentId'=>10135,'ChildId'=>101)
			);
			$rst = request($param);
			if($rst['Flag']==100){
				if($_POST['type'] == 4){
					header("Location: /rooms/agency.php?type={$_POST['type']}&cur_city_id={$_POST['cur_city_id']}&ip_city={$_POST['ip_city']}");
				}else{
					header("Location: ?module=confirm&type={$_POST['type']}&cur_city_id={$_POST['cur_city_id']}&ip_city={$_POST['ip_city']}"); 
				}
				//$module = $_POST['type']==4 ? 'agentNext' : 'confirm';
				//header("Location: ?module={$module}&type={$_POST['type']}&cur_city_id={$_POST['cur_city_id']}&ip_city={$_POST['ip_city']}"); 
			}else{
				$string = '';
				foreach($_POST as $k => $val){
					$string .= '&'.$k.'='.$val;
				}
				alertMsg($rst['FlagString'],"?module=info{$string}");
			}
		}else{
			if($type<1 || $type>4){
				isLegal($type);
			}
			
			$p = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetAllProvince')));
			$p = (array)$p['Result'];
			unset($p[0]);
			foreach($p as $province){
				$provinces[$province['province_id']] = $province['province_name'];
			}
			//得到IP
			$ip = get_ip();
			$ip = '125.122.139.124';
			//得到IP所在地
			$taobaoIpUrl = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
			
			$ipInfo = json_decode(socket_request($taobaoIpUrl),true);
			$ipInfo = $ipInfo['code'] == '0' ? $ipInfo['data'] : array();
			//$city = json_decode($openInfo['permanent_city'],true);
			$provinceId = (isset($userinfo['province'])&&$userinfo['province']>-1) ? intval($userinfo['province']) : intval($ipInfo['region_id']);
			$cityId = (isset($userinfo['city'])&&$userinfo['city']>-1) ? intval($userinfo['city']) : intval($ipInfo['city_id']);
			if(isset($_GET['province']) && $_GET['province']>0){
				$provinceId = intval($_GET['province']);
			}
			if(isset($_GET['city']) && $_GET['city']>0){
				$cityId = intval($_GET['city']);
			}


			if($ipInfo['region_id'] > 0){
				$c = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCities','ProvinceId'=>$ipInfo['region_id'])));
				$c = $c['Result'];
				foreach($c as $city){
					$cities[$city['city_id']] = $city['city_name'];
				}
			}else{
				$cities = array();
			}
			$curCity = !empty($cities[$ipInfo['city_id']]) ? $cities[$ipInfo['city_id']] : $c['0']['city_name'];
			$curCityId = !empty($cities[$ipInfo['city_id']]) ? $ipInfo['city_id'] : $c['0']['city_id'];
			$temp = 'join/info.html';
		}
		break;
	case 'confirm':
		//1到3之间为站长，艺人，室主
		if($type<1 || $type>3){
			isLegal($type);
		}

		$curCityId = (int)$_GET['cur_city_id'];
		$ipCity = $_GET['ip_city'];
		//$city = json_decode($openInfo['permanent_city'],true);
		$province = isset($userinfo['province']) ? intval($userinfo['province']) : -1;
		$city = isset($userinfo['city']) ? intval($userinfo['city']) : -1;
		
		$userInfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$uin)));
		$nick = !empty($userInfo['Nick']) ? $userInfo['Nick'] : $uin;
		$pName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetProvinceName','ProvinceId'=>$province)));
		$pName = $pName['provinceName'];
		$cName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCityName','CityId'=>$city)));
		$cName = $cName['cityName'];
		//手机归属地查询
		$api = 'http://life.tenpay.com/cgi-bin/mobile/MobileQueryAttribution.cgi?chgmobile='.$userinfo['phone'];
		$phoneInfo = socket_request($api);
		$xml = simplexml_load_string($phoneInfo);
		$location = trim($xml->city);

		$temp = 'join/confirm.html';
		
		break;
	case 'apply':
		$info = $_POST;
		$info['uin'] = $uin;
		$info['group_id'] = $group_id;

		$param = array(
			'extparam' => array('Tag'=>'ApplyArtistAndRoomer','Info'=>$info),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10035,'ParentId'=>10135,'ChildId'=>101)
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'getCities': 
		$province_id = intval($_GET['province_id']);
		$c = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCities','ProvinceId'=>$province_id)));
		$cities = json_encode((array)$c['Result']);
		exit($cities);
		break;
	case 'SendCode':
		$phone = $_GET['phone'];
		$rst = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'SendCode','Phone'=>$phone,'Module'=>'手机绑定')));
		if($rst['Flag'] == 100){
			$_SESSION['phone'] = $phone;
			$_SESSION['uniqueId'] = $rst['UniqueId'];
		}
		exit(json_encode($rst));
		break;
	case 'BindPhone':
		$phone = $_POST['phone'];
		$bindcode = $_POST['bindcode'];
		$uniqueId = !empty($_POST['uniqueid']) ? $_POST['uniqueid'] : $_SESSION['uniqueId'];
		unset($_SESSION['uniqueId']);
		unset($_SESSION['phone']);
		//手机验证更新下一步标识
		updateNext($uniqueId);
		
		$rst = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'BindPhone','Phone'=>$phone,'BindCode'=>$bindcode,'Email'=>$login_user['Email'],'Uid'=>$login_user['Uid'],'Openid'=>$login_user['Openid'])));
		exit(json_encode($rst));
		break;
	default:
		$temp = 'join/index.html';
		break;
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template($temp,$tpl);
