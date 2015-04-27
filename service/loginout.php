<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$group_id = intval($_GET['group_id']);

$param = array(
	'extparam' => array('Tag'=>'UserLogOut'),
	'param' => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10090,'ChildId'=>102,'GroupId'=>$group_id)
);

$result = request($param);
if( $result['Flag'] == '100' ){
	if(isset($_COOKIE['roomid'])){
		setcookie('roomid','',-1,'/');
	}
	$url = (empty($_GET['back']) || ($_GET['back']== '/') )? '/' : base64_decode($_GET['back']);
	ShowMsg('', $url);
}
