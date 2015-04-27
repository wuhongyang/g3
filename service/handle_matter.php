<?php
require_once 'common.php';

$module = $_GET['module'];

if($module == 'setRead'){
	$id = intval($_POST['id']);

	$param = array(
		'extparam' => array('Tag'=>'SetRead','Id'=>$id),
		'param' => array('BigCaseId'=>10004,'CaseId'=>10035,'ParentId'=>10250,'ChildId'=>102)
	);
	$rst = request($param);
	echo json_encode($rst);
}