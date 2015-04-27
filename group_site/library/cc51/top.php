<?php
/*六房间排行榜扩展*//*
foreach($rankList as $key=>$val){
	foreach($val as $key2=>$val2){
		if(in_array($key2,array('week','month','total','last_week'))){
			foreach($val[$key2] as $key3=>$val3){
				$param=array(
					'extparam'=>array('Tag'=>'GetScoreDiff','UinId'=>$val3['UinId'],'ExtendUin'=>$groupId,'Ruleid'=>$val3['Ruleid'],"Period"=>"total"),
					'param'=>array('BigCaseId'=>10001,'CaseId'=>10027,'ParentId'=>10074,'ChildId'=>118)
				);
				$result=request($param);
				if(!empty($result['RolesImg'])){
					$rankList[$key][$key2][$key3]['RolesImg']=$result['RolesImg'];
					$rankList[$key][$key2][$key3]['RolesName']=$result['RolesName'];
				}
			}
		}
	}
}*/
?>