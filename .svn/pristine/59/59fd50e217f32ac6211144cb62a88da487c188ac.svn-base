<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module = isset($_GET['module']) ? $_GET['module'] : 'menu_list';

//验证是否登陆
$user=checkDpLogin();
$Uin=$user['Uin'];
$Nick=$user['Nick'];
//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$isDz=(int)$permisssions['isDz'];
$groupId=(int)$permisssions['groupId'];
$permission=(array)$permisssions['permission'];
$menuPermissions=(array)$permisssions['menuPermissions'];

if(!checkGroupPermission(10328,$permission)){
	alertMsg('无权访问','group.php?module=group_info');
}

//站详情
$param=array(
	'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'获取站信息')
);
$userGroupInfo=request($param);
if($userGroupInfo['Flag']!=100){
	alertMsg($userGroupInfo['FlagString'],'/');
}
/*
if($userGroupInfo['Result']['init'] < 1){
	alertMsg('请先初始化','/group/decoration.php?module=init&url='.urlencode($_SERVER['REQUEST_URI']));
}*/

switch($module){
	case 'menu_list':
		$title='主页装修-左侧菜单管理';
		$param=array(
			'extparam'=>array('Tag'=>'GetMenuList','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10328,'ChildId'=>101,'Desc'=>'一级菜单列表读取')
		);
		$menuList = request($param);
		$menuList = (array)$menuList['MenuList'];
		$template = 'menu_list';
		break;
	case 'menu_info':
		$param=array(
			'extparam'=>array('Tag'=>'MenuInfo','GroupId'=>$groupId,'Id'=>intval($_GET['id'])),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10328,'ChildId'=>101,'Desc'=>'一级菜单列表读取')
		);
		$menuInfo = request($param);
		$menuInfo = $menuInfo['MenuInfo'];
		$menuInfo['other'] = json_decode($menuInfo['other'],true);
		exit(json_encode((array)$menuInfo));
	case 'menu_add':
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if(empty($_POST)){
				alertMsg('上传头像不能大于2M，请重新上传');
			}
			if($_POST['icon'] == -1){
				if(!empty($_FILES['custom_icon']['tmp_name'])){
					if(strpos($_FILES['custom_icon']['type'], 'image') === false){
						//exit(json_encode(array('Flag'=>101,'FlagString'=>'上传图片格式必须为jpg，png，gif格式')));
						alertMsg('上传图片格式必须为jpg，png，gif格式');
					}
					$size = $_FILES['custom_icon']['size']/(pow(1024, 2));
					if($size > 2){
						//exit(json_encode(array('Flag'=>101,'FlagString'=>'上传头像不能大于2M，请重新上传')));
						alertMsg('上传头像不能大于2M，请重新上传');
					}
					$bytes = file_get_contents($_FILES['custom_icon']['tmp_name']);	
				}else{
					//exit(json_encode(array('Flag'=>101,'FlagString'=>'上传菜单图标失败')));
					alertMsg('上传菜单图标失败');
				}
			}else{
				$bytes = file_get_contents("../pic/group/menu_icon{$_POST['icon']}.png");
			}
			$random = time();
			$index = md5($bytes);
			$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
			$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
			$result = array('Flag'=>$query['rst'],'File'=>$index);
			if($result['Flag'] != 100){
				//exit(json_encode(array('Flag'=>101,'FlagString'=>'上传菜单图标失败')));
				alertMsg('上传菜单图标失败');
			}
			$iconMd5 = $result['File'];
			$data = array('name'=>addslashes(trim($_POST['name'])),'icon'=>$iconMd5,'groupId'=>$groupId);
			$param=array(
				'extparam'=>array('Tag'=>'MenuAdd','Data'=>$data),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10328,'ChildId'=>102,'Desc'=>'添加一级菜单')
			);
			$rst = request($param);
			exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'");</script>');
		}
		break;
	case 'menu_edit':
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if(empty($_POST)){
				exit('<script>parent.callback(101,"上传图片不能大于2M，请重新上传");</script>');
			}
			if($_POST['icon'] == -1){
				if(!empty($_FILES['custom_icon']['tmp_name'])){
					if(strpos($_FILES['custom_icon']['type'], 'image') === false){
						exit('<script>parent.callback(101,"上传图片格式必须为jpg，png，gif格式");</script>');
					}
					$size = $_FILES['custom_icon']['size']/(pow(1024, 2));
					if($size > 2){
						exit('<script>parent.callback(101,"上传图片不能大于2M，请重新上传");</script>');
					}
					$bytes = file_get_contents($_FILES['custom_icon']['tmp_name']);	
				}else{
					exit('<script>parent.callback(101,"上传菜单图标失败");</script>');
				}
				$index = md5($bytes);
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				$result = array('Flag'=>$query['rst'],'File'=>$index);
				if($result['Flag'] != 100){
					exit('<script>parent.callback(101,"上传菜单图标失败");</script>');
				}
				$iconMd5 = $result['File'];
			}elseif(isset($_POST['icon']) && $_POST['icon'] > 0){
				$bytes = file_get_contents("../pic/group/menu_icon{$_POST['icon']}.png");
				$index = md5($bytes);
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				$result = array('Flag'=>$query['rst'],'File'=>$index);
				if($result['Flag'] != 100){
					exit('<script>parent.callback(101,"上传菜单图标失败");</script>');
				}
				$iconMd5 = $result['File'];
			}else{
				$iconMd5 = $_POST['current_icon'];
			}
			
			$data = array('name'=>addslashes(trim($_POST['name'])),'icon'=>$iconMd5,'groupId'=>$groupId,'id'=>intval($_POST['id']));
			$param=array(
				'extparam'=>array('Tag'=>'MenuEdit','Data'=>$data),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10328,'ChildId'=>102,'Desc'=>'添加一级菜单')
			);
			$rst = request($param);
			exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'");</script>');
		}
		break;
	case 'set_visible':
		$param=array(
			'extparam'=>array('Tag'=>'MenuVisible','Id'=>intval($_POST['id']),'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10328,'ChildId'=>104,'Desc'=>'一级菜单列表显示设置')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'menu_up':
		$id = intval($_POST['id']);
		$param=array(
			'extparam'=>array('Tag'=>'Up','Id'=>$id,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10328,'ChildId'=>103,'Desc'=>'一级菜单排序')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'menu_down':
		$id = intval($_POST['id']);
		$param=array(
			'extparam'=>array('Tag'=>'Down','Id'=>$id,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10328,'ChildId'=>103,'Desc'=>'一级菜单排序')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'sub_menu_list':
		$superId = intval($_GET['super_id']);
		$title='主页装修-左侧菜单管理';
		$param=array(
			'extparam'=>array('Tag'=>'SubMenuList','SuperId'=>$superId,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10328,'ChildId'=>105,'Desc'=>'二级菜单列表获取')
		);
		$list = request($param);
		$menuName = $list['MenuName'];
		$menuList = $list['MenuList'];
		$rooms = (array)$list['Rooms'];
		$roomList = array();
		foreach($rooms as $key => $val){
			$roomList[$key] = array('id'=>$val['id'],'name'=>addslashes($val['name']));
		}

		unset($list);
		if(empty($menuName)){
			alertMsg('无效一级菜单ID',-1);
		}
		
		$games = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'InteractList','Data'=>array('interact_status'=>1))));
		$games = (array)$games['list'];
		//$games = json_encode((array)$games['list']);
		//全站游戏
		$groupGames = (array)json_decode($userGroupInfo['Result']['games'], true);
		foreach ($groupGames as $key => $val) {
			if(!empty($val['name'])){
				$groupGames[$key]['name'] = urldecode($val['name']);
				$groupGames[$key]['url'] = urldecode($val['url']);
			}else{
				unset($groupGames[$key]);
			}
		}
		$groupGamesJSON = json_encode($groupGames);
		$template = 'sub_menu_list';
		break;
	case 'sub_menu_add':
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if(empty($_POST)){
				exit('<script>parent.callback(101,"上传图片不能大于2M，请重新上传");</script>');
			}
			//链接
			$link_type = intval($_POST['link_type']);
			$other['link_type'] = $link_type;
			if($link_type == 1){
				//取得游戏名称
				$game = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'InteractList','Data'=>array('interact_status'=>1),'Id'=>intval($_POST['game']))));
				$gameName = $game['list']['cmd'];
				$gameName = substr($gameName,4);
				$url = 'http://'.$_SERVER['SERVER_NAME'].'/g/'.intval($_POST['room']).'?Type=Game&GameName='.$gameName;
				$other['roomid'] = intval($_POST['room']);
				$other['gameid'] = intval($_POST['game']);
			}elseif($link_type == 2){
				$url = 'http://wpa.qq.com/msgrd?v=3&uin='.$_POST['qq'].'&site=qq&menu=yes';
				$other['qq'] = $_POST['qq'];
			}elseif($link_type == 3){
				$url = $_POST['url'];
			}elseif($link_type == 4){
				$groupGames = (array)json_decode($userGroupInfo['Result']['games'], true);
				foreach ($groupGames as $key => $val) {
					$groupGames[$key]['name'] = urldecode($val['name']);
					$groupGames[$key]['url'] = urldecode($val['url']);
				}
				$url = $groupGames[$_POST['group_game']]['url'];
				$other['gameid'] = intval($_POST['group_game']);
			}else{
				//exit(json_encode(array('Flag'=>101,'FlagString'=>'请选择链接类型')));
				//alertMsg('请选择链接类型');
				exit('<script>parent.callback(101,"请选择链接类型");</script>');
			}
			$random = time();
			//图标
			if($_POST['icon'] == -1){
				$iconMd5 = '';
			}elseif(isset($_POST['icon']) && $_POST['icon'] > 0){
				if($link_type != 4){
					$bytes = file_get_contents("../pic/gameicon/{$_POST['icon']}.png");
					$index = md5($bytes);
					$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
					$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
					$result = array('Flag'=>$query['rst'],'File'=>$index);
					if($result['Flag'] != 100){
						exit('<script>parent.callback(101,"上传菜单图标失败");</script>');
					}
					$iconMd5 = $result['File'];
				}else{
					$iconMd5 = $groupGames[$_POST['group_game']]['md5'];
				}
			}else{
				if(!empty($_FILES['custom_icon']['tmp_name'])){
					if(strpos($_FILES['custom_icon']['type'], 'image') === false){
						exit('<script>parent.callback(101,"上传图片格式必须为jpg，png，gif格式");</script>');
					}
					$size = $_FILES['custom_icon']['size']/(pow(1024, 2));
					if($size > 2){
						exit('<script>parent.callback(101,"上传图片不能大于2M，请重新上传");</script>');
					}
					$bytes = file_get_contents($_FILES['custom_icon']['tmp_name']);	
					$index = md5($bytes);
					$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
					$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
					$result = array('Flag'=>$query['rst'],'File'=>$index);
					if($result['Flag'] != 100){
						exit('<script>parent.callback(101,"上传菜单图标失败");</script>');
					}
					$iconMd5 = $result['File'];
				}else{
					exit('<script>parent.callback(101,"请上传图标");</script>');
				}
			}

			$data = array('name'=>addslashes(mb_substr(trim($_POST['name']),0,20,'utf8')),'icon'=>$iconMd5,'groupId'=>$groupId,'url'=>$url,'superId'=>intval($_POST['super_id']));
			$data['other'] = $other;
			$param=array(
				'extparam'=>array('Tag'=>'SubMenuAdd','Data'=>$data),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10328,'ChildId'=>106,'Desc'=>'添加二级菜单')
			);
			$rst = request($param);
			exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'");</script>');
		}
		break;
	case 'sub_menu_edit':
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if(empty($_POST)){
				exit('<script>parent.callback(101,"上传图片不能大于2M，请重新上传");</script>');
			}
			//链接
			$link_type = intval($_POST['link_type']);
			$other['link_type'] = $link_type;
			if($link_type == 1){
				//取得游戏名称
				$game = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'InteractList','Data'=>array('interact_status'=>1),'Id'=>intval($_POST['game']))));
				$gameName = $game['list']['cmd'];
				$gameName = substr($gameName,4);
				$url = 'http://'.$_SERVER['SERVER_NAME'].'/g/'.intval($_POST['room']).'?Type=Game&GameName='.$gameName;
				$other['roomid'] = intval($_POST['room']);
				$other['gameid'] = intval($_POST['game']);
			}elseif($link_type == 2){
				$url = 'http://wpa.qq.com/msgrd?v=3&uin='.$_POST['qq'].'&site=qq&menu=yes';
				$other['qq'] = $_POST['qq'];
			}elseif($link_type == 3){
				$url = $_POST['url'];
			}elseif($link_type == 4){
				$groupGames = (array)json_decode($userGroupInfo['Result']['games'], true);
				foreach ($groupGames as $key => $val) {
					$groupGames[$key]['name'] = urldecode($val['name']);
					$groupGames[$key]['url'] = urldecode($val['url']);
				}
				$url = $groupGames[$_POST['group_game']]['url'];
				$other['gameid'] = intval($_POST['group_game']);
			}else{
				exit('<script>parent.callback(101,"请选择链接类型");</script>');
			}
			//图标
			if($_POST['icon'] == -1){
				$iconMd5 = '';
			}elseif(isset($_POST['icon']) && $_POST['icon'] > 0){
				if($link_type != 4){
					$bytes = file_get_contents("../pic/gameicon/{$_POST['icon']}.png");
					$index = md5($bytes);
					$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
					$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
					$result = array('Flag'=>$query['rst'],'File'=>$index);
					if($result['Flag'] != 100){
						exit('<script>parent.callback(101,"上传菜单图标失败");</script>');
					}
					$iconMd5 = $result['File'];
				}else{
					$iconMd5 = $groupGames[$_POST['group_game']]['md5'];
				}
			}elseif($_POST['icon'] == '0'){
				if(!empty($_FILES['custom_icon']['tmp_name'])){
					if(strpos($_FILES['custom_icon']['type'], 'image') === false){
						exit('<script>parent.callback(101,"上传图片格式必须为jpg，png，gif格式");</script>');
					}
					$size = $_FILES['custom_icon']['size']/(pow(1024, 2));
					if($size > 2){
						exit('<script>parent.callback(101,"上传图片不能大于2M，请重新上传");</script>');
					}
					$bytes = file_get_contents($_FILES['custom_icon']['tmp_name']);	
					$index = md5($bytes);
					$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
					$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
					$result = array('Flag'=>$query['rst'],'File'=>$index);
					if($result['Flag'] != 100){
						exit('<script>parent.callback(101,"上传菜单图标失败");</script>');
					}
					$iconMd5 = $result['File'];
				}else{
					$iconMd5 = $_POST['current_icon'];
				}
			}else{
				$iconMd5 = $_POST['current_icon'];
			}
			

			$data = array('name'=>addslashes(mb_substr(trim($_POST['name']),0,20,'utf8')),'icon'=>$iconMd5,'groupId'=>$groupId,'url'=>$url,'id'=>intval($_POST['id']),'superId'=>intval($_POST['super_id']));
			$data['other'] = $other;
			$param=array(
				'extparam'=>array('Tag'=>'SubMenuEdit','Data'=>$data),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10328,'ChildId'=>106,'Desc'=>'编辑二级菜单')
			);
			$rst = request($param);
			exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'");</script>');
		}
		break;
	case 'sub_menu_set_visible':
		$param=array(
			'extparam'=>array('Tag'=>'SubMenuVisible','Id'=>intval($_POST['id']),'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10328,'ChildId'=>108,'Desc'=>'一级菜单列表显示设置')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'sub_menu_up':
		$id = intval($_POST['id']);
		$param=array(
			'extparam'=>array('Tag'=>'Up','Id'=>$id,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10328,'ChildId'=>107,'Desc'=>'二级菜单排序')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'sub_menu_down':
		$id = intval($_POST['id']);
		$param=array(
			'extparam'=>array('Tag'=>'Down','Id'=>$id,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10328,'ChildId'=>107,'Desc'=>'二级菜单排序')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
}

$tool = 'menu';
$serviceType='decoration';

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('decoration/'.$template.'.html',$tpl);