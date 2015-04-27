<?php
set_time_limit(0);
include '../data/library/global.fun.php';
$db  = db::connect(config('database','default'));

//更新test_api的一二级科目
$sql = "select * FROM g3_test.test_api group by parent_id";
$result = $db->get_results($sql,'ASSOC');
foreach($result as $val){
	$sql = "select * from kkyoo_ccs.tbl_parent where parent_id={$val['parent_id']}";
	$row = $db->get_row($sql,'ASSOC');
	$sql = "update g3_test.test_api set bigcase_id={$row['bigcase_id']},case_id={$row['case_id']} where parent_id={$val['parent_id']}";
	$db->query($sql);
}

$sql = "SELECT * FROM g3_test.test_api WHERE `status`>0 ORDER BY bigcase_id ASC,case_id ASC,parent_id ASC,child_id ASC";
$result = $db->get_results($sql,'ASSOC');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>接口检测</title>
<style type="text/css">
*{padding:0;margin:0;}
ul li{padding:10px;}
ul li:hover{background:#FFC;}
ul li font{display:inline-block;width:50px;text-align:center;}
ul li a{display:inline-block;width:90%;color:#999;text-decoration:none;white-space:nowrap;overflow:hidden;margin-right:20px;}
ul li a:hover{color:#900;}
.success{font-weight:bold;font-size:16px;color:#090;}
.fail{font-weight:bold;font-size:16px;color:#F00;}
</style>
</head>

<body>
<ul>
<?php
foreach($result as $key=>$val){
	$url = 'http://'.$_SERVER['HTTP_HOST'].'/data/index.php?parameter='.urlencode('{"param":{"BigCaseId":'.$val['bigcase_id'].',"CaseId":'.$val['case_id'].',"ParentId":'.$val['parent_id'].',"ChildId":'.$val['child_id'].',"Uin":"'.$_GET['uin'].'","SessionKey":"'.$_GET['session'].'","ChannelId":"'.$_GET['channelid'].'","TargetUin":"'.$_GET['target'].'","Client":"testurl","DoingWeight":"'.$_GET['doing'].'","MoneyWeight":"'.$_GET['money'].'","Desc":"testurl"},"extparam":'.$val['extparam'].'}');
	$rst = json_decode(socket_request($url),true);
	if($rst['Flag'] == 100){
		echo '<li><font>'.$key.'</font> <a href="'.$url.'" target="_blank">'.urldecode($url).'</a><span class="success">'.$rst['Flag'].' √</span></li>';
	}else{
		echo '<li><font>'.$key.'</font> <a href="'.$url.'" target="_blank">'.urldecode($url).'</a><span class="fail">'.$rst['Flag'].' メ</span></li>';
	}
	ob_flush();
	flush();
}
?>
</ul>
</body>
</html>

