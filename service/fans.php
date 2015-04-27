<?php
require_once 'common.php';

$module = $_GET['module'];
$Uin = $user['Uin'];
$Nick = $user['Nick'];
$Uid = $user['Uid'];

switch ($module) {
	default:
	case 'listfans'  :	//粉丝显示页面
		$param = array(
			'extparam' => array('Tag'=>'ListFans','Follow'=>$_GET['follow'],'Uin' => $Uin),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10037,'ChildId'=>103,'Uin' => $Uin)
		);
		$result = request($param);
		$lists = $result['fans'];
		$fansNum = $result['fansNum'];
		$followNum = $result['followNum'];
		$page = $result['page'];
		$user = $result['user'];
		$tem = "fans/wodefensi.html";
		break;
	case 'movefans'   :	//移除粉丝
		$tag = 'MoveFans';
		$mypost = array(
			'id'	=> $_GET['id'],
			'Uin' => $Uin,
		);
		//$result = setParam($tag, $mypost, '10004', '10019', '10037', '106');
		$desc = "移除粉丝";
		$param = array(
				'extparam' => array(
						'Tag'    => ucfirst($tag),
						'Data' => $mypost
				),
		
				'param'    => array(
						'BigCaseId'	=> 10004,
						'CaseId'    	=> 10019,
						'ParentId'  	=> 10037,
						'ChildId'   	=> 106,
						'Uin' 	    	=> $Uin,
						'Desc'		=> $desc,
				)
		);
		$result = request($param);
		if( $result['Flag'] == '100' )
			echo '1';
		else
			echo '0';
		exit;
	case 'listfollow'   :	//关注显示页面		
		$param = array(
			'extparam' => array('Tag'=>'ListFollow','Follow'=>$_GET['follow'],'Uin' => $Uin),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10019,'ParentId'=>10037,'ChildId'=>105,'Uin' => $Uin)
		);
		$result = request($param);
		$lists = $result['fans'];
		$fansNum = $result['fansNum'];
		$followNum = $result['followNum'];
		$page = $result['page'];
		$user = $result['user'];
		//判断是否存在昵称
		if( !empty($user['nick']) )
			$nick = $user['nick'];
		else
			$nick = $user['uin'];
		$tem = 'fans/wodefollow.html';
		break;
	case 'movefollow' :	//取消关注
		$tag = 'MoveFollow';
		$mypost = array(
			'id'	=> $_GET['id'],
			'Uin'   => $Uin,
		);
		//$result = setParam($tag, $mypost, '10004', '10019', '10037', '101');
		$desc = "取消关注";
		$param = array(
				'extparam' => array(
						'Tag'    => ucfirst($tag),
						'mypost' => $mypost
				),
		
				'param'    => array(
						'BigCaseId'	=> 10004,
						'CaseId'    	=> 10019,
						'ParentId'  	=> 10037,
						'ChildId'   	=> 101,
						'Uin' 	    	=> $Uin,
						'Desc'		=> $desc,
				)
		);
		$result = request($param);
		if( $result['Flag'] == '100' )
			ShowMsg('', 'fans.php?module=listfollow&follow='.$_GET['nowuin']);
		else
			ShowMsg('取消失败', 'fans.php?module=listfollow&follow='.$_GET['nowuin']);
		exit;
	case 'addfollow'   :	//添加关注
		$tag = 'AddFollow';
		$mypost = array(
			'id'	=> $_POST['id'],
			'Uin' => $Uin,
		);
		//$result = setParam($tag, $mypost, '10004', '10019', '10037', '102');
		$desc = "添加关注";
		$param = array(
				'extparam' => array(
						'Tag'    => ucfirst($tag),
						'mypost' => $mypost
				),
		
				'param'    => array(
						'BigCaseId'	=> 10004,
						'CaseId'    	=> 10019,
						'ParentId'  	=> 10037,
						'ChildId'   	=> 104,
						'Uin' 	    	=> $Uin,
						'Desc'		=> $desc,
				)
		);
		if( $result['Flag'] == '100')
			echo '1';
		else
			echo '0';
		exit;
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
include template($tem,$tpl);
