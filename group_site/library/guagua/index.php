<?php
/*瓜瓜网首页扩展*/
//列表
$param=array(
	'extparam'=>array('Tag'=>'GetActiveList','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10323,'ChildId'=>101,'Desc'=>'查询站点活动列表')
);
$activeList=request($param);
?>