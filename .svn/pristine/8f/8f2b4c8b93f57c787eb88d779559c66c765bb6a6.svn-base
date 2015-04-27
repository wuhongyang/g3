<?php
include_once('library/common.php');

$module=empty($_GET['module'])?'index':$_GET['module'];

//站风格
$param=array(
	'extparam'=>array('Tag'=>'GetGroupStyle','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Uin'=>$user['Uin'],'Desc'=>'查询站点当前风格')
);
$styleInfo=request($param);
$styleInfo=$styleInfo['StyleInfo'];

//顶部导航
$param=array(
	'extparam'=>array('Tag'=>'GetGroupNavigate','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Uin'=>$user['Uin'],'Desc'=>'查询站点顶部导航')
);
$navigateList=request($param);
$navigateList=$navigateList['navigateList'];

switch($module){
	case 'index':
		//轮播图
		$param=array(
			'extparam'=>array('Tag'=>'GetActiveAd','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10323,'ChildId'=>101,'Desc'=>'查询站点活动轮播图')
		);
		$activeAdList=request($param);
		$activeAdList=$activeAdList['activeAdList'];
		
		//列表
		$param=array(
			'extparam'=>array('Tag'=>'GetActiveList','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10323,'ChildId'=>101,'Desc'=>'查询站点活动列表')
		);
		$activeList=request($param);
		
		$activeIds='';
		foreach($activeList['activeList'] as $val){
			$activeIds.=$val['id'].',';
		}
		$activeIds=rtrim($activeIds,',');
		
		$template='active';
	break;
	case 'index_json':
		//列表
		$param=array(
			'extparam'=>array('Tag'=>'GetActiveListJson','ActiveIds'=>$_GET['activeIds']),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10323,'ChildId'=>101,'Desc'=>'查询站点活动列表')
		);
		$activeList=request($param);
		$activeList=$activeList['activeList'];
		
		//验证是否参与
		$user=httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
		if(!empty($user['Uin'])){
			foreach($activeList as $key=>$val){
				if($val['status']!=3){
					$players=explode(',',$val['players']);
					if(in_array($user['Uin'],$players)){
						 $activeList[$key]['status']=4;
					}
				}
			}
		}
		
		echo json_encode($activeList);
		exit;
	break;
	case 'info':
		$activeId=intval($_GET['aid']);
		if($activeId<=0){
			header("Location:/");
		}
		//详情
		$param=array(
			'extparam'=>array('Tag'=>'GetActiveInfo','ActiveId'=>$activeId),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10323,'ChildId'=>102,'Desc'=>'查询站点活动详情')
		);
		$activeInfo=request($param);
		if($activeInfo['Flag']!=100){
			header('Location:/404.html');
			exit;
		}
		$activeInfo=$activeInfo['info'];
		$thisUrl=urlencode('/zhan/'.$groupId.'/active/'.$activeId.'.html');
		
		$template='info';
	break;
	case 'info_json':
		$activeId=intval($_GET['aid']);
		//详情
		$param=array(
			'extparam'=>array('Tag'=>'GetActiveInfo','ActiveId'=>$activeId),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10323,'ChildId'=>102,'Desc'=>'查询站点活动详情')
		);
		$activeInfo=request($param);
		$activeInfo=$activeInfo['info'];
		
		//验证是否参与
		if($activeInfo['status']!=3){
			$user=httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
			if(!empty($user['Uin'])){
				$players=explode(',',$activeInfo['players']);
				if(in_array($user['Uin'],$players)){
					 $activeInfo['status']=4;
				}
			}
		}
		echo json_encode($activeInfo);
		exit;
	break;
	case 'join':
		$activeId=intval($_GET['aid']);
		if($activeId<=0){
			echo json_encode(array('Flag'=>105,'FlagString'=>'参与失败，请尝试刷新页面。'));
			exit;
		}
		//参与
		$param=array(
			'extparam'=>array('Tag'=>'JoinActive','ActiveId'=>$activeId),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10323,'ChildId'=>103,'Desc'=>'参与站点活动')
		);
		$res=request($param);
		echo json_encode($res);
		exit;
	break;
	case 'players':
		$activeId=intval($_GET['aid']);
		if($activeId<=0){
			header("Location:/");
		}
		//参与者
		$param=array(
			'extparam'=>array('Tag'=>'GetActivePlayers','ActiveId'=>$activeId),
			'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10323,'ChildId'=>102,'Desc'=>'查询活动参与者')
		);
		$playList=request($param);
		
		$template='players';		
	break;
}

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/';
$tmp_config['cache_dir'].=$themes.'/tpl/';

$back_url=base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']);
$moduleAction='active';
$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template('active/'.$template.'.html',$tpl);