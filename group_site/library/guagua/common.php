<?php
/*瓜瓜网公共模块*/

//站搜索配置
$param=array(
	'extparam'=>array('Tag'=>'GetGroupSearchConfig','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10009,'CaseId'=>10054,'ParentId'=>10321,'ChildId'=>101,'Desc'=>'查询站点搜索配置')
);
$searchConfig=request($param);
$searchConfig=$searchConfig['info'];
?>