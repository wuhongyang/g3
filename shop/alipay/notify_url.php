<?php
require_once '../../library/global.fun.php';

$param = array(
		'extparam' => array('Tag'=>'AlipayNotify','notOpenAgent'=>intval($_GET['notOpenAgent'])),
		'param'    => array('BigCaseId'=>10005,'CaseId'=>10024,'ParentId'=>10064,'ChildId'=>101,'Type'=>$_GET['type'])
	);
$result = request($param);

echo $result['Result'];