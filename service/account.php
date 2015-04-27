<?php
require_once 'common.php';

$module = isset($_GET['module']) ? $_GET['module'] : 'basic';

$uin = $user['Uin'];

//获取全站等级
$param = array(
		'extparam' => array('Tag'=>'GetLevel','Data'=>array('UinId'=>$uin)),
		'param'    => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10312,'ChildId'=>101)
);
//$result = request($param);
//$levelinfo = $result['Data'];

$param = array(
	'extparam' => array('Tag'=>"ShowBasic",'Uin'=>$uin),
	'param' => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10024,'ChildId'=>102,'Client'=>'WEB ADMIN','Desc'=>'查看基本资料')
);
$result = request($param);

$level = (array)$result['Level_array'];//等级
unset($result);

//我的商品分类
//$categories = (array)$GroupData['scheme_info'];
$param = array(
	'extparam'=>array('Tag'=>'Categories','GroupId'=>$user['GroupId'],'State'=>0),
	'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'分类')
);
$categories = request($param);
$categories = (array)$categories['Category'];
$cates = array();
foreach ($categories as $k => $v) {
	if($v['cate_id'] == -1){
		unset($categories[$k]);
	}
	$cates[$v['cate_id']] = $v['cate_name'];
}

switch($module){
	case 'basic':				
		//角色图标
		$rolesIcon = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetRolesIcon','Uin'=>$uin)));
		$rolesIcon = $rolesIcon['RolesIcon'];
		if(empty($rolesIcon)){
			$vipName = '普通用户';
		}
		//V点
		$kmoney = get_money($user['Uin'],$user['GroupId']);

		$temp = 'basic';
		break;
	case 'defaultProps':
		$parent_id = $_POST['parent_id'];
		$result = httpPOST(GROUP_SHOP_API_PATH,array('extparam'=>array('Tag'=>'SetDefault','Uin'=>$uin,'ParentId'=>$parent_id)));
		exit(json_encode($result));
		break;
	case 'modifyInfo':
		if(isset($_POST) && !empty($_POST)){
			if(mb_strlen($_POST['nick']) > 24){
				alertMsg('昵称长度不能大于8个字');
			}
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
			$rst = httpPOST(SSO_API_PATH,array('extparam'=>$data));
			$rst['Flag'] = 100;
			if($rst['Flag'] == 100){
				alertMsg($rst['FlagString'],'?module=modifyInfo');
			}else{
				alertMsg($rst['FlagString']);
			}
		}else{
			$userInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$user['Uin'])));
			$userInfo = $userInfo['baseInfo'];
			$year = date('Y');//当前年
			//省
			$p = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetAllProvince')));
			$p = (array)$p['Result'];
			unset($p[0]);
			foreach($p as $province){
				$provinces[$province['province_id']] = $province['province_name'];
			}
			if(!empty($userInfo['birthday'])){
				list($y,$m,$d) = explode('-', $userInfo['birthday']);
			}
			if($userInfo['height']<144 || $userInfo['height'] > 196){
				$userInfo['height'] = 160;
			}
			$nick = $userInfo['nick'];
			$temp = 'modify_info';
		}
		break;
	case 'changeFace':
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if( !empty( $_POST['stshead'] ) ){
				$bytes = file_get_contents($_POST['stshead']);
			}elseif(!empty($_FILES['upfile']['tmp_name'])){
				if(strpos($_FILES['upfile']['type'], 'image') === false){
					alertMsg('上传图片格式必须为jpg，png，gif格式');
				}
				$size = $_FILES['upfile']['size']/(pow(1024, 2));
				if($size > 2){
					alertMsg('上传头像不能大于2M，请重新上传');
				}
				$bytes = file_get_contents($_FILES['upfile']['tmp_name']);
			}else{
				alertMsg('上传头像不能大于2M，请重新上传');
			}
			$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'uin','Index'=>$_POST['uin']);
			$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
			if($query['rst'] == 100){
				$urls = array(
					PIC_API_PATH."/uin/{$uin}/100/100.jpg",
					PIC_API_PATH."/uin/{$uin}/50/50.jpg",
					PIC_API_PATH."/uin/{$uin}/40/40.jpg",
					PIC_API_PATH."/uin/{$uin}/160/160.jpg",
					PIC_API_PATH."/uin/{$uin}/70/70.jpg"
				);
				cdn_url($urls, 'CDN_UPLOAD_CLEAR');
			}else{
				alertMsg('头像更改失败','?module=changeFace');
			}
		}else{
			$temp = 'change_face';
		}
		break;
	case 'vdouRecord':
		$kmoney = get_money($uin);
		$_GET['startDate'] = $_GET['startDate'] ? $_GET['startDate'] : date('Y-m-01');
		$_GET['endDate'] = $_GET['endDate'] ? $_GET['endDate'] : date('Y-m-d');
		//条件搜索
		if( !empty($_GET) )
			$mypost = $_GET;
		$param = array(
			'extparam' => array('Tag'=>'ShowVdList','Uin'=>$uin,'Case'=>$_GET['case'],'startDate'=>$_GET['startDate'],'endDate'=>$_GET['endDate']),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10032,'ChildId'=>101)
		);
		$vd_info = request($param);
		$page = $vd_info['page'];
		$temp = 'vdou_record';
		break;
	case 'vdianRecord'://v点管理
		$param = array(
			'extparam' => array('Tag'=>'ShowVDiList','Uin'=>$uin),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10042,'ParentId'=>10317,'ChildId'=>101)
		);
		$result = request($param);
		$lists = $result['List'];
		$page = $result['Page'];
		unset($result['Page']);
		$temp = 'vdian_record';
		break;
	case 'vdianlist'://v点明细列表
		// $group_id = trim($_GET['group_id']);
		$kmoney = get_money($uin,$group_id);
		$_GET['startDate'] = $_GET['startDate'] ? $_GET['startDate'] : date('Y-m-01');
		$_GET['endDate'] = $_GET['endDate'] ? $_GET['endDate'] : date('Y-m-d');
		$param = array(
			'extparam' => array('Tag'=>'VdianList','Uin'=>$uin,'Group_id'=>$group_id,'Case'=>$_GET['case'],'startDate'=>$_GET['startDate'],'endDate'=>$_GET['endDate']),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10042,'ParentId'=>10317,'ChildId'=>102)
		);
		$result = request($param);
		$nick = $user['Nick'];
		$lists = $result['List'];
		$page = $result['Page'];
		$group_name = $result['group_name'];
		unset($result['Page']);
		unset($result['group_name']);
		$temp = 'vdianlist';
		break;
	case 'vchange'://v 点兑换
		// $group_id = trim($_GET['group_id']);
		header("Location: /shop/index.php?type=vdouexchange&&groupId={$group_id}");
		break;
	case 'commodity':
		//$category_id = intval($_GET['category_id']);
		$category = intval($_GET['category']);
		//$commodity_id = intval($GroupData['scheme_info'][$category_id]['list'][0]);

		$param = array(
			'extparam'=>array('Tag'=>'GetUserStocksOnCategory','Category'=>$category,'GroupId'=>$user['GroupId']),
			'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'分类','Uin'=>$uin)
		);

		$commodities = request($param);
		$commodities = $commodities['StockInfo'];
		$temp = 'commodity';
		break;
	case 'allmedal':
		$param = array(
			'extparam' => array('Tag'=>'GetAllMedalType','Id'=>0),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10277,'ChildId'=>101)
		);
		$result = request($param);
		$temp = 'allmedal';
		break;
	case 'mymedal':
		$param = array(
			'extparam' => array('Tag'=>'GetAllMedalType','Id'=>0),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10277,'ChildId'=>101)
		);
		$medaltypes = request($param);
		if(empty($_GET['tid'])){
			$_GET['tid'] = $medaltypes['Result'][0]['id'];
		}
		foreach($medaltypes['Result'] as $key=>$val){
			if($_GET['tid'] == $val['id']){
				$param = array(
					'extparam' => array('Tag'=>'GetLevelRate','BusinessId'=>$val['business_id'],'Uin'=>array($uin)),
					'param'    => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10277,'ChildId'=>101)
				);
				$userLevel = request($param);
				break;
			}
		}
		$param = array(
			'extparam' => array('Tag'=>'GetMedalList','Id'=>(int)$_GET['tid']),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10277,'ChildId'=>101)
		);
		$mymedals = request($param);
		$temp = 'mymedal';
		break;
	case 'vipList':
		//获取全站等级
		$param = array(
				'extparam' => array('Tag'=>'GetVipLevel','Data'=>array('UinId'=>$uin)),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10312,'ChildId'=>101)
		);
		$result = request($param);
		$levelinfo = $result['Data'];
		$temp = 'vipList';
		break;
	case 'special_num'://靓号
		$data = array("uin" => $user['Uin']);
		//if(intval($_GET['special_num']) > 0) $data['special_num'] = intval($_GET['special_num']);
		//获取靓号
		$param = array(
			'extparam' => array('Tag'=>'NumRecord', 'GroupId'=>$user['GroupId'], 'Data'=>$data),
			'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>108,'Desc'=>'靓号库存记录')
		);
		$result = request($param);
		$special_nums = $result['List'];
		$page = $result['Page'];
		$temp = 'special_num';
		break;
	case 'special_num_use'://启用靓号
		$liang_id = intval($_POST['liang_id']);
		//启用靓号
		$param = array(
			'extparam' => array('Tag'=>'UseNum', 'GroupId'=>$user['GroupId'], 'Uin'=>$user['Uin'], 'Num'=>$liang_id),
			'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>108,'Desc'=>'启用靓号')
		);
		$result = request($param);
		echo json_encode($result);exit;
		break;
	case 'gift_num'://赠送靓号
		$data['group_id'] = $user['GroupId'];
		$data['pay_uin'] = $user['Uin'];
		$data['uin'] = intval($_POST['uin']);
		$data['special_num'] = intval($_POST['special_num']);
		$data['service_gift'] = 1;
		$param = array(
			'extparam' => array('Tag'=>'GiftNum', 'Data'=>$data),
			'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>109,'Desc'=>$user['Uin'].'赠送'.$_POST['uin'].'靓号'.$_POST['special_num'])
		);
		$result = request($param);
		echo json_encode($result);exit;
		break;
	case 'videoList'://视频列表
		$param = array(
				'extparam' => array('Tag'=>'VideoList','GroupId'=>$group_id,'Uin'=>$uin),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10644,'ChildId'=>101)
		);
		$videoList = request($param);
		$videoList = $videoList['Data'];
		$temp = 'video_list';
		break;
	case 'videoSave'://上传视频
		if($_POST['name']===''){
			$result=array('Flag'=>101, 'FlagString'=>'视频标题不能为空');
		}
		if(mb_strlen($_POST['name'],'UTF-8')>10){
			$result=array('Flag'=>101, 'FlagString'=>'视频标题不能超过10个字');
		}
		if($_POST['link']===''){
			$result=array('Flag'=>101, 'FlagString'=>'视频链接不能为空');
		}
		$mode='/^http[s]?:\/\/.*\.swf.*/i';
		if(!preg_match($mode,$_POST['link'])){
			$result=array('Flag'=>101, 'FlagString'=>'视频链接格式错误');
		}
		if(empty($_POST['pic'])){
			$result=array('Flag'=>101, 'FlagString'=>'请先上传视频封面');
		}
		if(intval($_POST['id'])<=0){
			$param = array(
					'extparam' => array('Tag'=>'VideoList','GroupId'=>$group_id,'Uin'=>$uin),
					'param'    => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10644,'ChildId'=>101)
			);
			$videoList = request($param);
			if(count($videoList['Data'])>=5){
				$result=array('Flag'=>101, 'FlagString'=>'最多只能添加5个视频');
			}
		}
		if(!isset($result)){
			$param = array(
					'extparam' => array('Tag'=>'VideoSave','Id'=>intval($_POST['id']),'Name'=>$_POST['name'],'Link'=>$_POST['link'],'PicMD5'=>$_POST['pic'],'GroupId'=>$group_id,'Uin'=>$uin),
					'param'    => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10644,'ChildId'=>102)
			);
			$result = request($param);
		}
		echo json_encode($result);
		exit;
		break;
	case 'videoInfo'://视频列表
		$id=intval($_POST['id']);
		if($id<=0){
			echo json_encode(array('Flag'=>101, 'FlagString'=>'未知错误'));
			exit;
		}
		$param = array(
				'extparam' => array('Tag'=>'VideoList','Id'=>$id,'GroupId'=>$group_id,'Uin'=>$uin),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10644,'ChildId'=>101)
		);
		$videoInfo = request($param);
		$videoInfo = $videoInfo['Data'];
		if(empty($videoInfo)){
			echo json_encode(array('Flag'=>101, 'FlagString'=>'视频不存在或已删除'));
			exit;
		}
		echo json_encode($videoInfo);
		exit;
		break;	
	case 'videoDel'://视频列表
		$id=intval($_POST['id']);
		if($id<=0){
			echo json_encode(array('Flag'=>101, 'FlagString'=>'未知错误'));
			exit;
		}
		$param = array(
				'extparam' => array('Tag'=>'VideoDel','Id'=>$id,'GroupId'=>$group_id,'Uin'=>$uin),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10015,'ParentId'=>10644,'ChildId'=>103)
		);
		$result = request($param);
		echo json_encode($result);
		exit;
		break;	
}

if($themes=='default'){
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','service'));
}
else{
	$tmp_config=get_config('template','group_site');
	$tmp_config['template_dir'].=$themes.'/tpl/service/';
	$tmp_config['cache_dir'].=$themes.'/tpl/service/';
	$tpl = template::getInstance();
	$tpl->setOptions($tmp_config);
}
include template("account/{$temp}.html",$tpl);