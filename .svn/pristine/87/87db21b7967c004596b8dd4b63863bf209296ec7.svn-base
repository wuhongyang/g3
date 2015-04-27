<?php
$json = $_POST['extparam'];
$param = $_POST['param'];
switch($json['Tag']){
	case 'FeedList':
		$total = json_decode(socket_request(MONGO_API_PATH.'?cmd={"db":"kkyoo_action","table":"g3_feedback","total":[],"where":'.json_encode($json['where']).'}'),true);
		$total = $total['total'];
		$shownum = 20;
		$page  = intval($json['page']);
		$prev = $page - $shownum < 0? 0 : $page - $shownum;
		$next = $page + $shownum > $total? $page : $page + $shownum;
		$lists = json_decode(socket_request(MONGO_API_PATH.'/?cmd={"db":"kkyoo_action","table":"g3_feedback","fields":[],"where":'.json_encode($json['where']).',"option":{"limit":"'.$shownum.'","skip":"'.$page.'","sort":[["uptime","desc"]]}}'),true);
		$data = array('Flag'=>100,'Result'=>$lists['record'],'prev'=>$prev,'next'=>$next);
		echo json_encode($data);
		break;
	case 'View':
		$lists = json_decode(socket_request(MONGO_API_PATH.'/?cmd={"db":"kkyoo_action","table":"g3_feedback","fields":[],"where":{"_id":"'.$json['id'].'"},"option":{"limit":"1"}}'),true);
		if($lists['success'] == 100){
			echo json_encode(array('Flag'=>100,'Result'=>$lists['record'][0]));
		}else{
			echo json_encode(array('Flag'=>101,'FlagString'=>'不存在的反馈'));
		}
		break;
	case 'Dispose':
		$lists = json_decode(socket_request(MONGO_API_PATH.'/?cmd={"db":"kkyoo_action","table":"g3_feedback","fields":[],"where":{"_id":"'.$json['id'].'"},"option":{"limit":"1"}}'),true);
		$record = $lists['record'][0];
		$record['status'] = 1;
		$record['dispose'] = $param['Admin_name'];
		unset($record['_id']);
		$insert = array('db'=>'kkyoo_action','table'=>'g3_feedback','record'=>$record,'where'=>array('_id'=>$json['id']));
		$rst = json_decode(socket_request(MONGO_API_PATH.'/?cmd='.urlencode(json_encode($insert))),true);
		if($rst['success'] == 100){
			echo json_encode(array('Flag'=>100,'FlagString'=>'处理成功'));
		}else{
			echo json_encode(array('Flag'=>101,'FlagString'=>'处理失败'));
		}
		break;
}