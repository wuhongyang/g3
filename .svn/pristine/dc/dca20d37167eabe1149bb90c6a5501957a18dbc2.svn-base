<?php
include_once('library/common.php');
$module=empty($_GET['module'])?'home':$_GET['module'];
$callback=$groupExtInfo['callback']['value'];
if(empty($callback)){
	$callback="openlogin.vvku.com";
}
if($groupId<=0){
	header("Location:/");
}
//是否意向客户
$is_intention = $_GET['is_intention'];
//是否引导页
$is_guide = $_GET['is_guide'];

//验证是否登陆
$user=httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));

switch($module){
	case 'home':
		//站详情
		$param=array(
		   'extparam'=>array('Tag'=>'GetGroupInfo','Id'=>$groupId),
		   'param'=>array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101,'Desc'=>'获取站信息')
		);
		$groupInfo=request($param);
		if($groupInfo['Flag']!=100){
		   alertMsg($groupInfo['FlagString'],'/');
		}
		$groupInfo=$groupInfo['Result'];
		if(empty($groupInfo)){
		   header('Location:/404.html');
		   exit;
		}
		//站配置
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupSetting','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点配置')
		);
		$setting=request($param);
		$setting=$setting['setting'];
		
		//顶部导航
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupNavigate','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点顶部导航')
		);
		$navigateList=request($param);
		$navigateList=$navigateList['navigateList'];
		
		//载入各站扩展
		$library='library/'.$themes.'/'.basename(__FILE__);
		if(file_exists($library)){
			include_once($library);
		}
		
		//体验页
		if($is_guide && $user['Flag'] == 100){
			//引导页角色列表
			$param = array(
				'param' => array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10642,'ChildId'=>101),
				'extparam' => array('Tag'=>'GetPractice', 'GroupId'=>$groupId)
			);
			$rst = request($param);
			$all_role = $role = $rst['Data'];
			$role_pos = $_GET['role_pos'];
			$role_name = urldecode($_GET['role_name']);
			$account_pos = $_GET['account_pos'];
			foreach($role as $key=>$val){
				if($val['role_name'] == $role_name){
					$current_role = $val;
				}else{
					$rst_role[] = $val;
				}
			}
		}
		$template='index';
	break;
	case 'get_recommend_sub':
		//推荐位
		if($_GET['isArtistDetail']==1){
			$isArtistDetail=true;
		}
		else{
			$isArtistDetail=false;
		}
		$param=array(
			'extparam'=>array('Tag'=>'GetRecommendSub','SubId'=>intval($_GET['sub_id']),'IsArtistDetail'=>$isArtistDetail,'RuleId'=>intval($_GET['artistRankRuleId'])),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'获取推荐位')
		);
		$recommendSub=request($param);
		echo json_encode($recommendSub);
		exit;
	break;
	case 'get_group_room':
		//房间
		$data['groupId']=$groupId;
		if($_GET['keywords']){
			$data['keywords']=$_GET['keywords'];
		}
		if($_GET['cat_id']){
			$data['catId']=intval($_GET['cat_id']);
		}
		if($_GET['limit']){
			$data['limit']=$_GET['limit'];
		}
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupRoomList','Data'=>$data),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'获取站下房间')
		);
		$roomList=request($param);
		$roomList['page']=str_replace('条记录','个房间',$roomList['page']);
		$roomList['roomList']=array_values($roomList['roomList']);
		echo json_encode($roomList);
		exit;
	break;
	case 'get_group_artist':
		//艺人
		$data['groupId']=$groupId;
		if($_GET['keywords']){
			$data['keywords']=$_GET['keywords'];
		}
		if($_GET['limit']){
			$data['limit']=$_GET['limit'];
		}
		if($_GET['isArtistDetail']==1){
			$isArtistDetail=true;
		}
		else{
			$isArtistDetail=false;
		}
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupArtistList','Data'=>$data,'IsArtistDetail'=>$isArtistDetail,'RuleId'=>intval($_GET['artistRankRuleId'])),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'获取站下艺人')
		);
		$artistList=request($param);
		$artistList['page']=str_replace('条记录','个艺人',$artistList['page']);
		echo json_encode($artistList);
		exit;
	break;
}

function str_cut_out($str,$num=7){
	if(mb_strlen($str,'UTF-8')>($num+1)){
		$str=mb_substr($str,0,$num,'UTF-8').'...';
	}
	return $str;
}

$client=empty($_GET['client'])?'tpl':'tpl_'.$_GET['client'];

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/'.$client.'/';
$tmp_config['cache_dir'].=$themes.'/'.$client.'/';

$back_url=base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']);
$moduleAction='index';
$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template('index/'.$template.'.html',$tpl);
pagecache::main()->end();