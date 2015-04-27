<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'group_info':$_GET['module'];
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

//站详情
$param=array(
	'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$groupId,'IsDetails'=>true),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'获取站信息')
);
$userGroupInfo=request($param);
if($userGroupInfo['Flag']!=100){
	alertMsg($userGroupInfo['FlagString'],'/');
}
$userGroupInfo=$userGroupInfo['Result'];
$userGroupInfo['images']=(array)json_decode($userGroupInfo['images'],true);

//是否拥有权限
if(!checkGroupPermission(10318,$permission)){
	alertMsg('无权访问','group.php?module=group_info');
}

$groupManage=true;
switch ($module) {
	case 'init':
		if(isset($_POST) && !empty($_POST)){
			$param = array(
				'extparam'=>array('Tag'=>'Init','GroupId'=>$groupId),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>103,'Desc'=>'初始化')
			);
			$rst = request($param);
			exit(json_encode($rst));
		}else{
			$url = urldecode($_GET['url']);
			$template = 'init';
		}
		break;
	case 'group_info':
		$title='主页装修-基本资料';
		$tool = 'basic';
		$template='group_update';
	break;
	case 'upload_logo':
		if(!empty($_FILES['fileField'])){
			if(strpos($_FILES['fileField']['type'], 'image') === false){
				alertMsg('上传图片格式必须为jpg，png，gif格式');
			}
			$size = $_FILES['fileField']['size']/(pow(1024, 2));
			if($size > 2){
				alertMsg('上传图片不能大于2M，请重新上传');
			}
			$bytes = file_get_contents($_FILES['fileField']['tmp_name']);
			$index = md5($bytes);
			$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
			$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
			if($query['rst']==100){
				$rst['Flag'] = $query['rst'];
				$rst['File'] = $index;
				$rst['FileName'] = $_FILES['fileField']['name'];
				@unlink($_FILES['fileField']['tmp_name']);
			}
			$rst = json_encode($rst);
		}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
			alertMsg('上传图片不能大于2M，请重新上传');
		}
		$template = 'upload';
	break;
	case 'group_update_submit':
		if(empty($_POST['name'])){
			alertMsg('站名称不能为空');
		}
		if(mb_strlen($_POST['name']) > 30){
			alertMsg('站名称不能大于10个字');
		}
		$notice = addslashes($_POST['notice']);
		if(mb_strlen($notice) > 120){
			alertMsg('站公告不能大于40个中文字符');
		}
		$data = array('name'=>addslashes($_POST['name']),'notice'=>$notice,'logo'=>$_POST['logo']);
		$param=array(
			'extparam'=>array('Tag'=>'UpdateGroupInfo','GroupId'=>$userGroupInfo['groupid'],'Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>101,'Desc'=>'修改站点资料')
		);
		$result=request($param);
		if($result['Flag'] != 100){
			alertMsg($result['FlagString']);
		}
		alertMsg($result['FlagString'],'decoration.php?module=group_info');
	break;
	case 'style':
		$tool = 'style';
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$style_id = intval($_POST['style_id']);
			//保存选中的风格
			$param = array(
				'extparam'=>array('Tag'=>'GroupStyleSetting','GroupId'=>$groupId,'StyleId'=>$style_id),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>104,'Desc'=>'选择风格')
			);
			$result = request($param);
			if($result['Flag'] == 100){ 
				//1.找到该风格下的图
				//2.把该风格的图上传上去
				//风格详情
				$param = array(
					'extparam'=>array('Tag'=>'StyleInfo','StyleId'=>$style_id),
					'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>103,'Desc'=>'风格详情')
				);
				$styleInfo = request($param);
				$styleInfo = $styleInfo['Info'];
				$groupbg_id = intval($styleInfo['bg_img']);
				$banner_id = intval($styleInfo['banner']);

				$groupbg_exist = socket_request(cdn_url(PIC_API_PATH."/groupbg/{$groupId}/0/0.jpg"));
				if($groupbg_id > 0){
					if($groupbg_exist){
						//通过pic_id得到图片
						$picInfo = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'PicList','mypost'=>array('id'=>$groupbg_id))));
						$md5 = $picInfo['lists'][0]['img_path'];
						$bytes = socket_request(cdn_url(PIC_API_PATH."/p/{$md5}/0/0.jpg"));
						if($bytes){
							$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'groupbg','Index'=>$groupId);
							$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
							$urls[] = PIC_API_PATH."/groupbg/{$groupId}/0/0.jpg";
							$urls[] = PIC_API_PATH."/groupbg/{$groupId}/96/25.jpg";
						}
					}
				}

				$groupbanner_exist = socket_request(cdn_url(PIC_API_PATH."/groupbanner/{$groupId}/0/0.jpg"));
				if($banner_id > 0){
					if($groupbanner_exist){
						//通过pic_id得到图片
						$picInfo = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'PicList','mypost'=>array('id'=>$banner_id))));
						$md5 = $picInfo['lists'][0]['img_path'];
						$bytes = socket_request(cdn_url(PIC_API_PATH."/p/{$md5}/0/0.jpg"));
						if($bytes){
							$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'groupbanner','Index'=>$groupId);
							$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
							$urls[] = PIC_API_PATH."/groupbanner/{$groupId}/0/0.jpg";
							$urls[] = PIC_API_PATH."/groupbanner/{$groupId}/96/25.jpg";
						}
					}
				}
				
				if(!empty($urls)){
					cdn_url($urls, 'CDN_UPLOAD_CLEAR');
				}
			}else{
				exit(-1);
			}
		}else{
			//得到站点风格设置信息
			$param = array(
				'extparam'=>array('Tag'=>'GroupStyle','GroupId'=>$groupId),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>103,'Desc'=>'站点当前风格')
			);
			$group_style_info = request($param);
			$group_style_info = $group_style_info['StyleInfo'];
			$style = $group_style_info['style'];
			$group_style_info['style_name'] = $style['name'];
			$group_style_info['cat_id'] = $style['cat_id'];
			$picInfo = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'PicList','mypost'=>array('id'=>(int)$style['thumb']))));
			$md5 = $picInfo['lists'][0]['img_path'];
			$group_style_info['md5'] = $md5;
			//得到风格列表
			$param = array(
				'extparam'=>array('Tag'=>'StyleList','CatId'=>intval($_GET['cat_id'])),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>103,'Desc'=>'站点当前风格')
			);
			$style_list = request($param);
			$p = $style_list['Page'];
			$style_list = (array)$style_list['StyleList'];

			foreach ($style_list as $key => $val) {
				//得到图片
				$picInfo = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'PicList','mypost'=>array('id'=>(int)$val['thumb']))));
				$md5 = $picInfo['lists'][0]['img_path'];
				$style_list[$key]['color_style'] = unserialize($val['color_style']);
				$style_list[$key]['md5'] = $md5;
			}
			//获取所有风格分类
			$param = array(
				'extparam'=>array('Tag'=>'StyleCatList'),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>103,'Desc'=>'风格分类')
			);
			$cats = request($param);
			$cats = (array)$cats['Category'];
			if(!empty($group_style_info)){
				foreach ($cats as $cat) {
					if($cat['id'] == $group_style_info['cat_id']){
						$group_style_info['cat_name'] = $cat['name'];
						break;
					}
				}
			}
			$template = 'style_list';
		}
		break;
	case 'style_info':
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if(empty($_FILES)){
				alertMsg('上传图片不能大于2M，请重新上传');
			}
			if(!empty($_FILES['custom_bg']['tmp_name'])){
				if(strpos($_FILES['custom_bg']['type'], 'image') === false){
					alertMsg('上传图片格式必须为jpg，png，gif格式');
				}
				$size = $_FILES['custom_bg']['size']/(pow(1024, 2));
				if($size > 2){
					alertMsg('上传图片不能大于2M，请重新上传');
				}
				$bytes = file_get_contents($_FILES['custom_bg']['tmp_name']);
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'groupbg','Index'=>$groupId);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				$result = array('Flag'=>$query['rst'],'File'=>$index);
				if($result['Flag'] != 100){
					alertMsg('上传站点背景失败');
				}
				$urls[] = PIC_API_PATH."/groupbg/{$groupId}/0/0.jpg";
				$urls[] = PIC_API_PATH."/groupbg/{$groupId}/160/100.jpg";
			}
			
			if(!empty($_FILES['custom_banner']['tmp_name'])){
				list($width,$height) = getimagesize($_FILES['custom_banner']['tmp_name']); 
				if($width < 960){
					alertMsg('需上传960px宽度以上的图片');
				}
				if($height > 500){
					alertMsg('上传图片高度不能超过500px');
				}
				$type_arr = explode('/', $_FILES['custom_banner']['type']);
				$image_type = $type_arr[1];
				if(!in_array($image_type, array('png','jpg','jpeg','pjpeg'))){
					alertMsg('上传图片格式必须为jpg，png格式');
				}
				$size = $_FILES['custom_banner']['size']/(pow(1024, 2));
				if($size > 2){
					alertMsg('上传图片不能大于2M，请重新上传');
				}
				$bytes = file_get_contents($_FILES['custom_banner']['tmp_name']);
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'groupbanner','Index'=>$groupId);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				$result = array('Flag'=>$query['rst'],'File'=>$index);
				if($result['Flag'] != 100){
					alertMsg('上传顶部通栏失败');
				}
				$urls[] = PIC_API_PATH."/groupbanner/{$groupId}/0/0.jpg";
				$urls[] = PIC_API_PATH."/groupbanner/{$groupId}/610/130.jpg";
			}
			$groupbanner_exist = socket_request(cdn_url(PIC_API_PATH."/groupbanner/{$groupId}/0/0.jpg"));
			$_POST['banner_status'] = $groupbanner_exist? $_POST['banner_status'] : 0;
			$data = array('bg_align'=>$_POST['bg_align'],'bg_tile'=>$_POST['bg_tile'],'bg_status'=>intval($_POST['bg_status']),'banner_status'=>intval($_POST['banner_status']),'uptime'=>(int)time());
			$param=array(
				'extparam'=>array('Tag'=>'UpdateGroupStyle','GroupId'=>$groupId,'Data'=>$data),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>102,'Desc'=>'主页风格设置')
			);
			$rst = request($param);
			if($rst['Flag'] != 100){
				alertMsg('主页风格设置失败');
			}

			if(!empty($urls)){
				cdn_url($urls, 'CDN_UPLOAD_CLEAR');
			}
			alertMsg('成功','?module=style');
		}else{
			$style_id = intval($_GET['style_id']);
			$param = array(
				'extparam'=>array('Tag'=>'StyleInfo','StyleId'=>$style_id),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>103,'Desc'=>'风格详情')
			);
			$styleInfo = request($param);
			$styleInfo = $styleInfo['Info'];
			$picInfo = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'PicList','mypost'=>array('id'=>(int)$styleInfo['bg_img']))));
			$styleInfo['bg_md5'] = $picInfo['lists'][0]['img_path'];
			$picInfo = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'PicList','mypost'=>array('id'=>(int)$styleInfo['banner']))));
			$styleInfo['banner_md5'] = $picInfo['lists'][0]['img_path'];
			$param = array(
				'extparam'=>array('Tag'=>'GroupStyle','GroupId'=>$groupId),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>103,'Desc'=>'站点当前风格')
			);
			$group_style_info = request($param);
			$group_style_info = $group_style_info['StyleInfo'];
			$styleInfo['bg_status'] = $group_style_info['bg_status'];
			$styleInfo['bg_tile'] = $group_style_info['bg_tile'];
			$styleInfo['bg_align'] = $group_style_info['bg_align'];
			$styleInfo['banner_status'] = $group_style_info['banner_status'];
			
			//$groupbg_exists = socket_request(PIC_API_PATH."/groupbg/{$groupId}/0/0");
			//$groupbg_exist = $groupbg_exist!='0' ? true : false;

			$groupbanner_exist = socket_request(cdn_url(PIC_API_PATH."/groupbanner/{$groupId}/0/0.jpg"));
			$template = 'style_info';
		}
		break;
	case 'del_style_pic':
		$type = $_POST['type'];
		$style_id = intval($_POST['style_id']);
		if(!in_array($type, array('groupbg','groupbanner'))){
			exit(-1);//非法参数
		}
		//风格详情
		$param = array(
			'extparam'=>array('Tag'=>'StyleInfo','StyleId'=>$style_id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>103,'Desc'=>'风格详情')
		);
		$styleInfo = request($param);
		$styleInfo = $styleInfo['Info'];
		$groupbg_id = intval($styleInfo['bg_img']);
		$banner_id = intval($styleInfo['banner']);
		$pic_id = ($type == 'groupbg') ? intval($styleInfo['bg_img']) : intval($styleInfo['banner']);
		if($pic_id > 0){
			$picInfo = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'PicList','mypost'=>array('id'=>$pic_id))));
			$md5 = $picInfo['lists'][0]['img_path'];
			$bytes = socket_request(cdn_url(PIC_API_PATH."/p/{$md5}/0/0.jpg"));
			if($bytes!='0'){
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type'=>$type,'Index'=>$groupId);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				$urls[] = PIC_API_PATH."/{$type}/{$groupId}/0/0.jpg";
				$urls[] = PIC_API_PATH."/{$type}/{$groupId}/160/100.jpg";
			}
			if(!empty($urls)){
				cdn_url($urls, 'CDN_UPLOAD_CLEAR');
			}
			exit;
		}else{
			echo -2;
			exit;
		}
		break;
	case 'roomnotice':
		$tool = 'carousel';
		$title='主页装修-主页轮播图';
		$roomNotice = $userGroupInfo['images'];

		//得到活动图
		$where = array('end_time'=>array('value'=>date('Y-m-d'),'operate'=>'>='));
		$param = array(
			'extparam'=>array('Tag'=>'ActivityListNoPage','GroupId'=>$groupId,'Where'=>$where),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10330,'ChildId'=>101,'Desc'=>'活动列表读取')
		);
		$activityList = request($param);
		$activityList = (array)$activityList['List'];
		$actList = array();
		foreach ($activityList as $key => $val) {
			$actList[$key] = array('name'=>$val['title'],'image'=>$val['image'],'id'=>$val['id']);
		}
		$actList = json_encode($actList);
		
		$param=array(
			'extparam'=>array('Tag'=>'CarouselList','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>101,'Desc'=>'获取轮播图信息')
		);
		$list = request($param);
		$carouselList = $list['List'];

		$template = "roomnotice";
		break;
	case 'up_carousel':
		$tool = 'carousel';
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
			if(empty($_FILES)){
				exit('<script>parent._show_msg("上传图片不能大于2M，请重新上传",-1)</script>');
			}
			if(!empty($_FILES['carousel']['tmp_name'])){
				if(strpos($_FILES['carousel']['type'], 'image') === false){
					exit('<script>parent._show_msg("上传图片格式必须为jpg，png，gif格式",-1)</script>');
				}
				$size = $_FILES['carousel']['size']/(pow(1024, 2));
				if($size > 2){
					exit('<script>parent._show_msg("上传图片不能大于2M，请重新上传",-1)</script>');
				}
				$bytes = file_get_contents($_FILES['carousel']['tmp_name']);
				$index = md5($bytes);
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				$rst = json_encode(array('Flag'=>$query['rst'],'File'=>$index));
			}else{
				exit('<script>parent._show_msg("无效的上传文件",-1)</script>');
			}
		}
		$template = 'up_carousel';
		break;
	case 'add_carousel':
		$data = $_POST;
		$data['groupId'] = $groupId;
		$param=array(
			'extparam'=>array('Tag'=>'AddCarousel','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>101,'Desc'=>'添加站轮播图')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'carousel_up':
		$id = intval(($_POST['id']));
		$param=array(
			'extparam'=>array('Tag'=>'CarouselUp','Id'=>$id,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>101,'Desc'=>'轮播图上移')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'carousel_down':
		$id = intval(($_POST['id']));
		$param=array(
			'extparam'=>array('Tag'=>'CarouselDown','Id'=>$id,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>101,'Desc'=>'轮播图下移')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'carousel_del':
		$id = intval(($_POST['id']));
		$param=array(
			'extparam'=>array('Tag'=>'CarouselDel','Id'=>$id,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10318,'ChildId'=>101,'Desc'=>'轮播图删除')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
}

$serviceType='decoration';

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('decoration/'.$template.'.html',$tpl);