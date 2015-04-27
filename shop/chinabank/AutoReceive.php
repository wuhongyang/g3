<?php
require_once '../../library/global.fun.php';

$param = array(
		'extparam' => array('Tag'=>'ChinabankAuto','notOpenAgent'=>$_GET['notOpenAgent']),
		'param'    => array('BigCaseId'=>10005,'CaseId'=>10024,'ParentId'=>10094,'ChildId'=>101,'Type'=>$_GET['type'],'GroupId'=>intval($_GET['GroupId']))
	);
$result = request($param);

echo $result['Result'];