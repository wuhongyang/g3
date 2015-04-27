<?php
include_once 'common.php';
include_once 'reg_info_config.php';

$module=empty($_GET['module'])?'base_info':$_GET['module'];
$action=empty($_GET['act'])?'base_info':$_GET['act'];
$group_id=intval($GroupData['groupid']);
if($group_id < 1){
	alertMsg('非法参数');
}

$user = checkLogin();

switch ($module) {
	case 'base_info':
		if(isset($_POST) && !empty($_POST)){
			$data = array(
				'Tag'=>'EditNick',
				'Nick'=>$_POST['nick'],
				'GroupId'=>$group_id,
				'Gender'=>$_POST['gender'],
				'Name'=>$_POST['name'],
				'Birthday'=>$_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'],
				'Province' => $_POST['province'],
				'City' => $_POST['city'],
				'Height' => $_POST['height'],
				'Qq' => $_POST['qq'],
				'Phone' => $_POST['phone'],
				'Introduction' => $_POST['introduction'],
				'Age' => date('Y') - intval($_POST['year'])
			);
			$rst = httpPOST(SSO_API_PATH,array('param'=>array('SessionKey'=>$_COOKIE['USER_LOGIN_TOKEN_'.$group_id]),'extparam'=>$data));
			// $rst['Flag'] = 100;
			//exit(json_encode($rst));
			if($rst['Flag'] == 100){
				header("Location:?module=face_guide");
			}else{
				alertMsg($rst['FlagString']);
			}
		}else{
			$year = date('Y');//当前年
			//省
			$p = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetAllProvince')));
			$p = (array)$p['Result'];
			unset($p[0]);
			foreach($p as $province){
				$provinces[$province['province_id']] = $province['province_name'];
			}
			$userInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$user['Uin'])));
			$userInfo = $userInfo['baseInfo'];
			if(!empty($userInfo['birthday'])){
				list($y,$m,$d) = explode('-', $userInfo['birthday']);
			}
			if($userInfo['height']<144 || $userInfo['height'] > 196){
				$userInfo['height'] = 160;
			}
			$template = 'base_info.html';
		}
		break;
	//高级资料
	case 'advanced':
		if(!isset($reg_info_config[$GroupData['Template']][$action])){
			alertMsg('非法参数');
		}
		if(isset($_POST) && !empty($_POST)){
			//判断必填项
			foreach($reg_info_config[$GroupData['Template']][$action] as $key=>$val){
				if ($val['is_required']){
					if($val['type']=='text'){
						if(!isset($_POST['advanced'][$key])||$_POST['advanced'][$key]===''){
							alertMsg($val['name'].'不能为空');
						}
					}
					elseif($val['type']=='select'||$val['type']=='radio'||$val['type']=='checkbox'){
						if(!isset($_POST['advanced'][$key])||$_POST['advanced'][$key]===''){
							alertMsg('请选择'.$val['name']);
						}
					}
				}
			}
			$advancedInfo=array('key'=>$action,'info'=>$_POST['advanced']);
			$rst = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'EditUserAdvanced','Uin'=>$user['Uin'],'GroupId'=>$group_id,'AdvancedInfo'=>$advancedInfo)));
			if($rst['Flag'] == 100){
				header("Location:".$_POST['next_url']);
			}
			else{
				alertMsg($rst['FlagString']);
			}
		}else{
			$userAdvancedInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserAdvanced','Uin'=>$user['Uin'],'GroupId'=>$group_id)));
			$userAdvancedInfo = $userAdvancedInfo['advanced']['info'][$action];
			$template = $action.'.html';
		}
		break;

	case 'skip':
		$step = intval($_GET['step']);
		if($step <= 1){
			header("Location:?module=face_guide");
		}elseif($step >= 2){
			header("Location:?module=finish");
		}
		break;

	case 'face_guide':
		//$group_id = intval($_GET['group_id']);
		$template = 'face_guide.html';
		break;

	case 'upload_face':
		//$group_id = intval($_GET['group_id']);
		$result = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'GetFile','where'=>array('uin'=>$user['Uin'].'big'))));
		$hasFace = ($result['Flag']==100);
		$template = 'upload_face.html';
		break;

	case 'finish':
		$param = array(
			'extparam' => array('Tag'=>'GetHotRooms','Groupid'=>$group_id),
			'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
		);
		$rooms = request($param);
		$rooms = (array)$rooms['HotRooms'];
		$rooms = array_slice($rooms, 0, 6);
		$template = 'finish.html';
		break;
	
	default:
		# code...
		break;
}

if($GroupData['Template']=='default'){
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','group'));
	include template('reg/'.$template,$tpl);
}
else{
	$advancedInfo=$reg_info_config[$GroupData['Template']][$action];
	unset($reg_info_config);
	$back_url=base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	$themes=$GroupData['Template'];
	$tmp_config=get_config('template','group_site');
	$tmp_config['template_dir'].=$themes.'/tpl/';
	$tmp_config['cache_dir'].=$themes.'/tpl/';
	$tpl=template::getInstance();
	$tpl->setOptions($tmp_config);
	include template('reg/'.$template,$tpl);
}