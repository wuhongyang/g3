<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';
require_once 'image_to_oss.php';

$module = empty($_GET['module'])?'recommend_list':$_GET['module'];

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

if(!checkGroupPermission(10327,$permission)){
	alertMsg('无权访问','group.php?module=group_info');
}

//站详情
$param=array(
	'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$groupId,'IsDetails'=>true),
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

switch ($module) {
	case 'recommend_cat_list':
		$type = array(
			1 => '房间推荐',
			2 => '会员推荐',
			3 => '通用推荐',
			4 => '艺人推荐'
		);
		$param=array(
			'extparam'=>array('Tag'=>'RecommendCatList','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>108,'Desc'=>'推荐类别列表')
		);
		$list = request($param);
		$list = (array)$list['List'];
		$template = 'recommend_cat_list';
		break;
	case 'recommend_cat_add':
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$iconMd5 = '';
			if($_POST['style'] > 0){
				$rst = check_upload($_FILES['custom_icon']);
				if($rst['Flag'] != 100){
					exit('<script>parent.callback(101,"'.$rst['FlagString'].'");</script>');
				}
				$rst = send_to_oss($_FILES['custom_icon']['tmp_name']);
				if($rst['Flag'] != 100){
					exit('<script>parent.callback(101,"上传图标失败");</script>');
				}
				$iconMd5 = $rst['File'];
			}
			$data = $_POST;
			$data['group_id'] = $groupId;
			$data['icon'] = $iconMd5;
			unset($data['style']);
			$param=array(
				'extparam'=>array('Tag'=>'RecommendCatAdd','Data'=>$data),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>112,'Desc'=>'推荐类别添加')
			);
			$rst = request($param);
			exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'");</script>');
		}else{
			exit('<script>parent.callback(101,"非法调用");</script>');
		}
		break;
	case 'recommend_cat_info':
		$param=array(
			'extparam'=>array('Tag'=>'RecommendCatInfo','GroupId'=>$groupId,'Id'=>$_GET['id']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>108,'Desc'=>'推荐类别详情')
		);
		$info = request($param);
		$info = (array)$info['Info'];
		exit(json_encode($info));
		break;
	case 'recommend_cat_edit':
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$iconMd5 = '';
			if($_POST['style'] > 0){
				if(empty($_FILES['custom_icon']['name'])){
					$iconMd5 = $_POST['icon'];
				}else{
					$rst = check_upload($_FILES['custom_icon']);
					if($rst['Flag'] != 100){
						exit('<script>parent.callback(101,"'.$rst['FlagString'].'");</script>');
					}
					$rst = send_to_oss($_FILES['custom_icon']['tmp_name']);
					if($rst['Flag'] != 100){
						exit('<script>parent.callback(101,"上传图标失败");</script>');
					}
					$iconMd5 = $rst['File'];
				}
				
			}
			$data = $_POST;
			$data['icon'] = $iconMd5;
			$id = $data['id'];
			unset($data['id']);
			unset($data['style']);
			$param=array(
				'extparam'=>array('Tag'=>'RecommendCatEdit','Id'=>$id,'GroupId'=>$groupId,'Data'=>$data),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>112,'Desc'=>'推荐类别添加')
			);
			$rst = request($param);
			exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'");</script>');
		}else{
			exit('<script>parent.callback(101,"非法调用");</script>');
		}
		break;
	case 'recommend_cat_order':
		$id = $_POST['id'];
		$type = isset($_POST['type']) ? $_POST['type'] : 1;//默认上移
		$param=array(
			'extparam'=>array('Tag'=>'RecommendCatOrder','GroupId'=>$groupId,'Id'=>$id,'Type'=>$type),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>109,'Desc'=>'推荐类别移动')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'recommend_cat_visible':
		$id = $_POST['id'];
		$param=array(
			'extparam'=>array('Tag'=>'RecommendCatVisible','GroupId'=>$groupId,'Id'=>$id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>110,'Desc'=>'推荐类别设置')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'recommend_sub_cat_show':
		$parent_id = $_GET['parent_id'];
		$param=array(
			'extparam'=>array('Tag'=>'RecommendSubCatShow','GroupId'=>$groupId,'ParentId'=>$parent_id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>111,'Desc'=>'推荐分类列表')
		);
		$list = request($param);
		$parentInfo = (array)$list['ParentInfo'];
		$list = $list['List'];
		$template = 'recommend_sub_cat_list'.$parentInfo['type'];
		break;
	case 'recommend_sub_cat_add':
		$data = $_POST;
		$data['group_id'] = $groupId;
		$param=array(
			'extparam'=>array('Tag'=>'RecommendSubCatAdd','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>112,'Desc'=>'推荐分类添加')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'recommend_sub_cat_artist_add':
		$data = $_POST;
		$data['group_id'] = $groupId;
		$param=array(
			'extparam'=>array('Tag'=>'RecommendSubCatArtistAdd','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>117,'Desc'=>'推荐分类添加')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'recommend_sub_cat_order':
		$id = $_POST['id'];
		$parent_id = $_POST['parent_id'];
		$type = isset($_POST['type']) ? $_POST['type'] : 1;//默认上移
		$param=array(
			'extparam'=>array('Tag'=>'RecommendSubCatOrder','GroupId'=>$groupId,'Id'=>$id,'ParentId'=>$parent_id,'Type'=>$type),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>113,'Desc'=>'推荐类别移动')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'recommend_sub_cat_info':
		$id = $_GET['id'];
		$param=array(
			'extparam'=>array('Tag'=>'RecommendSubCatInfo','GroupId'=>$groupId,'Id'=>$id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>111,'Desc'=>'推荐分类列表')
		);
		$info = request($param);
		exit(json_encode((array)$info['Info']));
		break;
	case 'recommend_sub_cat_edit':
		$data = $_POST;
		$id = $data['id'];
		unset($data['id']);
		$param=array(
			'extparam'=>array('Tag'=>'RecommendSubCatEdit','Data'=>$data,'GroupId'=>$groupId,'Id'=>$id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>112,'Desc'=>'推荐分类编辑')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'recommend_sub_cat_artist_edit':
		$data = $_POST;
		$id = $data['id'];
		unset($data['id']);
		$param=array(
			'extparam'=>array('Tag'=>'RecommendSubCatArtistEdit','Data'=>$data,'GroupId'=>$groupId,'Id'=>$id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>117,'Desc'=>'推荐分类编辑')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'recommend_sub_cat_del':
		$id = $_POST['id'];
		$param=array(
			'extparam'=>array('Tag'=>'RecommendSubCatDel','GroupId'=>$groupId,'Id'=>$id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>114,'Desc'=>'推荐分类删除')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'recommend_sub_cat_visible':
		$id = $_POST['id'];
		$param=array(
			'extparam'=>array('Tag'=>'RecommendSubCatVisible','GroupId'=>$groupId,'Id'=>$id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>115,'Desc'=>'推荐分类设置')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'recommend_show':
		$parent_id = $_GET['parent_id'];
		$pparent_id = $_GET['pparent_id'];
		$param=array(
			'extparam'=>array('Tag'=>'RecommendShow','GroupId'=>$groupId,'ParentId'=>$parent_id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>101,'Desc'=>'推荐位列表')
		);
		$list = request($param);
		$type = $list['Type'];
		$parent_info = $list['ParentInfo'];
		$parent_name = $parent_info['name'];

		$templates = array(1=>'room',2=>'vip',3=>'common',4=>'artist');
		if($type == 1){
			$rooms = (array)$list['Rooms'];
			//去除冻结状态的
			$rooms_JSON = array();
			foreach ($rooms as $k => $val) {
				if($val['status'] < 1){
					continue;
				}
				$rooms_JSON[$k] = $val;
			}
		}elseif($type == 3){
			$mode = $parent_info['mode'];
		}elseif($type == 4){
			//得到站下所有艺人
			$artists = getGroupChannelUser($groupId);
		}

		$template = "recommend_{$templates[$type]}_list";
		$list = $list['List'];
		
		break;
	case 'room_recommend_add':
		$code = intval($_POST['code']);
		$data = array('code'=>$code,'group_id'=>$groupId,'parent_id'=>$_POST['parent_id']);
		$param=array(
			'extparam'=>array('Tag'=>'AddRoomRecommend','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>102,'Desc'=>'添加房间推荐位')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'vip_recommend_add':
		$data = array('code'=>$_POST['code'],'group_id'=>$groupId,'parent_id'=>$_POST['parent_id']);
		$param=array(
			'extparam'=>array('Tag'=>'AddVipRecommend','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>105,'Desc'=>'添加推荐位')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'common_recommend_add':
		$data = $_POST;
		$data['group_id'] = $groupId;
		$param=array(
			'extparam'=>array('Tag'=>'AddCommonRecommend','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>128,'Desc'=>'添加通用推荐位')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'common_recommend_info':
		$param=array(
			'extparam'=>array('Tag'=>'RecommendCommonInfo','Id'=>$_GET['id'],'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>126,'Desc'=>'通用推荐位详情')
		);
		$info = request($param);
		exit(json_encode((array)$info['Info']));
		break;
	case 'common_recommend_edit':
		$data = $_POST;
		$id = $data['id'];
		unset($data['id']);
		$param=array(
			'extparam'=>array('Tag'=>'RecommendCommonEdit','Id'=>$id,'GroupId'=>$groupId,'Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>129,'Desc'=>'通用推荐位详情')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'recommend_common_del':
		$param=array(
			'extparam'=>array('Tag'=>'RecommendCommonDel','Id'=>$_POST['id'],'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>131,'Desc'=>'通用推荐位详情')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'recommend_common_order':
		$id = intval($_POST['id']);
		$type = $_POST['type'];
		$param = array(
			'extparam'=>array('Tag'=>'RecommendCommonOrder','Id'=>$id,'GroupId'=>$groupId,'Type'=>$type),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>130,'Desc'=>'推荐位排序')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'recommend_order':
		$id = intval($_POST['id']);
		$type = $_POST['type'];
		$param = array(
			'extparam'=>array('Tag'=>'RecommendOrder','Id'=>$id,'GroupId'=>$groupId,'Type'=>$type),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>103,'Desc'=>'推荐位排序')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'recommend_del':
		$id = intval($_POST['id']);
		$param = array(
			'extparam'=>array('Tag'=>'RecommendDel','Id'=>$id,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10327,'ChildId'=>104,'Desc'=>'推荐位删除')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'group_vip'://得到站会员
		$info = getGroupVip(intval($_GET['uin']),$groupId);
		if($info['uin'] != intval($_GET['uin'])){
			$info = array('Flag'=>101,'FlagString'=>'不是本站会员');
		}else{
			$userInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$info['uin'])));
			$info['nick'] = !empty($userInfo['baseInfo']['nick']) ? $userInfo['baseInfo']['nick'] : $info['uin'];
		}	
		exit(json_encode($info));
		break;
}
$tool = 'recommend';
$serviceType = 'decoration';

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('decoration/'.$template.'.html',$tpl);