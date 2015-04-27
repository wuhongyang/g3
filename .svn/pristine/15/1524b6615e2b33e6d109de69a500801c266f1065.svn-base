<?php
set_time_limit(0);
require '../data/library/global.fun.php';
require 'include.inc.php';
$db  = db::connect(config('database','default'));
//$db->query("TRUNCATE TABLE `g3_test`.`test_httpload`");
$sql = "select * from g3_test.test_api where `status`>0 ORDER BY bigcase_id ASC,case_id ASC,parent_id ASC,child_id ASC";
$result = $db->get_results($sql,'ASSOC');
$num = $_GET['reqnum']>0? $_GET['reqnum'] : 1;
$time = $_GET['time']>0? $_GET['time'] : 1;
$paiallel = intval($_GET['paiallel_num']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>http_load压力测试</title>
</head>

<body>
<h3>共 <?php echo count($result); ?> 个接口</h3>
<?php
foreach($result as $key=>$val){
	$rst = array();
	$url = 'http://'.$_SERVER['HTTP_HOST'].'/data/index.php?parameter='.urlencode('{"param":{"BigCaseId":'.$val['bigcase_id'].',"CaseId":'.$val['case_id'].',"ParentId":'.$val['parent_id'].',"ChildId":'.$val['child_id'].',"Uin":"'.$_GET['uin'].'","SessionKey":"'.$_GET['session'].'","ChannelId":"'.$_GET['channelid'].'","TargetUin":"'.$_GET['target'].'","Client":"testurl","DoingWeight":"'.$_GET['doing'].'","MoneyWeight":"'.$_GET['money'].'","Desc":"testurl"},"extparam":'.$val['extparam'].'}');
	exec("{$_SERVER['DOCUMENT_ROOT']}test/ptest/ptest -p {$paiallel} -f {$num} -r 1 -c 1 \"$url\"",$rst);
	$rst = iconv('gbk', 'utf-8',implode('<br>',$rst));
	$sql = "INSERT INTO g3_test.test_ptest (bigcase_id,case_id,parent_id,child_id,result)VALUES
			({$val['bigcase_id']},{$val['case_id']},{$val['parent_id']},{$val['child_id']},'{$rst}')";
	$db->query($sql);
	echo "<p>第{$key}个 =================================>>完成...</p>";
	ob_flush();
	flush();
}
echo '<p>=======================================================================>>全部完成...</p>';
ob_flush();
flush();
?>
</body>
</html>