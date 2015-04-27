<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'activity_list':$_GET['module'];

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

if(!checkGroupPermission(10330,$permission)){
	alertMsg('无权访问','group.php?module=group_info');
}

switch ($module) {
	case 'activity_list':
		$title='主页装修-活动设置';
		$template = 'activity_list';
		$param = array(
			'extparam'=>array('Tag'=>'ActivityList','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10330,'ChildId'=>101,'Desc'=>'活动列表读取')
		);
		$list = request($param);
		$activityList = $list['List'];
		break;
	case 'activity_add':
		$title='主页装修-活动设置';
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if(empty($_POST)){
				exit('<script>parent.callback(101,"上传图片不能大于2M，请重新上传");</script>');
			}
			if(mb_strlen(htmlspecialchars($_POST['title'],ENT_NOQUOTES)) > 45){
				exit('<script>parent.callback(101,"活动标题不能超过15个字");</script>');
			}
			if(!empty($_FILES['image']['tmp_name'])){
				if(strpos($_FILES['image']['type'], 'image') === false){
					exit('<script>parent.callback(101,"上传图片格式必须为jpg，png，gif格式");</script>');
				}
				$size = $_FILES['image']['size']/(pow(1024, 2));
				if($size > 2){
					exit('<script>parent.callback(101,"上传图片不能大于2M，请重新上传");</script>');
				}
				$random = time();
				$bytes = file_get_contents($_FILES['image']['tmp_name']);
				$index = md5($bytes);
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				$result = array('Flag'=>$query['rst'],'File'=>$index);
				if($result['Flag'] != 100){
					exit('<script>parent.callback(101,"活动宣传图上传失败");</script>');
				}
				$data = $_POST;
				$data['image'] = $result['File'];
				$data['groupId'] = $groupId;
				$data['title'] = htmlspecialchars($data['title'],ENT_NOQUOTES);
				$param = array(
					'extparam'=>array('Tag'=>'ActivityAdd','Data'=>$data),
					'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10330,'ChildId'=>104,'Desc'=>'发布新活动')
				);
				$rst = request($param);
				exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'");</script>');
			}else{
				exit('<script>parent.callback(101,"活动宣传图上传失败");</script>');
			}
		}else{
			//得到站下房间
			$param=array(
			 	'extparam'=>array('Tag'=>'GetGroupRooms','GroupId'=>$groupId),
     			'param'=>array('BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10260,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取站下房间')
    		);
    		$roomList = request($param);
    		$roomList = $roomList['roomList'];
    		$rooms = array();
			foreach ($roomList as $key => $val) {
				if($val['status'] < 1){
					continue;
				}
				$rooms[$val['id']] = empty($val['name']) ? $val['id'] : $val['name'];
			}
			$template = 'activity_info';
		}
		break;
	case 'activity_edit':
		$title='主页装修-活动设置';
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if(empty($_POST)){
				exit('<script>parent.callback(101,"上传图片不能大于2M，请重新上传");</script>');
			}
			if(mb_strlen(htmlspecialchars($_POST['title'],ENT_NOQUOTES)) > 45){
				exit('<script>parent.callback(101,"活动标题不能超过15个字");</script>');
			}
			if(!empty($_FILES['image']['tmp_name'])){
				if(strpos($_FILES['image']['type'], 'image') === false){
					exit('<script>parent.callback(101,"上传图片格式必须为jpg，png，gif格式");</script>');
				}
				$size = $_FILES['image']['size']/(pow(1024, 2));
				if($size > 2){
					exit('<script>parent.callback(101,"上传图片不能大于2M，请重新上传");</script>');
				}
				$bytes = file_get_contents($_FILES['image']['tmp_name']);
				$index = md5($bytes);
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				$result = array('Flag'=>$query['rst'],'File'=>$index);
				if($result['Flag'] != 100){
					exit('<script>parent.callback(101,"活动宣传图上传失败");</script>');
				}
				$image = $result['File'];
			}else{
				$image = $_POST['current_image'];
			}
			$id = intval($_POST['id']);
			unset($_POST['id']);

			$data = $_POST;
			$data['image'] = $image;
			$data['groupId'] = $groupId;
			$data['title'] = htmlspecialchars($data['title'],ENT_NOQUOTES);
			$param = array(
				'extparam'=>array('Tag'=>'ActivityUpdate','Data'=>$data,'Id'=>$id),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10330,'ChildId'=>104,'Desc'=>'编辑活动')
			);
			$rst = request($param);
			exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'");</script>');
		}else{
			$param = array(
				'extparam'=>array('Tag'=>'ActivityInfo','Id'=>intval($_GET['id'])),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10330,'ChildId'=>101,'Desc'=>'活动详情')
			);
			$info = request($param);
			$info = $info['Info'];

			//得到站下房间
			$param=array(
			 	'extparam'=>array('Tag'=>'GetGroupRooms','GroupId'=>$groupId),
     			'param'=>array('BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10260,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取站下房间')
    		);
    		$roomList = request($param);
    		$roomList = $roomList['roomList'];
    		$rooms = array();
			foreach ($roomList as $key => $val) {
				if($val['status'] < 1){
					continue;
				}
				$rooms[$val['id']] = empty($val['name']) ? $val['id'] : $val['name'];
			}
			$template = 'activity_info';
		}
		break;
	case 'activity_recommend':
		$id = intval($_POST['id']);
		$param = array(
			'extparam'=>array('Tag'=>'ActivityRecommend','Id'=>intval($id),'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10330,'ChildId'=>102,'Desc'=>'活动列表推荐')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'activity_sort':
		$param = array(
			'extparam'=>array('Tag'=>'ActivityOrder','Data'=>$_POST),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10330,'ChildId'=>103,'Desc'=>'活动列表排序设置')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'activity_del':
		$_POST['group_id'] = $groupId;
		$param = array(
			'extparam'=>array('Tag'=>'ActivityDel','Data'=>$_POST),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10330,'ChildId'=>103,'Desc'=>'活动列表删除')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
}

$tool = 'activity';
$serviceType = 'decoration';

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('decoration/'.$template.'.html',$tpl);