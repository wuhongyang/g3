<?php
require_once '../../library/global.fun.php';
$param = array(
	'extparam' => array('Tag'=>'BaofooReceive','notOpenAgent'=>intval($_GET['notOpenAgent'])),
	'param'    => array('BigCaseId'=>10005,'CaseId'=>10024,'ParentId'=>10320,'ChildId'=>101,'Type'=>$_GET['type'],'GroupId'=>intval($_GET['GroupId']))
);
$result = request($param);
if($result['Flag'] == 100){
	echo 'OK';
}else{
	echo 'fail';
}